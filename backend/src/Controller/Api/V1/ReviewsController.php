<?php

namespace App\Controller\Api\V1;

use App\Entity\Review;
use App\Repository\ReviewRepository;
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

#[Route('/api/v1/reviews')]
class ReviewsController extends AbstractController
{
    public function __construct(
        private ReviewRepository $reviewRepository,
        private EntityManagerInterface $entityManager,
        private ValidatorInterface $validator
    ) {}

    #[Route('', name: 'api_v1_reviews_list', methods: ['GET'])]
    #[IsGranted('ROLE_AGENCY_ADMIN')]
    public function listReviews(Request $request): JsonResponse
    {
        $page = max(1, (int) $request->query->get('page', 1));
        $perPage = min(100, max(1, (int) $request->query->get('per_page', 20)));
        $sort = $request->query->get('sort', 'created_at');
        $clientId = $request->query->get('client_id', '');
        $platform = $request->query->get('platform', '');
        $rating = $request->query->get('rating', '');
        $status = $request->query->get('status', '');

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
        if ($platform) {
            $criteria['platform'] = $platform;
        }
        if ($rating) {
            $criteria['rating'] = (int) $rating;
        }
        if ($status) {
            $criteria['status'] = $status;
        }

        // Get reviews with pagination and filtering
        $reviews = $this->reviewRepository->findByCriteria($criteria, $sortFields, $perPage, ($page - 1) * $perPage);
        $totalReviews = $this->reviewRepository->countByCriteria($criteria);

        $reviewData = [];
        foreach ($reviews as $review) {
            $reviewData[] = [
                'id' => $review->getId(),
                'platform' => $review->getPlatform(),
                'platform_review_id' => $review->getPlatformReviewId(),
                'client_id' => $review->getClientId(),
                'author_name' => $review->getAuthorName(),
                'rating' => $review->getRating(),
                'title' => $review->getTitle(),
                'content' => $review->getContent(),
                'status' => $review->getStatus(),
                'review_date' => $review->getReviewDate()?->format('Y-m-d'),
                'response_text' => $review->getResponseText(),
                'response_date' => $review->getResponseDate()?->format('Y-m-d'),
                'created_at' => $review->getCreatedAt()->format('c'),
                'updated_at' => $review->getUpdatedAt()->format('c')
            ];
        }

        return $this->json([
            'data' => $reviewData,
            'pagination' => [
                'page' => $page,
                'per_page' => $perPage,
                'total' => $totalReviews,
                'pages' => ceil($totalReviews / $perPage)
            ]
        ]);
    }

    #[Route('/{id}', name: 'api_v1_reviews_get', methods: ['GET'])]
    #[IsGranted('ROLE_AGENCY_ADMIN')]
    public function getReview(string $id): JsonResponse
    {
        if (!Uuid::isValid($id)) {
            return $this->json(['error' => 'Invalid UUID'], Response::HTTP_BAD_REQUEST);
        }

        $review = $this->reviewRepository->find($id);
        if (!$review) {
            return $this->json(['error' => 'Review not found'], Response::HTTP_NOT_FOUND);
        }

        $reviewData = [
            'id' => $review->getId(),
            'platform' => $review->getPlatform(),
            'platform_review_id' => $review->getPlatformReviewId(),
            'client_id' => $review->getClientId(),
            'author_name' => $review->getAuthorName(),
            'rating' => $review->getRating(),
            'title' => $review->getTitle(),
            'content' => $review->getContent(),
            'status' => $review->getStatus(),
            'review_date' => $review->getReviewDate()?->format('Y-m-d'),
            'response_text' => $review->getResponseText(),
            'response_date' => $review->getResponseDate()?->format('Y-m-d'),
            'metadata' => $review->getMetadata(),
            'created_at' => $review->getCreatedAt()->format('c'),
            'updated_at' => $review->getUpdatedAt()->format('c')
        ];

        return $this->json($reviewData);
    }

    #[Route('/{id}/respond', name: 'api_v1_reviews_respond', methods: ['POST'])]
    #[IsGranted('ROLE_AGENCY_ADMIN')]
    public function respondToReview(string $id, Request $request): JsonResponse
    {
        try {
            if (!Uuid::isValid($id)) {
                return $this->json(['error' => 'Invalid UUID'], Response::HTTP_BAD_REQUEST);
            }

            $review = $this->reviewRepository->find($id);
            if (!$review) {
                return $this->json(['error' => 'Review not found'], Response::HTTP_NOT_FOUND);
            }

            $data = json_decode($request->getContent(), true);
            
            if (!$data) {
                return $this->json(['error' => 'Invalid JSON'], Response::HTTP_BAD_REQUEST);
            }

            // Validate input
            $constraints = new Assert\Collection([
                'text' => [new Assert\NotBlank()],
                'public' => [new Assert\Optional([new Assert\Type('boolean')])],
                'internal_notes' => [new Assert\Optional([new Assert\NotBlank()])]
            ]);

            $violations = $this->validator->validate($data, $constraints);
            if (count($violations) > 0) {
                $errors = [];
                foreach ($violations as $violation) {
                    $errors[$violation->getPropertyPath()] = $violation->getMessage();
                }
                return $this->json(['error' => 'Validation failed', 'details' => $errors], Response::HTTP_BAD_REQUEST);
            }

            // Update review with response
            $review->setResponseText($data['text']);
            $review->setResponseDate(new \DateTimeImmutable());
            $review->setStatus('responded');

            if (isset($data['internal_notes'])) {
                $metadata = $review->getMetadata() ?? [];
                $metadata['internal_notes'] = $data['internal_notes'];
                $review->setMetadata($metadata);
            }

            $this->entityManager->flush();

            $reviewData = [
                'id' => $review->getId(),
                'platform' => $review->getPlatform(),
                'client_id' => $review->getClientId(),
                'rating' => $review->getRating(),
                'title' => $review->getTitle(),
                'content' => $review->getContent(),
                'status' => $review->getStatus(),
                'response_text' => $review->getResponseText(),
                'response_date' => $review->getResponseDate()?->format('Y-m-d'),
                'updated_at' => $review->getUpdatedAt()->format('c')
            ];

            return $this->json([
                'message' => 'Review response added successfully',
                'review' => $reviewData
            ]);

        } catch (\Exception $e) {
            return $this->json(['error' => 'Internal server error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/sync', name: 'api_v1_reviews_sync', methods: ['POST'])]
    #[IsGranted('ROLE_AGENCY_ADMIN')]
    public function syncReviews(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            
            if (!$data) {
                return $this->json(['error' => 'Invalid JSON'], Response::HTTP_BAD_REQUEST);
            }

            // Validate input
            $constraints = new Assert\Collection([
                'platforms' => [new Assert\NotBlank(), new Assert\Type('array'), new Assert\All([
                    new Assert\Choice(['google', 'yelp', 'facebook', 'trustpilot'])
                ])],
                'client_id' => [new Assert\NotBlank(), new Assert\Uuid()],
                'force_refresh' => [new Assert\Optional([new Assert\Type('boolean')])]
            ]);

            $violations = $this->validator->validate($data, $constraints);
            if (count($violations) > 0) {
                $errors = [];
                foreach ($violations as $violation) {
                    $errors[$violation->getPropertyPath()] = $violation->getMessage();
                }
                return $this->json(['error' => 'Validation failed', 'details' => $errors], Response::HTTP_BAD_REQUEST);
            }

            // Here you would typically enqueue a job to sync reviews
            // For now, we'll return a success response
            $syncJobData = [
                'id' => Uuid::v4()->toRfc4122(),
                'platforms' => $data['platforms'],
                'client_id' => $data['client_id'],
                'force_refresh' => $data['force_refresh'] ?? false,
                'status' => 'queued',
                'created_at' => (new \DateTimeImmutable())->format('c')
            ];

            return $this->json([
                'message' => 'Review sync job queued successfully',
                'sync_job' => $syncJobData
            ], Response::HTTP_CREATED);

        } catch (\Exception $e) {
            return $this->json(['error' => 'Internal server error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
