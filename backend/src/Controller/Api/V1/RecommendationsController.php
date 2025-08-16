<?php

namespace App\Controller\Api\V1;

use App\Entity\Recommendation;
use App\Repository\RecommendationRepository;
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

#[Route('/api/v1/recommendations')]
class RecommendationsController extends AbstractController
{
    public function __construct(
        private RecommendationRepository $recommendationRepository,
        private EntityManagerInterface $entityManager,
        private ValidatorInterface $validator
    ) {}

    #[Route('', name: 'api_v1_recommendations_list', methods: ['GET'])]
    #[IsGranted('ROLE_AGENCY_ADMIN')]
    public function listRecommendations(Request $request): JsonResponse
    {
        $page = max(1, (int) $request->query->get('page', 1));
        $perPage = min(100, max(1, (int) $request->query->get('per_page', 20)));
        $sort = $request->query->get('sort', 'priority');
        $clientId = $request->query->get('client_id', '');
        $status = $request->query->get('status', '');
        $priority = $request->query->get('priority', '');

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
        if ($status) {
            $criteria['status'] = $status;
        }
        if ($priority) {
            $criteria['priority'] = $priority;
        }

        // Get recommendations with pagination and filtering
        $recommendations = $this->recommendationRepository->findByCriteria($criteria, $sortFields, $perPage, ($page - 1) * $perPage);
        $totalRecommendations = $this->recommendationRepository->countByCriteria($criteria);

        $recommendationData = [];
        foreach ($recommendations as $recommendation) {
            $recommendationData[] = [
                'id' => $recommendation->getId(),
                'title' => $recommendation->getTitle(),
                'description' => $recommendation->getDescription(),
                'client_id' => $recommendation->getClientId(),
                'category' => $recommendation->getCategory(),
                'priority' => $recommendation->getPriority(),
                'status' => $recommendation->getStatus(),
                'assigned_to' => $recommendation->getAssignedTo(),
                'due_date' => $recommendation->getDueDate()?->format('Y-m-d'),
                'estimated_effort' => $recommendation->getEstimatedEffort(),
                'impact_score' => $recommendation->getImpactScore(),
                'created_at' => $recommendation->getCreatedAt()->format('c'),
                'updated_at' => $recommendation->getUpdatedAt()->format('c')
            ];
        }

        return $this->json([
            'data' => $recommendationData,
            'pagination' => [
                'page' => $page,
                'per_page' => $perPage,
                'total' => $totalRecommendations,
                'pages' => ceil($totalRecommendations / $perPage)
            ]
        ]);
    }

    #[Route('/{id}', name: 'api_v1_recommendations_get', methods: ['GET'])]
    #[IsGranted('ROLE_AGENCY_ADMIN')]
    public function getRecommendation(string $id): JsonResponse
    {
        if (!Uuid::isValid($id)) {
            return $this->json(['error' => 'Invalid UUID'], Response::HTTP_BAD_REQUEST);
        }

        $recommendation = $this->recommendationRepository->find($id);
        if (!$recommendation) {
            return $this->json(['error' => 'Recommendation not found'], Response::HTTP_NOT_FOUND);
        }

        $recommendationData = [
            'id' => $recommendation->getId(),
            'title' => $recommendation->getTitle(),
            'description' => $recommendation->getDescription(),
            'client_id' => $recommendation->getClientId(),
            'category' => $recommendation->getCategory(),
            'priority' => $recommendation->getPriority(),
            'status' => $recommendation->getStatus(),
            'assigned_to' => $recommendation->getAssignedTo(),
            'due_date' => $recommendation->getDueDate()?->format('Y-m-d'),
            'estimated_effort' => $recommendation->getEstimatedEffort(),
            'impact_score' => $recommendation->getImpactScore(),
            'metadata' => $recommendation->getMetadata(),
            'created_at' => $recommendation->getCreatedAt()->format('c'),
            'updated_at' => $recommendation->getUpdatedAt()->format('c')
        ];

        return $this->json($recommendationData);
    }

    #[Route('/{id}', name: 'api_v1_recommendations_update', methods: ['PATCH'])]
    #[IsGranted('ROLE_AGENCY_ADMIN')]
    public function updateRecommendation(string $id, Request $request): JsonResponse
    {
        try {
            if (!Uuid::isValid($id)) {
                return $this->json(['error' => 'Invalid UUID'], Response::HTTP_BAD_REQUEST);
            }

            $recommendation = $this->recommendationRepository->find($id);
            if (!$recommendation) {
                return $this->json(['error' => 'Recommendation not found'], Response::HTTP_NOT_FOUND);
            }

            $data = json_decode($request->getContent(), true);
            
            if (!$data) {
                return $this->json(['error' => 'Invalid JSON'], Response::HTTP_BAD_REQUEST);
            }

            // Validate input
            $constraints = new Assert\Collection([
                'status' => [new Assert\Optional([new Assert\Choice(['todo', 'in_progress', 'completed', 'cancelled'])])],
                'assigned_to' => [new Assert\Optional([new Assert\Uuid()])],
                'due_date' => [new Assert\Optional([new Assert\Date()])],
                'notes' => [new Assert\Optional([new Assert\NotBlank()])],
                'progress' => [new Assert\Optional([new Assert\Range(['min' => 0, 'max' => 100])])]
            ]);

            $violations = $this->validator->validate($data, $constraints);
            if (count($violations) > 0) {
                $errors = [];
                foreach ($violations as $violation) {
                    $errors[$violation->getPropertyPath()] = $violation->getMessage();
                }
                return $this->json(['error' => 'Validation failed', 'details' => $errors], Response::HTTP_BAD_REQUEST);
            }

            // Update fields
            if (isset($data['status'])) {
                $recommendation->setStatus($data['status']);
            }

            if (isset($data['assigned_to'])) {
                $recommendation->setAssignedTo($data['assigned_to']);
            }

            if (isset($data['due_date'])) {
                $recommendation->setDueDate(new \DateTimeImmutable($data['due_date']));
            }

            if (isset($data['notes'])) {
                $metadata = $recommendation->getMetadata() ?? [];
                $metadata['notes'] = $data['notes'];
                $recommendation->setMetadata($metadata);
            }

            if (isset($data['progress'])) {
                $metadata = $recommendation->getMetadata() ?? [];
                $metadata['progress'] = $data['progress'];
                $recommendation->setMetadata($metadata);
            }

            $this->entityManager->flush();

            $recommendationData = [
                'id' => $recommendation->getId(),
                'title' => $recommendation->getTitle(),
                'description' => $recommendation->getDescription(),
                'client_id' => $recommendation->getClientId(),
                'category' => $recommendation->getCategory(),
                'priority' => $recommendation->getPriority(),
                'status' => $recommendation->getStatus(),
                'assigned_to' => $recommendation->getAssignedTo(),
                'due_date' => $recommendation->getDueDate()?->format('Y-m-d'),
                'estimated_effort' => $recommendation->getEstimatedEffort(),
                'impact_score' => $recommendation->getImpactScore(),
                'created_at' => $recommendation->getCreatedAt()->format('c'),
                'updated_at' => $recommendation->getUpdatedAt()->format('c')
            ];

            return $this->json([
                'message' => 'Recommendation updated successfully',
                'recommendation' => $recommendationData
            ]);

        } catch (\Exception $e) {
            return $this->json(['error' => 'Internal server error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
