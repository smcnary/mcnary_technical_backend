<?php

namespace App\Service;

use App\Entity\Lead;
use App\Entity\LeadSource;
use App\ValueObject\LeadStatus;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class GoogleSheetsService
{
    private const GOOGLE_SHEETS_API_BASE = 'https://sheets.googleapis.com/v4/spreadsheets';
    private const GOOGLE_OAUTH_TOKEN_URL = 'https://oauth2.googleapis.com/token';

    public function __construct(
        private HttpClientInterface $httpClient,
        private EntityManagerInterface $entityManager,
        private LoggerInterface $logger
    ) {}

    /**
     * Fetch data from a Google Sheet using public CSV export
     */
    public function fetchSheetData(string $spreadsheetId, string $range = 'A:Z'): array
    {
        // Use public CSV export URL - no authentication required
        $csvUrl = "https://docs.google.com/spreadsheets/d/{$spreadsheetId}/export?format=csv&gid=0";
        
        try {
            $response = $this->httpClient->request('GET', $csvUrl);
            $csvContent = $response->getContent();
            
            if (empty($csvContent)) {
                return [];
            }

            // Parse CSV content
            $lines = explode("\n", trim($csvContent));
            $data = [];
            
            foreach ($lines as $line) {
                if (empty(trim($line))) {
                    continue;
                }
                
                // Parse CSV line (handle quoted fields)
                $row = str_getcsv($line);
                $data[] = $row;
            }

            return $data;

        } catch (\Exception $e) {
            $this->logger->error('Failed to fetch Google Sheets data', [
                'spreadsheet_id' => $spreadsheetId,
                'range' => $range,
                'error' => $e->getMessage()
            ]);
            
            throw new \Exception('Failed to fetch Google Sheets data: ' . $e->getMessage());
        }
    }

    /**
     * Import leads from Google Sheets data
     */
    public function importLeadsFromSheet(array $sheetData, ?string $clientId = null, ?string $sourceId = null, bool $overwriteExisting = false): array
    {
        if (empty($sheetData)) {
            return [
                'imported' => 0,
                'updated' => 0,
                'skipped' => 0,
                'errors' => ['No data found in sheet']
            ];
        }

        // First row should be headers
        $headers = array_shift($sheetData);
        $headers = array_map('strtolower', array_map('trim', $headers));

        // Map headers to Lead entity fields
        $fieldMapping = $this->getFieldMapping();
        
        $imported = 0;
        $updated = 0;
        $skipped = 0;
        $errors = [];

        // Get or create lead source
        $source = null;
        if ($sourceId) {
            $source = $this->entityManager->getRepository(LeadSource::class)->find($sourceId);
        }
        
        if (!$source) {
            $source = $this->entityManager->getRepository(LeadSource::class)->findOneBy(['name' => 'Google Sheets Import']);
            if (!$source) {
                $source = new LeadSource('Google Sheets Import');
                $source->setDescription('Leads imported from Google Sheets');
                $source->setStatus('active');
                $this->entityManager->persist($source);
            }
        }

        foreach ($sheetData as $index => $row) {
            try {
                // Skip empty rows
                if (empty(array_filter($row))) {
                    continue;
                }

                // Combine headers with row data
                $rowData = array_combine($headers, array_pad($row, count($headers), ''));
                
                // Map and validate the data
                $leadData = $this->mapRowToLeadData($rowData, $fieldMapping);
                
                if (!$leadData) {
                    $errors[] = "Row " . ($index + 2) . ": Missing required fields (name and email)";
                    continue;
                }

                // Check if lead already exists
                $existingLead = $this->entityManager->getRepository(Lead::class)->findOneBy(['email' => $leadData['email']]);
                
                if ($existingLead && !$overwriteExisting) {
                    $skipped++;
                    continue;
                }

                // Create or update lead
                if ($existingLead && $overwriteExisting) {
                    $lead = $existingLead;
                    $updated++;
                } else {
                    $lead = new Lead();
                    $imported++;
                }

                // Set lead data
                $lead->setFullName($leadData['full_name']);
                $lead->setEmail($leadData['email']);
                $lead->setSource($source);
                $lead->setStatus(LeadStatus::NEW_LEAD);

                if (!empty($leadData['phone'])) {
                    $lead->setPhone($leadData['phone']);
                }

                if (!empty($leadData['firm'])) {
                    $lead->setFirm($leadData['firm']);
                }

                if (!empty($leadData['website'])) {
                    $lead->setWebsite($leadData['website']);
                }

                if (!empty($leadData['city'])) {
                    $lead->setCity($leadData['city']);
                }

                if (!empty($leadData['state'])) {
                    $lead->setState($leadData['state']);
                }

                if (!empty($leadData['zip_code'])) {
                    $lead->setZipCode($leadData['zip_code']);
                }

                if (!empty($leadData['message'])) {
                    $lead->setMessage($leadData['message']);
                }

                if (!empty($leadData['practice_areas'])) {
                    $lead->setPracticeAreas($leadData['practice_areas']);
                }

                // Store original sheet data in UTM JSON for reference
                $utmData = [
                    'source' => 'google_sheets',
                    'imported_at' => (new \DateTimeImmutable())->format('c'),
                    'original_data' => $rowData
                ];
                $lead->setUtmJson($utmData);

                if (!$existingLead) {
                    $this->entityManager->persist($lead);
                }

            } catch (\Exception $e) {
                $errors[] = "Row " . ($index + 2) . ": " . $e->getMessage();
            }
        }

        if ($imported > 0 || $updated > 0) {
            $this->entityManager->flush();
        }

        return [
            'imported' => $imported,
            'updated' => $updated,
            'skipped' => $skipped,
            'errors' => $errors
        ];
    }

    /**
     * Get field mapping for Google Sheets columns to Lead entity fields
     */
    private function getFieldMapping(): array
    {
        return [
            'name' => 'full_name',
            'full_name' => 'full_name',
            'fullname' => 'full_name',
            'contact_name' => 'full_name',
            'email' => 'email',
            'email_address' => 'email',
            'phone' => 'phone',
            'phone_number' => 'phone',
            'telephone' => 'phone',
            'firm' => 'firm',
            'company' => 'firm',
            'business' => 'firm',
            'law_firm' => 'firm',
            'website' => 'website',
            'url' => 'website',
            'web_site' => 'website',
            'city' => 'city',
            'state' => 'state',
            'zip' => 'zip_code',
            'zip_code' => 'zip_code',
            'postal_code' => 'zip_code',
            'message' => 'message',
            'notes' => 'message',
            'comments' => 'message',
            'practice_areas' => 'practice_areas',
            'practice_area' => 'practice_areas',
            'services' => 'practice_areas',
            'specialties' => 'practice_areas'
        ];
    }

    /**
     * Map row data to Lead entity fields
     */
    private function mapRowToLeadData(array $rowData, array $fieldMapping): ?array
    {
        $leadData = [];

        // Find required fields
        $fullName = null;
        $email = null;

        foreach ($rowData as $key => $value) {
            $key = strtolower(trim($key));
            $value = trim($value);

            if (empty($value)) {
                continue;
            }

            if (isset($fieldMapping[$key])) {
                $field = $fieldMapping[$key];
                
                if ($field === 'full_name') {
                    $fullName = $value;
                } elseif ($field === 'email') {
                    $email = $value;
                } elseif ($field === 'practice_areas') {
                    // Handle practice areas (comma-separated or array)
                    if (str_contains($value, ',')) {
                        $leadData[$field] = array_map('trim', explode(',', $value));
                    } else {
                        $leadData[$field] = [$value];
                    }
                } else {
                    $leadData[$field] = $value;
                }
            }
        }

        // Validate required fields
        if (empty($fullName) || empty($email)) {
            return null;
        }

        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return null;
        }

        $leadData['full_name'] = $fullName;
        $leadData['email'] = $email;

        return $leadData;
    }

    /**
     * Get OAuth access token using refresh token (kept for backward compatibility)
     */
    public function getAccessToken(string $refreshToken): string
    {
        $response = $this->httpClient->request('POST', self::GOOGLE_OAUTH_TOKEN_URL, [
            'headers' => [
                'Content-Type' => 'application/x-www-form-urlencoded',
            ],
            'body' => [
                'client_id' => $_ENV['GOOGLE_OAUTH_CLIENT_ID'],
                'client_secret' => $_ENV['GOOGLE_OAUTH_CLIENT_SECRET'],
                'refresh_token' => $refreshToken,
                'grant_type' => 'refresh_token',
            ],
        ]);

        $tokenData = $response->toArray();
        
        if (!isset($tokenData['access_token'])) {
            throw new \Exception('Failed to obtain access token from Google');
        }

        return $tokenData['access_token'];
    }

    /**
     * Validate Google Sheets URL and extract spreadsheet ID
     */
    public function extractSpreadsheetId(string $url): string
    {
        // Handle different Google Sheets URL formats
        $patterns = [
            '/\/spreadsheets\/d\/([a-zA-Z0-9-_]+)/',
            '/\/d\/([a-zA-Z0-9-_]+)/',
            '/^([a-zA-Z0-9-_]+)$/' // Direct spreadsheet ID
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $url, $matches)) {
                return $matches[1];
            }
        }

        throw new \Exception('Invalid Google Sheets URL format');
    }
}

