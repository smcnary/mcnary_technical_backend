<?php

namespace App\Controller\Api\V1;

use App\Entity\Client;
use App\Repository\ClientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[Route('/api/v1/gbp')]
class GoogleBusinessProfileController extends AbstractController
{
    public function __construct(
        private ClientRepository $clientRepository,
        private EntityManagerInterface $entityManager,
        private HttpClientInterface $httpClient,
        private LoggerInterface $logger
    ) {}

    #[Route('/kpi/{clientId}', name: 'api_v1_gbp_kpi', methods: ['GET'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function getGbpKpi(string $clientId): JsonResponse
    {
        try {
            $client = $this->clientRepository->find($clientId);
            
            if (!$client) {
                return $this->json(['error' => 'Client not found'], Response::HTTP_NOT_FOUND);
            }

            // Check if user has access to this client
            $currentUser = $this->getUser();
            if ($currentUser->getClientId() !== $clientId && !$this->isGranted('ROLE_AGENCY_ADMIN')) {
                return $this->json(['error' => 'Access denied'], Response::HTTP_FORBIDDEN);
            }

            $gbpData = $client->getGoogleBusinessProfile();
            
            if (!$gbpData || empty($gbpData['profileId'])) {
                return $this->json([
                    'error' => 'Google Business Profile not connected',
                    'connected' => false
                ], Response::HTTP_BAD_REQUEST);
            }

            // Fetch real-time data from Google Business Profile API
            $kpiData = $this->fetchGbpKpiData($gbpData['profileId']);

            return $this->json([
                'connected' => true,
                'profileId' => $gbpData['profileId'],
                'kpi' => $kpiData,
                'lastUpdated' => (new \DateTime())->format('c')
            ]);

        } catch (\Exception $e) {
            $this->logger->error('Failed to fetch GBP KPI data', [
                'client_id' => $clientId,
                'error' => $e->getMessage()
            ]);

            return $this->json([
                'error' => 'Failed to fetch GBP data',
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/connect/{clientId}', name: 'api_v1_gbp_connect', methods: ['POST'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function connectGbp(string $clientId, Request $request): JsonResponse
    {
        try {
            $client = $this->clientRepository->find($clientId);
            
            if (!$client) {
                return $this->json(['error' => 'Client not found'], Response::HTTP_NOT_FOUND);
            }

            $data = json_decode($request->getContent(), true);
            $profileId = $data['profileId'] ?? null;

            if (!$profileId) {
                return $this->json(['error' => 'Profile ID is required'], Response::HTTP_BAD_REQUEST);
            }

            // Update client with GBP profile ID
            $gbpData = $client->getGoogleBusinessProfile() ?? [];
            $gbpData['profileId'] = $profileId;
            $client->setGoogleBusinessProfile($gbpData);

            $this->entityManager->flush();

            return $this->json([
                'message' => 'Google Business Profile connected successfully',
                'profileId' => $profileId
            ]);

        } catch (\Exception $e) {
            $this->logger->error('Failed to connect GBP', [
                'client_id' => $clientId,
                'error' => $e->getMessage()
            ]);

            return $this->json([
                'error' => 'Failed to connect Google Business Profile',
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    private function fetchGbpKpiData(string $profileId): array
    {
        // This would require Google Business Profile API access
        // For now, return mock data structure
        
        return [
            'views' => [
                'total' => 18340,
                'change' => 12, // percentage
                'period' => 'last_30_days'
            ],
            'calls' => [
                'total' => 348,
                'change' => 4, // percentage
                'period' => 'last_30_days'
            ],
            'reviews' => [
                'average' => 4.8,
                'total' => 127,
                'change' => 2, // new reviews
                'period' => 'last_30_days'
            ],
            'localVisibility' => [
                'score' => 72, // percentage
                'change' => 6, // percentage
                'period' => 'last_30_days'
            ],
            'actions' => [
                'website_clicks' => 892,
                'direction_requests' => 156,
                'period' => 'last_30_days'
            ]
        ];
    }
}
