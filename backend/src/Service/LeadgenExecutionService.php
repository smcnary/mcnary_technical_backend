<?php

namespace App\Service;

use App\Entity\Lead;
use App\Entity\LeadSource;
use App\Entity\Client;
use App\Repository\LeadRepository;
use App\Repository\LeadSourceRepository;
use App\Repository\ClientRepository;
use App\ValueObject\LeadStatus;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Uid\Uuid;
use Psr\Log\LoggerInterface;

class LeadgenExecutionService
{
    private const LEADGEN_API_BASE_URL = 'http://leadgen:3000'; // Adjust based on your setup
    
    public function __construct(
        private LeadRepository $leadRepository,
        private LeadSourceRepository $leadSourceRepository,
        private ClientRepository $clientRepository,
        private EntityManagerInterface $entityManager,
        private LoggerInterface $logger
    ) {}

    /**
     * Execute a leadgen campaign
     */
    public function executeCampaign(array $config): array
    {
        try {
            $this->logger->info('Starting leadgen campaign', ['config' => $config]);
            
            // Validate configuration
            $this->validateCampaignConfig($config);
            
            // Prepare leadgen request
            $leadgenRequest = $this->prepareLeadgenRequest($config);
            
            // Execute leadgen service
            $leadgenResponse = $this->callLeadgenService($leadgenRequest);
            
            // Process results
            $results = $this->processLeadgenResults($leadgenResponse, $config);
            
            $this->logger->info('Leadgen campaign completed', [
                'campaign_id' => $config['campaign_id'] ?? 'unknown',
                'leads_generated' => $results['leads_generated'],
                'leads_imported' => $results['leads_imported']
            ]);
            
            return $results;
            
        } catch (\Exception $e) {
            $this->logger->error('Leadgen campaign failed', [
                'error' => $e->getMessage(),
                'config' => $config
            ]);
            throw $e;
        }
    }

    /**
     * Validate campaign configuration
     */
    private function validateCampaignConfig(array $config): void
    {
        $requiredFields = ['name', 'vertical', 'geo'];
        foreach ($requiredFields as $field) {
            if (!isset($config[$field]) || empty($config[$field])) {
                throw new \InvalidArgumentException("Missing required field: {$field}");
            }
        }

        // Validate geo configuration
        if (!isset($config['geo']['city']) || !isset($config['geo']['region'])) {
            throw new \InvalidArgumentException('Geo configuration must include city and region');
        }

        // Validate budget
        if (isset($config['budget']['max_cost_usd']) && $config['budget']['max_cost_usd'] <= 0) {
            throw new \InvalidArgumentException('Budget must be greater than 0');
        }
    }

    /**
     * Prepare request for leadgen service
     */
    private function prepareLeadgenRequest(array $config): array
    {
        $request = [
            'name' => $config['name'],
            'vertical' => $config['vertical'],
            'geo' => [
                'city' => $config['geo']['city'],
                'region' => $config['geo']['region'],
                'country' => $config['geo']['country'] ?? 'US',
                'radius_km' => $config['geo']['radius_km'] ?? 30
            ],
            'filters' => [
                'min_rating' => $config['filters']['min_rating'] ?? 3.0,
                'keywords' => $config['filters']['keywords'] ?? []
            ],
            'sources' => $config['sources'] ?? ['google_places'],
            'enrichment' => $config['enrichment'] ?? [],
            'budget' => [
                'max_cost_usd' => $config['budget']['max_cost_usd'] ?? 25
            ],
            'schedule' => [
                'enabled' => $config['schedule']['enabled'] ?? false
            ]
        ];

        // Add optional fields
        if (isset($config['filters']['max_results'])) {
            $request['filters']['max_results'] = $config['filters']['max_results'];
        }

        if (isset($config['filters']['exclude_keywords'])) {
            $request['filters']['exclude_keywords'] = $config['filters']['exclude_keywords'];
        }

        return $request;
    }

    /**
     * Call the leadgen service
     */
    private function callLeadgenService(array $request): array
    {
        $client = HttpClient::create();
        
        try {
            $response = $client->request('POST', self::LEADGEN_API_BASE_URL . '/api/campaigns', [
                'json' => $request,
                'timeout' => 300 // 5 minutes timeout
            ]);

            if ($response->getStatusCode() !== Response::HTTP_OK) {
                throw new \RuntimeException('Leadgen service returned error: ' . $response->getContent(false));
            }

            return $response->toArray();
            
        } catch (\Exception $e) {
            throw new \RuntimeException('Failed to call leadgen service: ' . $e->getMessage());
        }
    }

    /**
     * Process leadgen results and import leads
     */
    private function processLeadgenResults(array $leadgenResponse, array $config): array
    {
        $results = [
            'campaign_id' => $leadgenResponse['campaign_id'] ?? null,
            'leads_generated' => 0,
            'leads_imported' => 0,
            'leads_updated' => 0,
            'leads_skipped' => 0,
            'errors' => [],
            'execution_time' => $leadgenResponse['execution_time'] ?? null,
            'cost' => $leadgenResponse['cost'] ?? 0
        ];

        if (!isset($leadgenResponse['leads']) || !is_array($leadgenResponse['leads'])) {
            $results['errors'][] = 'No leads returned from leadgen service';
            return $results;
        }

        $results['leads_generated'] = count($leadgenResponse['leads']);

        // Get or create lead source
        $source = $this->getOrCreateLeadgenSource($config['name']);

        // Get client if specified
        $client = null;
        if (isset($config['client_id'])) {
            $client = $this->clientRepository->find($config['client_id']);
        }

        // Import each lead
        foreach ($leadgenResponse['leads'] as $leadData) {
            try {
                $result = $this->importLeadgenLead($leadData, $client, $source, $config);
                $results[$result]++;
            } catch (\Exception $e) {
                $results['errors'][] = "Failed to import lead: " . $e->getMessage();
            }
        }

        $this->entityManager->flush();

        return $results;
    }

    /**
     * Import a single lead from leadgen data
     */
    private function importLeadgenLead(array $leadData, ?Client $client, LeadSource $source, array $config): string
    {
        // Extract primary contact information
        $primaryEmail = $this->extractPrimaryEmail($leadData);
        $primaryPhone = $this->extractPrimaryPhone($leadData);

        if (!$primaryEmail) {
            throw new \Exception('No valid email found');
        }

        // Check if lead already exists
        $existingLead = $this->leadRepository->findOneBy(['email' => $primaryEmail]);
        
        if ($existingLead) {
            // Update existing lead with additional information
            $this->updateLeadFromLeadgenData($existingLead, $leadData, $config);
            return 'leads_updated';
        }

        // Create new lead
        $lead = new Lead();
        $this->populateLeadFromLeadgenData($lead, $leadData, $client, $source, $config);
        
        $this->entityManager->persist($lead);
        return 'leads_imported';
    }

    /**
     * Populate lead entity from leadgen data
     */
    private function populateLeadFromLeadgenData(Lead $lead, array $leadData, ?Client $client, LeadSource $source, array $config): void
    {
        // Basic information
        $lead->setFullName($leadData['legal_entity']['name'] ?? 'Unknown');
        $lead->setEmail($this->extractPrimaryEmail($leadData));
        $lead->setPhone($this->extractPrimaryPhone($leadData));
        
        // Firm information
        $lead->setFirm($leadData['legal_entity']['name'] ?? null);
        $lead->setWebsite($leadData['website'] ?? null);
        
        // Address information
        if (isset($leadData['address'])) {
            $lead->setCity($leadData['address']['city'] ?? null);
            $lead->setState($leadData['address']['region'] ?? null);
            $lead->setZipCode($leadData['address']['postal'] ?? null);
        }
        
        // Practice areas from vertical and tags
        $practiceAreas = [];
        if (isset($config['vertical'])) {
            $practiceAreas[] = $this->mapVerticalToPracticeArea($config['vertical']);
        }
        if (isset($leadData['tags']) && is_array($leadData['tags'])) {
            $practiceAreas = array_merge($practiceAreas, $leadData['tags']);
        }
        $lead->setPracticeAreas(array_unique($practiceAreas));
        
        // Message from campaign context
        $message = "Generated from leadgen campaign: " . $config['name'];
        if (isset($config['vertical'])) {
            $message .= " - Vertical: " . $config['vertical'];
        }
        if (isset($leadData['lead_score'])) {
            $message .= " - Lead Score: " . $leadData['lead_score'];
        }
        $lead->setMessage($message);
        
        // Set relationships
        $lead->setClient($client);
        $lead->setSource($source);
        
        // Set status
        $lead->setStatus(LeadStatus::NEW_LEAD);
        
        // Store additional leadgen data in UTM JSON
        $utmData = [
            'leadgen_campaign' => $config,
            'leadgen_data' => $leadData,
            'generated_at' => (new \DateTimeImmutable())->format('c'),
            'lead_score' => $leadData['lead_score'] ?? null,
            'vertical' => $config['vertical'] ?? null,
            'rating' => $leadData['reviews']['rating'] ?? null,
            'review_count' => $leadData['reviews']['count'] ?? null
        ];
        $lead->setUtmJson($utmData);
    }

    /**
     * Update existing lead with leadgen data
     */
    private function updateLeadFromLeadgenData(Lead $lead, array $leadData, array $config): void
    {
        // Only update if we have new information
        if (!$lead->getPhone() && $this->extractPrimaryPhone($leadData)) {
            $lead->setPhone($this->extractPrimaryPhone($leadData));
        }
        
        if (!$lead->getWebsite() && isset($leadData['website'])) {
            $lead->setWebsite($leadData['website']);
        }
        
        // Update UTM data with leadgen information
        $utmData = $lead->getUtmJson();
        $utmData['leadgen_campaign'] = $config;
        $utmData['leadgen_data'] = $leadData;
        $utmData['updated_at'] = (new \DateTimeImmutable())->format('c');
        $lead->setUtmJson($utmData);
    }

    /**
     * Extract primary email from leadgen data
     */
    private function extractPrimaryEmail(array $leadData): ?string
    {
        if (isset($leadData['emails']) && is_array($leadData['emails']) && !empty($leadData['emails'])) {
            // Prefer personal emails over generic/role emails
            foreach ($leadData['emails'] as $email) {
                if (isset($email['type']) && $email['type'] === 'personal') {
                    return $email['value'] ?? null;
                }
            }
            // Fall back to first email
            return $leadData['emails'][0]['value'] ?? null;
        }
        return null;
    }

    /**
     * Extract primary phone from leadgen data
     */
    private function extractPrimaryPhone(array $leadData): ?string
    {
        if (isset($leadData['phones']) && is_array($leadData['phones']) && !empty($leadData['phones'])) {
            // Prefer main phone over mobile/fax
            foreach ($leadData['phones'] as $phone) {
                if (isset($phone['type']) && $phone['type'] === 'main') {
                    return $phone['value'] ?? null;
                }
            }
            // Fall back to first phone
            return $leadData['phones'][0]['value'] ?? null;
        }
        return null;
    }

    /**
     * Map leadgen vertical to practice area
     */
    private function mapVerticalToPracticeArea(string $vertical): string
    {
        $mapping = [
            'local_services' => 'Local Services',
            'b2b_saas' => 'B2B SaaS',
            'ecommerce' => 'E-commerce',
            'healthcare' => 'Healthcare',
            'real_estate' => 'Real Estate',
            'other' => 'Other'
        ];
        
        return $mapping[$vertical] ?? ucfirst(str_replace('_', ' ', $vertical));
    }

    /**
     * Get or create leadgen source
     */
    private function getOrCreateLeadgenSource(string $campaignName): LeadSource
    {
        $sourceName = "Leadgen: " . $campaignName;
        $source = $this->leadSourceRepository->findOneBy(['name' => $sourceName]);
        
        if (!$source) {
            $source = new LeadSource($sourceName);
            $source->setDescription('Automated lead generation campaign');
            $source->setStatus('active');
            
            $this->entityManager->persist($source);
        }
        
        return $source;
    }

    /**
     * Get available verticals
     */
    public function getAvailableVerticals(): array
    {
        return [
            'local_services' => 'Local Services',
            'b2b_saas' => 'B2B SaaS',
            'ecommerce' => 'E-commerce',
            'healthcare' => 'Healthcare',
            'real_estate' => 'Real Estate',
            'other' => 'Other'
        ];
    }

    /**
     * Get available sources
     */
    public function getAvailableSources(): array
    {
        return [
            'google_places' => 'Google Places',
            'yelp' => 'Yelp',
            'facebook' => 'Facebook',
            'linkedin' => 'LinkedIn'
        ];
    }

    /**
     * Get campaign status
     */
    public function getCampaignStatus(string $campaignId): array
    {
        $client = HttpClient::create();
        
        try {
            $response = $client->request('GET', self::LEADGEN_API_BASE_URL . "/api/campaigns/{$campaignId}");
            
            if ($response->getStatusCode() !== Response::HTTP_OK) {
                throw new \RuntimeException('Failed to get campaign status');
            }
            
            return $response->toArray();
            
        } catch (\Exception $e) {
            throw new \RuntimeException('Failed to get campaign status: ' . $e->getMessage());
        }
    }
}
