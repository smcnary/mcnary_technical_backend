<?php

namespace App\Controller\Api\V1;

use App\Entity\Keyword;
use App\Repository\KeywordRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[Route('/api/v1/keywords')]
class KeywordsController extends AbstractController
{
    public function __construct(
        private KeywordRepository $keywordRepository,
        private EntityManagerInterface $entityManager,
        private ValidatorInterface $validator
    ) {}

    #[Route('', name: 'api_v1_keywords_list', methods: ['GET'])]
    #[IsGranted('ROLE_AGENCY_ADMIN')]
    public function listKeywords(Request $request): JsonResponse
    {
        $page = max(1, (int) $request->query->get('page', 1));
        $perPage = min(100, max(1, (int) $request->query->get('per_page', 20)));
        $sort = $request->query->get('sort', 'created_at');
        $clientId = $request->query->get('client_id', '');
        $campaignId = $request->query->get('campaign_id', '');
        $status = $request->query->get('status', '');
        $difficulty = $request->query->get('difficulty', '');

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
        if ($clientId) {
            $criteria['client_id'] = $clientId;
        }
        if ($campaignId) {
            $criteria['campaign_id'] = $campaignId;
        }
        if ($status) {
            $criteria['status'] = $status;
        }
        if ($difficulty) {
            $criteria['difficulty'] = $difficulty;
        }

        // Get keywords with pagination and filtering
        $keywords = $this->keywordRepository->findByCriteria($criteria, $sortFields, $perPage, ($page - 1) * $perPage);
        $totalKeywords = $this->keywordRepository->countByCriteria($criteria);

        $keywordData = [];
        foreach ($keywords as $keyword) {
            $keywordData[] = [
                'id' => $keyword->getId(),
                'keyword' => $keyword->getKeyword(),
                'client_id' => $keyword->getClientId(),
                'campaign_id' => $keyword->getCampaignId(),
                'target_position' => $keyword->getTargetPosition(),
                'current_position' => $keyword->getCurrentPosition(),
                'priority' => $keyword->getPriority(),
                'status' => $keyword->getStatus(),
                'difficulty' => $keyword->getDifficulty(),
                'search_volume' => $keyword->getSearchVolume(),
                'created_at' => $keyword->getCreatedAt()->format('c'),
                'updated_at' => $keyword->getUpdatedAt()->format('c')
            ];
        }

        return $this->json([
            'data' => $keywordData,
            'pagination' => [
                'page' => $page,
                'per_page' => $perPage,
                'total' => $totalKeywords,
                'pages' => ceil($totalKeywords / $perPage)
            ]
        ]);
    }

    #[Route('', name: 'api_v1_keywords_create', methods: ['POST'])]
    #[IsGranted('ROLE_AGENCY_ADMIN')]
    public function createKeywords(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            
            if (!$data) {
                return $this->json(['error' => 'Invalid JSON'], Response::HTTP_BAD_REQUEST);
            }

            // Check if this is a bulk import or single keyword
            if (isset($data['keywords']) && is_array($data['keywords'])) {
                return $this->createBulkKeywords($data);
            } else {
                return $this->createSingleKeyword($data);
            }

        } catch (\Exception $e) {
            return $this->json(['error' => 'Internal server error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    private function createSingleKeyword(array $data): JsonResponse
    {
        // Validate input
        $constraints = new Assert\Collection([
            'keyword' => [new Assert\NotBlank()],
            'client_id' => [new Assert\NotBlank(), new Assert\Uuid()],
            'campaign_id' => [new Assert\Optional([new Assert\Uuid()])],
            'target_position' => [new Assert\Optional([new Assert\Positive()])],
            'priority' => [new Assert\Optional([new Assert\Choice(['low', 'medium', 'high', 'urgent'])])]
        ]);

        $violations = $this->validator->validate($data, $constraints);
        if (count($violations) > 0) {
            $errors = [];
            foreach ($violations as $violation) {
                $errors[$violation->getPropertyPath()] = $violation->getMessage();
            }
            return $this->json(['error' => 'Validation failed', 'details' => $errors], Response::HTTP_BAD_REQUEST);
        }

        // Create keyword
        $keyword = new Keyword();
        $keyword->setKeyword($data['keyword']);
        $keyword->setClientId($data['client_id']);
        $keyword->setStatus('tracking');

        if (isset($data['campaign_id'])) {
            $keyword->setCampaignId($data['campaign_id']);
        }

        if (isset($data['target_position'])) {
            $keyword->setTargetPosition($data['target_position']);
        }

        if (isset($data['priority'])) {
            $keyword->setPriority($data['priority']);
        }

        $this->entityManager->persist($keyword);
        $this->entityManager->flush();

        $keywordData = [
            'id' => $keyword->getId(),
            'keyword' => $keyword->getKeyword(),
            'client_id' => $keyword->getClientId(),
            'campaign_id' => $keyword->getCampaignId(),
            'target_position' => $keyword->getTargetPosition(),
            'priority' => $keyword->getPriority(),
            'status' => $keyword->getStatus(),
            'created_at' => $keyword->getCreatedAt()->format('c')
        ];

        return $this->json([
            'message' => 'Keyword created successfully',
            'keyword' => $keywordData
        ], Response::HTTP_CREATED);
    }

    private function createBulkKeywords(array $data): JsonResponse
    {
        // Validate input
        $constraints = new Assert\Collection([
            'keywords' => [new Assert\NotBlank(), new Assert\Type('array'), new Assert\Count(['min' => 1, 'max' => 100])],
            'client_id' => [new Assert\NotBlank(), new Assert\Uuid()],
            'campaign_id' => [new Assert\Optional([new Assert\Uuid()])]
        ]);

        $violations = $this->validator->validate($data, $constraints);
        if (count($violations) > 0) {
            $errors = [];
            foreach ($violations as $violation) {
                $errors[$violation->getPropertyPath()] = $violation->getMessage();
            }
            return $this->json(['error' => 'Validation failed', 'details' => $errors], Response::HTTP_BAD_REQUEST);
        }

        $createdKeywords = [];
        $errors = [];

        foreach ($data['keywords'] as $index => $keywordData) {
            try {
                // Validate individual keyword data
                $keywordConstraints = new Assert\Collection([
                    'keyword' => [new Assert\NotBlank()],
                    'target_position' => [new Assert\Optional([new Assert\Positive()])],
                    'priority' => [new Assert\Optional([new Assert\Choice(['low', 'medium', 'high', 'urgent'])])]
                ]);

                $keywordViolations = $this->validator->validate($keywordData, $keywordConstraints);
                if (count($keywordViolations) > 0) {
                    $errors[] = "Keyword at index {$index}: " . implode(', ', array_map(fn($v) => $v->getMessage(), iterator_to_array($keywordViolations)));
                    continue;
                }

                // Create keyword
                $keyword = new Keyword();
                $keyword->setKeyword($keywordData['keyword']);
                $keyword->setClientId($data['client_id']);
                $keyword->setStatus('tracking');

                if (isset($data['campaign_id'])) {
                    $keyword->setCampaignId($data['campaign_id']);
                }

                if (isset($keywordData['target_position'])) {
                    $keyword->setTargetPosition($keywordData['target_position']);
                }

                if (isset($keywordData['priority'])) {
                    $keyword->setPriority($keywordData['priority']);
                }

                $this->entityManager->persist($keyword);
                $createdKeywords[] = $keyword;

            } catch (\Exception $e) {
                $errors[] = "Keyword at index {$index}: " . $e->getMessage();
            }
        }

        if (!empty($createdKeywords)) {
            $this->entityManager->flush();
        }

        $response = [
            'message' => 'Bulk keyword creation completed',
            'created_count' => count($createdKeywords),
            'total_requested' => count($data['keywords'])
        ];

        if (!empty($errors)) {
            $response['errors'] = $errors;
        }

        if (!empty($createdKeywords)) {
            $response['keywords'] = array_map(fn($k) => [
                'id' => $k->getId(),
                'keyword' => $k->getKeyword(),
                'client_id' => $k->getClientId(),
                'campaign_id' => $k->getCampaignId(),
                'status' => $k->getStatus()
            ], $createdKeywords);
        }

        $statusCode = !empty($createdKeywords) ? Response::HTTP_CREATED : Response::HTTP_BAD_REQUEST;
        return $this->json($response, $statusCode);
    }
}
