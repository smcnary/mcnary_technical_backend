<?php

namespace App\Service;

use App\Entity\Client;
use App\Entity\OAuthConnection;
use App\Entity\OAuthToken;
use App\Repository\ClientRepository;
use App\Repository\OAuthConnectionRepository;
use App\Repository\OAuthTokenRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class GoogleBusinessProfileSyncService
{
    private const GOOGLE_API_BASE = 'https://mybusiness.googleapis.com/v4';
    private const SYNC_INTERVAL_HOURS = 6; // Sync every 6 hours

    public function __construct(
        private ClientRepository $clientRepository,
        private OAuthConnectionRepository $oauthConnectionRepository,
        private OAuthTokenRepository $oauthTokenRepository,
        private EntityManagerInterface $entityManager,
        private HttpClientInterface $httpClient,
        private LoggerInterface $logger
    ) {}

    /**
     * Sync GBP data for all connected clients
     */
    public function syncAllClients(): array
    {
        $results = [];
        
        // Find all clients with GBP connections
        $gbpConnections = $this->oauthConnectionRepository->findBy([
            'provider' => 'google_gbp'
        ]);

        foreach ($gbpConnections as $connection) {
            try {
                $result = $this->syncClientData($connection->getClient());
                $results[$connection->getClient()->getId()] = $result;
            } catch (\Exception $e) {
                $this->logger->error('Failed to sync GBP data for client', [
                    'client_id' => $connection->getClient()->getId(),
                    'error' => $e->getMessage()
                ]);
                
                $results[$connection->getClient()->getId()] = [
                    'success' => false,
                    'error' => $e->getMessage()
                ];
            }
        }

        return $results;
    }

    /**
     * Sync GBP data for a specific client
     */
    public function syncClientData(Client $client): array
    {
        $connection = $this->oauthConnectionRepository->findOneBy([
            'client' => $client,
            'provider' => 'google_gbp'
        ]);

        if (!$connection) {
            throw new \Exception('No GBP connection found for client');
        }

        $token = $this->oauthTokenRepository->findOneBy(['connection' => $connection]);
        if (!$token) {
            throw new \Exception('No OAuth token found for GBP connection');
        }

        // Refresh token if expired
        if ($token->isExpired()) {
            $token = $this->refreshToken($token, $connection);
        }

        try {
            // Fetch insights from Google Business Profile API
            $insights = $this->fetchBusinessInsights($token->getAccessToken(), $client);
            
            // Update client with latest data
            $this->updateClientWithGbpData($client, $insights);
            
            // Update last sync timestamp
            $connection->setMetadata(array_merge(
                $connection->getMetadata() ?? [],
                ['last_sync' => (new \DateTime())->format('c')]
            ));
            
            $this->entityManager->flush();

            return [
                'success' => true,
                'data_points' => count($insights),
                'last_sync' => (new \DateTime())->format('c')
            ];

        } catch (\Exception $e) {
            $this->logger->error('Failed to sync GBP data', [
                'client_id' => $client->getId(),
                'error' => $e->getMessage()
            ]);
            
            throw $e;
        }
    }

    /**
     * Check if client needs sync based on last sync time
     */
    public function needsSync(Client $client): bool
    {
        $connection = $this->oauthConnectionRepository->findOneBy([
            'client' => $client,
            'provider' => 'google_gbp'
        ]);

        if (!$connection) {
            return false;
        }

        $metadata = $connection->getMetadata() ?? [];
        $lastSync = $metadata['last_sync'] ?? null;

        if (!$lastSync) {
            return true;
        }

        $lastSyncTime = new \DateTime($lastSync);
        $hoursSinceSync = (new \DateTime())->diff($lastSyncTime)->h;

        return $hoursSinceSync >= self::SYNC_INTERVAL_HOURS;
    }

    /**
     * Get client by ID
     */
    public function getClientById(string $clientId): ?Client
    {
        return $this->clientRepository->find($clientId);
    }

    /**
     * Fetch business insights from Google Business Profile API
     */
    private function fetchBusinessInsights(string $accessToken, Client $client): array
    {
        $gbpData = $client->getGoogleBusinessProfile();
        $profileId = $gbpData['profileId'] ?? null;

        if (!$profileId) {
            throw new \Exception('No profile ID found for client');
        }

        // Extract account ID from profile ID (format: accounts/{accountId}/locations/{locationId})
        if (!preg_match('/accounts\/([^\/]+)/', $profileId, $matches)) {
            throw new \Exception('Invalid profile ID format');
        }

        $accountId = $matches[1];
        $locationId = $this->extractLocationId($profileId);

        $insights = [];

        // Fetch profile views
        $views = $this->fetchMetric($accessToken, $accountId, $locationId, 'PROFILE_VIEWS');
        if ($views) {
            $insights['views'] = $views;
        }

        // Fetch phone calls
        $calls = $this->fetchMetric($accessToken, $accountId, $locationId, 'PHONE_CALLS');
        if ($calls) {
            $insights['calls'] = $calls;
        }

        // Fetch reviews
        $reviews = $this->fetchReviews($accessToken, $accountId, $locationId);
        if ($reviews) {
            $insights['reviews'] = $reviews;
        }

        // Fetch actions (website clicks, direction requests)
        $actions = $this->fetchActions($accessToken, $accountId, $locationId);
        if ($actions) {
            $insights['actions'] = $actions;
        }

        return $insights;
    }

    /**
     * Fetch a specific metric from Google Business Profile API
     */
    private function fetchMetric(string $accessToken, string $accountId, string $locationId, string $metric): ?array
    {
        $endpoint = self::GOOGLE_API_BASE . "/accounts/{$accountId}/locations/{$locationId}/reportInsights";
        
        $params = [
            'locationNames' => ["accounts/{$accountId}/locations/{$locationId}"],
            'basicRequest' => [
                'metricRequests' => [
                    [
                        'metric' => $metric,
                        'options' => ['AGGREGATED_DAILY']
                    ]
                ],
                'timeRange' => [
                    'startDate' => [
                        'year' => (new \DateTime('-30 days'))->format('Y'),
                        'month' => (new \DateTime('-30 days'))->format('n'),
                        'day' => (new \DateTime('-30 days'))->format('j')
                    ],
                    'endDate' => [
                        'year' => (new \DateTime())->format('Y'),
                        'month' => (new \DateTime())->format('n'),
                        'day' => (new \DateTime())->format('j')
                    ]
                ]
            ]
        ];

        try {
            $response = $this->httpClient->request('POST', $endpoint, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Content-Type' => 'application/json',
                ],
                'json' => $params
            ]);

            $data = $response->toArray();
            return $this->processMetricData($data, $metric);
            
        } catch (\Exception $e) {
            $this->logger->warning('Failed to fetch metric', [
                'metric' => $metric,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Fetch reviews data
     */
    private function fetchReviews(string $accessToken, string $accountId, string $locationId): ?array
    {
        $endpoint = self::GOOGLE_API_BASE . "/accounts/{$accountId}/locations/{$locationId}/reviews";
        
        try {
            $response = $this->httpClient->request('GET', $endpoint, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $accessToken,
                ]
            ]);

            $data = $response->toArray();
            return $this->processReviewsData($data);
            
        } catch (\Exception $e) {
            $this->logger->warning('Failed to fetch reviews', [
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Fetch actions data (website clicks, direction requests)
     */
    private function fetchActions(string $accessToken, string $accountId, string $locationId): ?array
    {
        $websiteClicks = $this->fetchMetric($accessToken, $accountId, $locationId, 'WEBSITE_CLICKS');
        $directionRequests = $this->fetchMetric($accessToken, $accountId, $locationId, 'DIRECTION_REQUESTS');

        if (!$websiteClicks && !$directionRequests) {
            return null;
        }

        return [
            'website_clicks' => $websiteClicks['total'] ?? 0,
            'direction_requests' => $directionRequests['total'] ?? 0,
            'period' => 'last_30_days'
        ];
    }

    /**
     * Process metric data from API response
     */
    private function processMetricData(array $data, string $metric): array
    {
        $total = 0;
        $dailyData = [];

        if (isset($data['locationMetrics'][0]['metricValues'][0]['dimensionalValues'])) {
            foreach ($data['locationMetrics'][0]['metricValues'][0]['dimensionalValues'] as $value) {
                $total += $value['value'] ?? 0;
                $dailyData[] = $value;
            }
        }

        // Calculate change from previous period (mock for now)
        $change = rand(-20, 30); // This would be calculated from historical data

        return [
            'total' => $total,
            'change' => $change,
            'period' => 'last_30_days',
            'daily_data' => $dailyData
        ];
    }

    /**
     * Process reviews data from API response
     */
    private function processReviewsData(array $data): array
    {
        $reviews = $data['reviews'] ?? [];
        $totalReviews = count($reviews);
        
        $totalRating = 0;
        foreach ($reviews as $review) {
            $totalRating += $review['starRating'] ?? 0;
        }
        
        $averageRating = $totalReviews > 0 ? $totalRating / $totalReviews : 0;
        
        // Count new reviews in last 30 days
        $newReviews = 0;
        $thirtyDaysAgo = new \DateTime('-30 days');
        
        foreach ($reviews as $review) {
            if (isset($review['createTime'])) {
                $reviewDate = new \DateTime($review['createTime']);
                if ($reviewDate >= $thirtyDaysAgo) {
                    $newReviews++;
                }
            }
        }

        return [
            'average' => round($averageRating, 1),
            'total' => $totalReviews,
            'change' => $newReviews,
            'period' => 'last_30_days'
        ];
    }

    /**
     * Update client with fetched GBP data
     */
    private function updateClientWithGbpData(Client $client, array $insights): void
    {
        $gbpData = $client->getGoogleBusinessProfile() ?? [];
        
        if (isset($insights['views'])) {
            $gbpData['views'] = $insights['views'];
        }
        
        if (isset($insights['calls'])) {
            $gbpData['calls'] = $insights['calls'];
        }
        
        if (isset($insights['reviews'])) {
            $gbpData['reviews'] = $insights['reviews'];
            $gbpData['rating'] = $insights['reviews']['average'];
            $gbpData['reviewsCount'] = $insights['reviews']['total'];
        }
        
        if (isset($insights['actions'])) {
            $gbpData['actions'] = $insights['actions'];
        }
        
        // Calculate local visibility score (mock calculation)
        $visibilityScore = $this->calculateVisibilityScore($insights);
        $gbpData['localVisibility'] = [
            'score' => $visibilityScore,
            'change' => rand(-10, 15), // Mock change
            'period' => 'last_30_days'
        ];
        
        $gbpData['lastUpdated'] = (new \DateTime())->format('c');
        
        $client->setGoogleBusinessProfile($gbpData);
    }

    /**
     * Calculate local visibility score based on metrics
     */
    private function calculateVisibilityScore(array $insights): int
    {
        $score = 50; // Base score
        
        // Increase score based on views
        if (isset($insights['views']['total'])) {
            $views = $insights['views']['total'];
            if ($views > 1000) $score += 20;
            elseif ($views > 500) $score += 15;
            elseif ($views > 100) $score += 10;
        }
        
        // Increase score based on reviews
        if (isset($insights['reviews']['average'])) {
            $rating = $insights['reviews']['average'];
            if ($rating >= 4.5) $score += 15;
            elseif ($rating >= 4.0) $score += 10;
            elseif ($rating >= 3.5) $score += 5;
        }
        
        // Increase score based on total reviews
        if (isset($insights['reviews']['total'])) {
            $totalReviews = $insights['reviews']['total'];
            if ($totalReviews > 100) $score += 10;
            elseif ($totalReviews > 50) $score += 5;
        }
        
        return min(100, max(0, $score));
    }

    /**
     * Extract location ID from profile ID
     */
    private function extractLocationId(string $profileId): string
    {
        if (preg_match('/locations\/([^\/]+)/', $profileId, $matches)) {
            return $matches[1];
        }
        
        throw new \Exception('Could not extract location ID from profile ID');
    }

    /**
     * Refresh OAuth token
     */
    private function refreshToken(OAuthToken $token, OAuthConnection $connection): OAuthToken
    {
        if (!$token->getRefreshToken()) {
            throw new \Exception('No refresh token available');
        }

        $response = $this->httpClient->request('POST', 'https://oauth2.googleapis.com/token', [
            'headers' => [
                'Content-Type' => 'application/x-www-form-urlencoded',
            ],
            'body' => [
                'client_id' => $_ENV['GOOGLE_CLIENT_ID'],
                'client_secret' => $_ENV['GOOGLE_CLIENT_SECRET'],
                'refresh_token' => $token->getRefreshToken(),
                'grant_type' => 'refresh_token',
            ],
        ]);

        $tokenData = $response->toArray();
        $token->setAccessToken($tokenData['access_token']);

        if (isset($tokenData['expires_in'])) {
            $expiresAt = new \DateTime();
            $expiresAt->add(new \DateInterval('PT' . $tokenData['expires_in'] . 'S'));
            $token->setExpiresAt($expiresAt);
        }

        $this->entityManager->flush();

        return $token;
    }
}
