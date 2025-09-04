<?php

namespace App\Service;

use App\Repository\ClientRepository;

class AuditIntakeValidationService
{
    public function __construct(
        private ClientRepository $clientRepository
    ) {}

    /**
     * Check if an email is already associated with an existing client
     */
    public function checkEmailExists(string $email): ?array
    {
        $client = $this->clientRepository->findByEmail($email);
        
        if (!$client) {
            return null;
        }

        return [
            'exists' => true,
            'client_id' => $client->getId(),
            'client_name' => $client->getName(),
            'client_slug' => $client->getSlug(),
            'message' => "Email '{$email}' is already associated with client '{$client->getName()}'"
        ];
    }

    /**
     * Check if a website URL domain matches an existing client slug
     */
    public function checkWebsiteExists(string $websiteUrl): ?array
    {
        $domain = $this->extractDomain($websiteUrl);
        
        if (!$domain) {
            return null;
        }

        $client = $this->clientRepository->findBySlug($domain);
        
        if (!$client) {
            return null;
        }

        return [
            'exists' => true,
            'client_id' => $client->getId(),
            'client_name' => $client->getName(),
            'client_slug' => $client->getSlug(),
            'domain' => $domain,
            'message' => "Website '{$websiteUrl}' appears to be associated with existing client '{$client->getName()}'"
        ];
    }

    /**
     * Comprehensive check for both email and website
     */
    public function validateAuditIntakeData(?string $email, ?string $websiteUrl): array
    {
        $results = [
            'email_check' => null,
            'website_check' => null,
            'has_conflicts' => false,
            'conflicts' => []
        ];

        if ($email) {
            $emailResult = $this->checkEmailExists($email);
            $results['email_check'] = $emailResult;
            
            if ($emailResult) {
                $results['has_conflicts'] = true;
                $results['conflicts'][] = $emailResult;
            }
        }

        if ($websiteUrl) {
            $websiteResult = $this->checkWebsiteExists($websiteUrl);
            $results['website_check'] = $websiteResult;
            
            if ($websiteResult) {
                $results['has_conflicts'] = true;
                $results['conflicts'][] = $websiteResult;
            }
        }

        return $results;
    }

    /**
     * Extract domain from URL and convert to slug format
     */
    public function extractDomain(string $url): ?string
    {
        $parsedUrl = parse_url($url);
        if (!$parsedUrl || !isset($parsedUrl['host'])) {
            return null;
        }

        $host = $parsedUrl['host'];
        
        // Remove www. prefix if present
        if (str_starts_with($host, 'www.')) {
            $host = substr($host, 4);
        }

        // Convert to slug format (lowercase, replace dots with hyphens)
        $slug = strtolower(str_replace('.', '-', $host));
        
        return $slug;
    }
}
