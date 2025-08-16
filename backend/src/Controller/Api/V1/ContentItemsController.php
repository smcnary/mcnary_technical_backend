<?php

namespace App\Controller\Api\V1;

use App\Entity\ContentItem;
use App\Repository\ContentItemRepository;
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

#[Route('/api/v1/content-items')]
class ContentItemsController extends AbstractController
{
    public function __construct(
        private ContentItemRepository $contentItemRepository,
        private EntityManagerInterface $entityManager,
        private ValidatorInterface $validator
    ) {}

    #[Route('', name: 'api_v1_content_items_list', methods: ['GET'])]
    #[IsGranted('ROLE_AGENCY_ADMIN')]
    public function listContentItems(Request $request): JsonResponse
    {
        $page = max(1, (int) $request->query->get('page', 1));
        $perPage = min(100, max(1, (int) $request->query->get('per_page', 20)));
        $sort = $request->query->get('sort', 'created_at');
        $clientId = $request->query->get('client_id', '');
        $status = $request->query->get('status', '');
        $type = $request->query->get('type', '');
        $authorId = $request->query->get('author_id', '');

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
        if ($type) {
            $criteria['type'] = $type;
        }
        if ($authorId) {
            $criteria['author_id'] = $authorId;
        }

        // Get content items with pagination and filtering
        $contentItems = $this->contentItemRepository->findByCriteria($criteria, $sortFields, $perPage, ($page - 1) * $perPage);
        $totalContentItems = $this->contentItemRepository->countByCriteria($criteria);

        $contentItemData = [];
        foreach ($contentItems as $contentItem) {
            $contentItemData[] = [
                'id' => $contentItem->getId(),
                'title' => $contentItem->getTitle(),
                'type' => $contentItem->getType(),
                'client_id' => $contentItem->getClientId(),
                'author_id' => $contentItem->getAuthorId(),
                'status' => $contentItem->getStatus(),
                'excerpt' => $contentItem->getExcerpt(),
                'content' => $contentItem->getContent(),
                'target_keywords' => $contentItem->getTargetKeywords(),
                'publish_date' => $contentItem->getPublishDate()?->format('Y-m-d'),
                'created_at' => $contentItem->getCreatedAt()->format('c'),
                'updated_at' => $contentItem->getUpdatedAt()->format('c')
            ];
        }

        return $this->json([
            'data' => $contentItemData,
            'pagination' => [
                'page' => $page,
                'per_page' => $perPage,
                'total' => $totalContentItems,
                'pages' => ceil($totalContentItems / $perPage)
            ]
        ]);
    }

    #[Route('', name: 'api_v1_content_items_create', methods: ['POST'])]
    #[IsGranted('ROLE_AGENCY_ADMIN')]
    public function createContentItem(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            
            if (!$data) {
                return $this->json(['error' => 'Invalid JSON'], Response::HTTP_BAD_REQUEST);
            }

            // Validate input
            $constraints = new Assert\Collection([
                'title' => [new Assert\NotBlank()],
                'type' => [new Assert\NotBlank(), new Assert\Choice(['blog', 'page', 'video', 'infographic', 'case_study'])],
                'client_id' => [new Assert\NotBlank(), new Assert\Uuid()],
                'author_id' => [new Assert\Optional([new Assert\Uuid()])],
                'excerpt' => [new Assert\Optional([new Assert\NotBlank()])],
                'content' => [new Assert\Optional([new Assert\NotBlank()])],
                'target_keywords' => [new Assert\Optional([new Assert\Type('array')])],
                'publish_date' => [new Assert\Optional([new Assert\Date()])]
            ]);

            $violations = $this->validator->validate($data, $constraints);
            if (count($violations) > 0) {
                $errors = [];
                foreach ($violations as $violation) {
                    $errors[$violation->getPropertyPath()] = $violation->getMessage();
                }
                return $this->json(['error' => 'Validation failed', 'details' => $errors], Response::HTTP_BAD_REQUEST);
            }

            // Create content item
            $contentItem = new ContentItem();
            $contentItem->setTitle($data['title']);
            $contentItem->setType($data['type']);
            $contentItem->setClientId($data['client_id']);
            $contentItem->setStatus('draft');

            if (isset($data['author_id'])) {
                $contentItem->setAuthorId($data['author_id']);
            }

            if (isset($data['excerpt'])) {
                $contentItem->setExcerpt($data['excerpt']);
            }

            if (isset($data['content'])) {
                $contentItem->setContent($data['content']);
            }

            if (isset($data['target_keywords'])) {
                $contentItem->setTargetKeywords($data['target_keywords']);
            }

            if (isset($data['publish_date'])) {
                $contentItem->setPublishDate(new \DateTimeImmutable($data['publish_date']));
            }

            $this->entityManager->persist($contentItem);
            $this->entityManager->flush();

            $contentItemData = [
                'id' => $contentItem->getId(),
                'title' => $contentItem->getTitle(),
                'type' => $contentItem->getType(),
                'client_id' => $contentItem->getClientId(),
                'author_id' => $contentItem->getAuthorId(),
                'status' => $contentItem->getStatus(),
                'excerpt' => $contentItem->getExcerpt(),
                'content' => $contentItem->getContent(),
                'target_keywords' => $contentItem->getTargetKeywords(),
                'publish_date' => $contentItem->getPublishDate()?->format('Y-m-d'),
                'created_at' => $contentItem->getCreatedAt()->format('c'),
                'updated_at' => $contentItem->getUpdatedAt()->format('c')
            ];

            return $this->json([
                'message' => 'Content item created successfully',
                'content_item' => $contentItemData
            ], Response::HTTP_CREATED);

        } catch (\Exception $e) {
            return $this->json(['error' => 'Internal server error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/{id}', name: 'api_v1_content_items_get', methods: ['GET'])]
    #[IsGranted('ROLE_AGENCY_ADMIN')]
    public function getContentItem(string $id): JsonResponse
    {
        if (!Uuid::isValid($id)) {
            return $this->json(['error' => 'Invalid UUID'], Response::HTTP_BAD_REQUEST);
        }

        $contentItem = $this->contentItemRepository->find($id);
        if (!$contentItem) {
            return $this->json(['error' => 'Content item not found'], Response::HTTP_NOT_FOUND);
        }

        $contentItemData = [
            'id' => $contentItem->getId(),
            'title' => $contentItem->getTitle(),
            'type' => $contentItem->getType(),
            'client_id' => $contentItem->getClientId(),
            'author_id' => $contentItem->getAuthorId(),
            'status' => $contentItem->getStatus(),
            'excerpt' => $contentItem->getExcerpt(),
            'content' => $contentItem->getContent(),
            'target_keywords' => $contentItem->getTargetKeywords(),
            'publish_date' => $contentItem->getPublishDate()?->format('Y-m-d'),
            'metadata' => $contentItem->getMetadata(),
            'created_at' => $contentItem->getCreatedAt()->format('c'),
            'updated_at' => $contentItem->getUpdatedAt()->format('c')
        ];

        return $this->json($contentItemData);
    }

    #[Route('/{id}', name: 'api_v1_content_items_update', methods: ['PATCH'])]
    #[IsGranted('ROLE_AGENCY_ADMIN')]
    public function updateContentItem(string $id, Request $request): JsonResponse
    {
        try {
            if (!Uuid::isValid($id)) {
                return $this->json(['error' => 'Invalid UUID'], Response::HTTP_BAD_REQUEST);
            }

            $contentItem = $this->contentItemRepository->find($id);
            if (!$contentItem) {
                return $this->json(['error' => 'Content item not found'], Response::HTTP_NOT_FOUND);
            }

            $data = json_decode($request->getContent(), true);
            
            if (!$data) {
                return $this->json(['error' => 'Invalid JSON'], Response::HTTP_BAD_REQUEST);
            }

            // Validate input
            $constraints = new Assert\Collection([
                'title' => [new Assert\Optional([new Assert\NotBlank()])],
                'status' => [new Assert\Optional([new Assert\Choice(['draft', 'review', 'published', 'archived'])])],
                'excerpt' => [new Assert\Optional([new Assert\NotBlank()])],
                'content' => [new Assert\Optional([new Assert\NotBlank()])],
                'target_keywords' => [new Assert\Optional([new Assert\Type('array')])],
                'publish_date' => [new Assert\Optional([new Assert\Date()])]
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
            if (isset($data['title'])) {
                $contentItem->setTitle($data['title']);
            }

            if (isset($data['status'])) {
                $contentItem->setStatus($data['status']);
            }

            if (isset($data['excerpt'])) {
                $contentItem->setExcerpt($data['excerpt']);
            }

            if (isset($data['content'])) {
                $contentItem->setContent($data['content']);
            }

            if (isset($data['target_keywords'])) {
                $contentItem->setTargetKeywords($data['target_keywords']);
            }

            if (isset($data['publish_date'])) {
                $contentItem->setPublishDate(new \DateTimeImmutable($data['publish_date']));
            }

            $this->entityManager->flush();

            $contentItemData = [
                'id' => $contentItem->getId(),
                'title' => $contentItem->getTitle(),
                'type' => $contentItem->getType(),
                'client_id' => $contentItem->getClientId(),
                'author_id' => $contentItem->getAuthorId(),
                'status' => $contentItem->getStatus(),
                'excerpt' => $contentItem->getExcerpt(),
                'content' => $contentItem->getContent(),
                'target_keywords' => $contentItem->getTargetKeywords(),
                'publish_date' => $contentItem->getPublishDate()?->format('Y-m-d'),
                'created_at' => $contentItem->getCreatedAt()->format('c'),
                'updated_at' => $contentItem->getUpdatedAt()->format('c')
            ];

            return $this->json([
                'message' => 'Content item updated successfully',
                'content_item' => $contentItemData
            ]);

        } catch (\Exception $e) {
            return $this->json(['error' => 'Internal server error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
