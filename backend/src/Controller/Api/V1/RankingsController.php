<?php

namespace App\Controller\Api\V1;

use App\Entity\KeywordRanking;
use App\Repository\KeywordRankingRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Uid\Uuid;

#[Route('/api/v1/rankings')]
class RankingsController extends AbstractController
{
    public function __construct(
        private KeywordRankingRepository $rankingRepository
    ) {}

    #[Route('', name: 'api_v1_rankings_list', methods: ['GET'])]
    #[IsGranted('ROLE_AGENCY_ADMIN')]
    public function listRankings(Request $request): JsonResponse
    {
        $page = max(1, (int) $request->query->get('page', 1));
        $perPage = min(100, max(1, (int) $request->query->get('per_page', 20)));
        $sort = $request->query->get('sort', 'date');
        $keywordId = $request->query->get('keyword_id', '');
        $from = $request->query->get('from', '');
        $to = $request->query->get('to', '');
        $clientId = $request->query->get('client_id', '');

        // Parse sort parameter
        $sortFields = [];
        foreach (explode(',', $sort) as $field) {
            $direction = 'ASC';
            if (str_starts_with($field, '-')) {
                $direction = 'DESC';
                $field = substr($field, 1);
            }
            $sortFields[$field] = $direction;
        }

        // Build criteria
        $criteria = [];
        if ($keywordId) {
            $criteria['keyword_id'] = $keywordId;
        }
        if ($from) {
            $criteria['from'] = $from;
        }
        if ($to) {
            $criteria['to'] = $to;
        }
        if ($clientId) {
            $criteria['client_id'] = $clientId;
        }

        // Get rankings with pagination and filtering
        $rankings = $this->rankingRepository->findByCriteria($criteria, $sortFields, $perPage, ($page - 1) * $perPage);
        $totalRankings = $this->rankingRepository->countByCriteria($criteria);

        $rankingData = [];
        foreach ($rankings as $ranking) {
            $rankingData[] = [
                'id' => $ranking->getId(),
                'keyword_id' => $ranking->getKeywordId(),
                'keyword' => $ranking->getKeyword(),
                'position' => $ranking->getPosition(),
                'previous_position' => $ranking->getPreviousPosition(),
                'change' => $ranking->getChange(),
                'search_volume' => $ranking->getSearchVolume(),
                'ctr' => $ranking->getCtr(),
                'date' => $ranking->getDate()->format('Y-m-d'),
                'created_at' => $ranking->getCreatedAt()->format('c')
            ];
        }

        return $this->json([
            'data' => $rankingData,
            'pagination' => [
                'page' => $page,
                'per_page' => $perPage,
                'total' => $totalRankings,
                'pages' => ceil($totalRankings / $perPage)
            ]
        ]);
    }

    #[Route('/summary', name: 'api_v1_rankings_summary', methods: ['GET'])]
    #[IsGranted('ROLE_AGENCY_ADMIN')]
    public function getRankingsSummary(Request $request): JsonResponse
    {
        $clientId = $request->query->get('client_id', '');
        $dateFrom = $request->query->get('from', '');
        $dateTo = $request->query->get('to', '');

        if (!$clientId) {
            return $this->json(['error' => 'client_id is required'], Response::HTTP_BAD_REQUEST);
        }

        // Build criteria
        $criteria = ['client_id' => $clientId];
        if ($dateFrom) {
            $criteria['from'] = $dateFrom;
        }
        if ($dateTo) {
            $criteria['to'] = $dateTo;
        }

        // Get summary data
        $summary = $this->rankingRepository->getSummary($criteria);

        // Calculate average position
        $avgPosition = $summary['total_position'] / max($summary['total_keywords'], 1);
        
        // Get top movers (keywords with biggest position improvements)
        $topMovers = $this->rankingRepository->getTopMovers($criteria, 10);

        $summaryData = [
            'client_id' => $clientId,
            'date_range' => [
                'from' => $dateFrom,
                'to' => $dateTo
            ],
            'overview' => [
                'total_keywords' => $summary['total_keywords'],
                'average_position' => round($avgPosition, 2),
                'total_position_change' => $summary['total_position_change'],
                'keywords_improved' => $summary['keywords_improved'],
                'keywords_declined' => $summary['keywords_declined']
            ],
            'top_movers' => array_map(fn($mover) => [
                'keyword' => $mover['keyword'],
                'position_change' => $mover['position_change'],
                'current_position' => $mover['current_position'],
                'previous_position' => $mover['previous_position']
            ], $topMovers),
            'performance_metrics' => [
                'avg_ctr' => $summary['avg_ctr'] ?? 0,
                'total_search_volume' => $summary['total_search_volume'] ?? 0,
                'visibility_score' => $summary['visibility_score'] ?? 0
            ]
        ];

        return $this->json($summaryData);
    }

    #[Route('/{id}', name: 'api_v1_rankings_get', methods: ['GET'])]
    #[IsGranted('ROLE_AGENCY_ADMIN')]
    public function getRanking(string $id): JsonResponse
    {
        if (!Uuid::isValid($id)) {
            return $this->json(['error' => 'Invalid UUID'], Response::HTTP_BAD_REQUEST);
        }

        $ranking = $this->rankingRepository->find($id);
        if (!$ranking) {
            return $this->json(['error' => 'Ranking not found'], Response::HTTP_NOT_FOUND);
        }

        $rankingData = [
            'id' => $ranking->getId(),
            'keyword_id' => $ranking->getKeywordId(),
            'keyword' => $ranking->getKeyword(),
            'position' => $ranking->getPosition(),
            'previous_position' => $ranking->getPreviousPosition(),
            'change' => $ranking->getChange(),
            'search_volume' => $ranking->getSearchVolume(),
            'ctr' => $ranking->getCtr(),
            'date' => $ranking->getDate()->format('Y-m-d'),
            'metadata' => $ranking->getMetadata(),
            'created_at' => $ranking->getCreatedAt()->format('c'),
            'updated_at' => $ranking->getUpdatedAt()->format('c')
        ];

        return $this->json($rankingData);
    }
}
