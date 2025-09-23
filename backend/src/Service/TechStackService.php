<?php

namespace App\Service;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Psr\Log\LoggerInterface;

class TechStackService
{
    private HttpClientInterface $httpClient;
    private string $apiKey;
    private string $baseUrl = 'https://api.wappalyzer.com/v2/lookup';

    public function __construct(
        private LoggerInterface $logger,
        string $wappalyzerApiKey = ''
    ) {
        $this->httpClient = HttpClient::create();
        $this->apiKey = $wappalyzerApiKey;
    }

    /**
     * Analyze a website's technology stack
     */
    public function analyzeWebsite(string $url): array
    {
        if (empty($this->apiKey)) {
            return [
                'url' => $url,
                'technologies' => [],
                'error' => 'Wappalyzer API key not configured'
            ];
        }

        try {
            // Normalize URL
            $normalizedUrl = $this->normalizeUrl($url);
            
            $response = $this->httpClient->request('GET', $this->baseUrl, [
                'query' => [
                    'urls' => $normalizedUrl
                ],
                'headers' => [
                    'x-api-key' => $this->apiKey,
                    'Content-Type' => 'application/json',
                ],
                'timeout' => 30
            ]);

            if ($response->getStatusCode() !== 200) {
                throw new \Exception('Wappalyzer API error: ' . $response->getStatusCode() . ' ' . $response->getInfo('http_code'));
            }

            $data = $response->toArray();
            
            // Handle the response format from Wappalyzer API
            if (is_array($data) && count($data) > 0) {
                $result = $data[0];
                return [
                    'url' => $normalizedUrl,
                    'technologies' => $result['technologies'] ?? [],
                    'lastAnalyzed' => (new \DateTime())->format('c')
                ];
            }

            return [
                'url' => $normalizedUrl,
                'technologies' => [],
                'lastAnalyzed' => (new \DateTime())->format('c')
            ];

        } catch (\Exception $e) {
            $this->logger->error('Error analyzing website technology stack', [
                'url' => $url,
                'error' => $e->getMessage()
            ]);

            return [
                'url' => $url,
                'technologies' => [],
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Normalize URL to ensure it has a protocol
     */
    private function normalizeUrl(string $url): string
    {
        if (empty($url)) {
            return '';
        }
        
        // Remove any whitespace
        $url = trim($url);
        
        // Add protocol if missing
        if (!str_starts_with($url, 'http://') && !str_starts_with($url, 'https://')) {
            $url = 'https://' . $url;
        }
        
        return $url;
    }

    /**
     * Get technology categories for better organization
     */
    public function getTechnologyCategories(array $technologies): array
    {
        $categories = [];
        
        foreach ($technologies as $tech) {
            foreach ($tech['categories'] ?? [] as $category) {
                if (!isset($categories[$category])) {
                    $categories[$category] = [];
                }
                $categories[$category][] = $tech;
            }
        }
        
        return $categories;
    }

    /**
     * Get a summary of the technology stack
     */
    public function getTechStackSummary(array $technologies): array
    {
        $categories = [];
        foreach ($technologies as $tech) {
            foreach ($tech['categories'] ?? [] as $category) {
                $categories[$category] = true;
            }
        }

        // Sort by confidence and get top 5
        usort($technologies, function($a, $b) {
            return ($b['confidence'] ?? 0) <=> ($a['confidence'] ?? 0);
        });
        
        $topTechnologies = array_slice($technologies, 0, 5);

        return [
            'total' => count($technologies),
            'categories' => array_keys($categories),
            'topTechnologies' => $topTechnologies
        ];
    }

    /**
     * Check if a specific technology is detected
     */
    public function hasTechnology(array $technologies, string $techName): bool
    {
        foreach ($technologies as $tech) {
            if (stripos($tech['name'] ?? '', $techName) !== false) {
                return true;
            }
        }
        return false;
    }

    /**
     * Get technologies by category
     */
    public function getTechnologiesByCategory(array $technologies, string $category): array
    {
        return array_filter($technologies, function($tech) use ($category) {
            foreach ($tech['categories'] ?? [] as $cat) {
                if (stripos($cat, $category) !== false) {
                    return true;
                }
            }
            return false;
        });
    }
}
