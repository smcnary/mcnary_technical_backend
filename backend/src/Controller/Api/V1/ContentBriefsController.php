<?php

namespace App\Controller\Api\V1;

use App\Entity\ContentBrief;
use App\Repository\ContentBriefRepository;
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

#[Route('/api/v1/content-briefs')]
class ContentBriefsController extends AbstractController
{
    public function __construct(
        private ContentBriefRepository $contentBriefRepository,
        private EntityManagerInterface $entityManager,
        private ValidatorInterface $validator
    ) {}

    #[Route('', name: 'api_v1_content_briefs_list', methods: ['GET'])]
    #[IsGranted('ROLE_AGENCY_ADMIN')]
    public function listContentBriefs(Request $request): JsonResponse
    {
        $page = max(1, (int) $request->query->get('page', 1));
        $perPage = min(100, max(1, (int) $request->query->get('per_page', 20)));
        $sort = $request->query->get('sort', 'created_at');
        $contentItemId = $request->query->get('content_item_id', '');
        $clientId = $request->query->get('client_id', '');
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
        if ($contentItemId) {
            $criteria['content_item_id'] = $contentItemId;
        }
        if ($clientId) {
            $criteria['client_id'] = $clientId;
        }
        if ($status) {
            $criteria['status'] = $status;
        }

        // Get content briefs with pagination and filtering
        $contentBriefs = $this->contentBriefRepository->findByCriteria($criteria, $sortFields, $perPage, ($page - 1) * $perPage);
        $totalContentBriefs = $this->contentBriefRepository->countByCriteria($criteria);

        $contentBriefData = [];
        foreach ($contentBriefs as $contentBrief) {
            $contentBriefData[] = [
                'id' => $contentBrief->getId(),
                'title' => $contentBrief->getTitle(),
                'content_item_id' => $contentBrief->getContentItemId(),
                'client_id' => $contentBrief->getClientId(),
                'outline' => $contentBrief->getOutline(),
                'references' => $contentBrief->getReferences(),
                'target_audience' => $contentBrief->getTargetAudience(),
                'word_count' => $contentBrief->getWordCount(),
                'status' => $contentBrief->getStatus(),
                'created_at' => $contentBrief->getCreatedAt()->format('c'),
                'updated_at' => $contentBrief->getUpdatedAt()->format('c')
            ];
        }

        return $this->json([
            'data' => $contentBriefData,
            'pagination' => [
                'page' => $page,
                'per_page' => $perPage,
                'total' => $totalContentBriefs,
                'pages' => ceil($totalContentBriefs / $perPage)
            ]
        ]);
    }

    #[Route('', name: 'api_v1_content_briefs_create', methods: ['POST'])]
    #[IsGranted('ROLE_AGENCY_ADMIN')]
    public function createContentBrief(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            
            if (!$data) {
                return $this->json(['error' => 'Invalid JSON'], Response::HTTP_BAD_REQUEST);
            }

            // Validate input
            $constraints = new Assert\Collection([
                'title' => [new Assert\NotBlank()],
                'content_item_id' => [new Assert\Optional([new Assert\Uuid()])],
                'client_id' => [new Assert\NotBlank(), new Assert\Uuid()],
                'outline' => [new Assert\Optional([new Assert\Type('array')])],
                'references' => [new Assert\Optional([new Assert\Type('array')])],
                'target_audience' => [new Assert\Optional([new Assert\NotBlank()])],
                'word_count' => [new Assert\Optional([new Assert\Positive()])]
            ]);

            $violations = $this->validator->validate($data, $constraints);
            if (count($violations) > 0) {
                $errors = [];
                foreach ($violations as $violation) {
                    $errors[$violation->getPropertyPath()] = $violation->getMessage();
                }
                return $this->json(['error' => 'Validation failed', 'details' => $errors], Response::HTTP_BAD_REQUEST);
            }

            // Create content brief
            $contentBrief = new ContentBrief();
            $contentBrief->setTitle($data['title']);
            $contentBrief->setClientId($data['client_id']);
            $contentBrief->setStatus('draft');

            if (isset($data['content_item_id'])) {
                $contentBrief->setContentItemId($data['content_item_id']);
            }

            if (isset($data['outline'])) {
                $contentBrief->setOutline($data['outline']);
            }

            if (isset($data['references'])) {
                $contentBrief->setReferences($data['references']);
            }

            if (isset($data['target_audience'])) {
                $contentBrief->setTargetAudience($data['target_audience']);
            }

            if (isset($data['word_count'])) {
                $contentBrief->setWordCount($data['word_count']);
            }

            $this->entityManager->persist($contentBrief);
            $this->entityManager->flush();

            $contentBriefData = [
                'id' => $contentBrief->getId(),
                'title' => $contentBrief->getTitle(),
                'content_item_id' => $contentBrief->getContentItemId(),
                'client_id' => $contentBrief->getClientId(),
                'outline' => $contentBrief->getOutline(),
                'references' => $contentBrief->getReferences(),
                'target_audience' => $contentBrief->getTargetAudience(),
                'word_count' => $contentBrief->getWordCount(),
                'status' => $contentBrief->getStatus(),
                'created_at' => $contentBrief->getCreatedAt()->format('c'),
                'updated_at' => $contentBrief->getUpdatedAt()->format('c')
            ];

            return $this->json([
                'message' => 'Content brief created successfully',
                'content_brief' => $contentBriefData
            ], Response::HTTP_CREATED);

        } catch (\Exception $e) {
            return $this->json(['error' => 'Internal server error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/{id}', name: 'api_v1_content_briefs_get', methods: ['GET'])]
    #[IsGranted('ROLE_AGENCY_ADMIN')]
    public function getContentBrief(string $id): JsonResponse
    {
        if (!Uuid::isValid($id)) {
            return $this->json(['error' => 'Invalid UUID'], Response::HTTP_BAD_REQUEST);
        }

        $contentBrief = $this->contentBriefRepository->find($id);
        if (!$contentBrief) {
            return $this->json(['error' => 'Content brief not found'], Response::HTTP_NOT_FOUND);
        }

        $contentBriefData = [
            'id' => $contentBrief->getId(),
            'title' => $contentBrief->getTitle(),
            'content_item_id' => $contentBrief->getContentItemId(),
            'client_id' => $contentBrief->getClientId(),
            'outline' => $contentBrief->getOutline(),
            'references' => $contentBrief->getReferences(),
            'target_audience' => $contentBrief->getTargetAudience(),
            'word_count' => $contentBrief->getWordCount(),
            'status' => $contentBrief->getStatus(),
            'metadata' => $contentBrief->getMetadata(),
            'created_at' => $contentBrief->getCreatedAt()->format('c'),
            'updated_at' => $contentBrief->getUpdatedAt()->format('c')
        ];

        return $this->json($contentBriefData);
    }
}
