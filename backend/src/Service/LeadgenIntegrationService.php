<?php

namespace App\Service;

use App\Entity\Lead;
use App\Entity\LeadEvent;
use App\Entity\LeadSource;
use App\Entity\Client;
use App\Repository\LeadRepository;
use App\Repository\LeadSourceRepository;
use App\Repository\ClientRepository;
use App\ValueObject\LeadStatus;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Uid\Uuid;

class LeadgenIntegrationService
{
    public function __construct(
        private LeadRepository $leadRepository,
        private LeadSourceRepository $leadSourceRepository,
        private ClientRepository $clientRepository,
        private EntityManagerInterface $entityManager
    ) {}

    /**
     * Import leads from leadgen service data
     */
    public function importLeadgenData(array $leadgenData, ?string $clientId = null, ?string $sourceId = null): array
    {
        $results = [
            'imported' => 0,
            'updated' => 0,
            'skipped' => 0,
            'errors' => []
        ];

        // Get or create leadgen source
        $source = $this->getOrCreateLeadgenSource($sourceId);

        // Get client if provided
        $client = null;
        if ($clientId) {
            $client = $this->clientRepository->find($clientId);
        }

        foreach ($leadgenData as $index => $leadData) {
            try {
                $result = $this->processLeadgenLead($leadData, $client, $source);
                $results[$result]++;
            } catch (\Exception $e) {
                $results['errors'][] = "Row " . ($index + 1) . ": " . $e->getMessage();
            }
        }

        $this->entityManager->flush();

        return $results;
    }

    /**
     * Process a single lead from leadgen data
     */
    private function processLeadgenLead(array $leadData, ?Client $client, LeadSource $source): string
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
            $this->updateLeadFromLeadgenData($existingLead, $leadData);
            return 'updated';
        }

        // Create new lead
        $lead = new Lead();
        $this->populateLeadFromLeadgenData($lead, $leadData, $client, $source);
        
        $this->entityManager->persist($lead);
        return 'imported';
    }

    /**
     * Populate lead entity from leadgen data
     */
    private function populateLeadFromLeadgenData(Lead $lead, array $leadData, ?Client $client, LeadSource $source): void
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
        if (isset($leadData['vertical'])) {
            $practiceAreas[] = $this->mapVerticalToPracticeArea($leadData['vertical']);
        }
        if (isset($leadData['tags']) && is_array($leadData['tags'])) {
            $practiceAreas = array_merge($practiceAreas, $leadData['tags']);
        }
        $lead->setPracticeAreas(array_unique($practiceAreas));
        
        // Message from leadgen context
        $message = "Imported from leadgen service";
        if (isset($leadData['vertical'])) {
            $message .= " - Vertical: " . $leadData['vertical'];
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
            'leadgen_data' => $leadData,
            'imported_at' => (new \DateTimeImmutable())->format('c'),
            'lead_score' => $leadData['lead_score'] ?? null,
            'vertical' => $leadData['vertical'] ?? null,
            'rating' => $leadData['reviews']['rating'] ?? null,
            'review_count' => $leadData['reviews']['count'] ?? null
        ];
        $lead->setUtmJson($utmData);
    }

    /**
     * Update existing lead with leadgen data
     */
    private function updateLeadFromLeadgenData(Lead $lead, array $leadData): void
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
    private function getOrCreateLeadgenSource(?string $sourceId): LeadSource
    {
        if ($sourceId) {
            $source = $this->leadSourceRepository->find($sourceId);
            if ($source) {
                return $source;
            }
        }

        // Create default leadgen source
        $source = new LeadSource();
        $source->setName('Leadgen Service');
        $source->setDescription('Automated lead generation service');
        $source->setIsActive(true);
        
        $this->entityManager->persist($source);
        return $source;
    }

    /**
     * Track lead statistics (dials, contacts, interviews, applications)
     */
    public function trackLeadStatistics(string $leadId, string $eventType, array $eventData = []): LeadEvent
    {
        $lead = $this->leadRepository->find($leadId);
        if (!$lead) {
            throw new \Exception('Lead not found');
        }

        $event = new LeadEvent();
        $event->setLead($lead);
        $event->setType($eventType);
        $event->setDirection($eventData['direction'] ?? 'outbound');
        $event->setDuration($eventData['duration'] ?? null);
        $event->setNotes($eventData['notes'] ?? null);
        $event->setOutcome($eventData['outcome'] ?? null);
        $event->setNextAction($eventData['next_action'] ?? null);

        $this->entityManager->persist($event);

        // Update lead status based on event type
        $this->updateLeadStatusFromEvent($lead, $eventType);

        $this->entityManager->flush();

        return $event;
    }

    /**
     * Update lead status based on event type
     */
    private function updateLeadStatusFromEvent(Lead $lead, string $eventType): void
    {
        switch ($eventType) {
            case 'phone_call':
                if ($lead->getStatus() === LeadStatus::NEW_LEAD) {
                    $lead->setStatus(LeadStatus::CONTACTED);
                }
                break;
            case 'meeting':
                if (in_array($lead->getStatus(), [LeadStatus::NEW_LEAD, LeadStatus::CONTACTED])) {
                    $lead->setStatus(LeadStatus::INTERVIEW_SCHEDULED);
                }
                break;
            case 'application':
                $lead->setStatus(LeadStatus::APPLICATION_RECEIVED);
                break;
        }
    }

    /**
     * Get lead statistics
     */
    public function getLeadStatistics(string $leadId): array
    {
        $lead = $this->leadRepository->find($leadId);
        if (!$lead) {
            throw new \Exception('Lead not found');
        }

        $events = $lead->getEvents();
        
        $stats = [
            'total_events' => $events->count(),
            'phone_calls' => 0,
            'emails' => 0,
            'meetings' => 0,
            'applications' => 0,
            'last_contact' => null,
            'total_duration' => 0
        ];

        foreach ($events as $event) {
            switch ($event->getType()) {
                case 'phone_call':
                    $stats['phone_calls']++;
                    if ($event->getDuration()) {
                        $stats['total_duration'] += $event->getDuration();
                    }
                    break;
                case 'email':
                    $stats['emails']++;
                    break;
                case 'meeting':
                    $stats['meetings']++;
                    break;
                case 'application':
                    $stats['applications']++;
                    break;
            }

            if (!$stats['last_contact'] || $event->getCreatedAt() > $stats['last_contact']) {
                $stats['last_contact'] = $event->getCreatedAt();
            }
        }

        return $stats;
    }
}
