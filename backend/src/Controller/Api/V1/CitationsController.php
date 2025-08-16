<?php

namespace App\Controller\Api\V1;

use App\Entity\Citation;
use App\Repository\CitationRepository;
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

#[Route('/api/v1/citations')]
class CitationsController extends AbstractController
{
    public function __construct(
        private CitationRepository $citationRepository,
        private EntityManagerInterface $entityManager,
        private ValidatorInterface $validator
    ) {}

    #[Route('', name: 'api_v1_citations_list', methods: ['GET'])]
    #[IsGranted('ROLE_AGENCY_ADMIN')]
    public function listCitations(Request $request): JsonResponse
    {
        $page = max(1, (int) $request->query->get('page', 1));
        $perPage = min(100, max(1, (int) $request->query->get('per_page', 20)));
        $sort = $request->query->get('sort', 'created_at');
        $clientId = $request->query->get('client_id', '');
        $status = $request->query->get('status', '');
        $platform = $request->query->get('platform', '');

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
        if ($platform) {
            $criteria['platform'] = $platform;
        }

        // Get citations with pagination and filtering
        $citations = $this->citationRepository->findByCriteria($criteria, $sortFields, $perPage, ($page - 1) * $perPage);
        $totalCitations = $this->citationRepository->countByCriteria($criteria);

        $citationData = [];
        foreach ($citations as $citation) {
            $citationData[] = [
                'id' => $citation->getId(),
                'platform' => $citation->getPlatform(),
                'url' => $citation->getUrl(),
                'client_id' => $citation->getClientId(),
                'status' => $citation->getStatus(),
                'business_name' => $citation->getBusinessName(),
                'address' => $citation->getAddress(),
                'phone' => $citation->getPhone(),
                'website' => $citation->getWebsite(),
                'notes' => $citation->getNotes(),
                'created_at' => $citation->getCreatedAt()->format('c'),
                'updated_at' => $citation->getUpdatedAt()->format('c')
            ];
        }

        return $this->json([
            'data' => $citationData,
            'pagination' => [
                'page' => $page,
                'per_page' => $perPage,
                'total' => $totalCitations,
                'pages' => ceil($totalCitations / $perPage)
            ]
        ]);
    }

    #[Route('', name: 'api_v1_citations_create', methods: ['POST'])]
    #[IsGranted('ROLE_AGENCY_ADMIN')]
    public function createCitation(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            
            if (!$data) {
                return $this->json(['error' => 'Invalid JSON'], Response::HTTP_BAD_REQUEST);
            }

            // Validate input
            $constraints = new Assert\Collection([
                'platform' => [new Assert\NotBlank(), new Assert\Choice(['Google', 'Yelp', 'YellowPages', 'Facebook', 'Bing', 'Other'])],
                'url' => [new Assert\NotBlank(), new Assert\Url()],
                'client_id' => [new Assert\NotBlank(), new Assert\Uuid()],
                'status' => [new Assert\Optional([new Assert\Choice(['claimed', 'unclaimed', 'pending', 'verified'])])],
                'business_name' => [new Assert\Optional([new Assert\NotBlank()])],
                'address' => [new Assert\Optional([new Assert\NotBlank()])],
                'phone' => [new Assert\Optional([new Assert\NotBlank()])],
                'website' => [new Assert\Optional([new Assert\Url()])],
                'notes' => [new Assert\Optional([new Assert\NotBlank()])]
            ]);

            $violations = $this->validator->validate($data, $constraints);
            if (count($violations) > 0) {
                $errors = [];
                foreach ($violations as $violation) {
                    $errors[$violation->getPropertyPath()] = $violation->getMessage();
                }
                return $this->json(['error' => 'Validation failed', 'details' => $errors], Response::HTTP_BAD_REQUEST);
            }

            // Create citation
            $citation = new Citation();
            $citation->setPlatform($data['platform']);
            $citation->setUrl($data['url']);
            $citation->setClientId($data['client_id']);
            $citation->setStatus($data['status'] ?? 'pending');

            if (isset($data['business_name'])) {
                $citation->setBusinessName($data['business_name']);
            }

            if (isset($data['address'])) {
                $citation->setAddress($data['address']);
            }

            if (isset($data['phone'])) {
                $citation->setPhone($data['phone']);
            }

            if (isset($data['website'])) {
                $citation->setWebsite($data['website']);
            }

            if (isset($data['notes'])) {
                $citation->setNotes($data['notes']);
            }

            $this->entityManager->persist($citation);
            $this->entityManager->flush();

            $citationData = [
                'id' => $citation->getId(),
                'platform' => $citation->getPlatform(),
                'url' => $citation->getUrl(),
                'client_id' => $citation->getClientId(),
                'status' => $citation->getStatus(),
                'business_name' => $citation->getBusinessName(),
                'address' => $citation->getAddress(),
                'phone' => $citation->getPhone(),
                'website' => $citation->getWebsite(),
                'notes' => $citation->getNotes(),
                'created_at' => $citation->getCreatedAt()->format('c'),
                'updated_at' => $citation->getUpdatedAt()->format('c')
            ];

            return $this->json([
                'message' => 'Citation created successfully',
                'citation' => $citationData
            ], Response::HTTP_CREATED);

        } catch (\Exception $e) {
            return $this->json(['error' => 'Internal server error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/{id}', name: 'api_v1_citations_get', methods: ['GET'])]
    #[IsGranted('ROLE_AGENCY_ADMIN')]
    public function getCitation(string $id): JsonResponse
    {
        if (!Uuid::isValid($id)) {
            return $this->json(['error' => 'Invalid UUID'], Response::HTTP_BAD_REQUEST);
        }

        $citation = $this->citationRepository->find($id);
        if (!$citation) {
            return $this->json(['error' => 'Citation not found'], Response::HTTP_NOT_FOUND);
        }

        $citationData = [
            'id' => $citation->getId(),
            'platform' => $citation->getPlatform(),
            'url' => $citation->getUrl(),
            'client_id' => $citation->getClientId(),
            'status' => $citation->getStatus(),
            'business_name' => $citation->getBusinessName(),
            'address' => $citation->getAddress(),
            'phone' => $citation->getPhone(),
            'website' => $citation->getWebsite(),
            'notes' => $citation->getNotes(),
            'metadata' => $citation->getMetadata(),
            'created_at' => $citation->getCreatedAt()->format('c'),
            'updated_at' => $citation->getUpdatedAt()->format('c')
        ];

        return $this->json($citationData);
    }

    #[Route('/{id}', name: 'api_v1_citations_update', methods: ['PATCH'])]
    #[IsGranted('ROLE_AGENCY_ADMIN')]
    public function updateCitation(string $id, Request $request): JsonResponse
    {
        try {
            if (!Uuid::isValid($id)) {
                return $this->json(['error' => 'Invalid UUID'], Response::HTTP_BAD_REQUEST);
            }

            $citation = $this->citationRepository->find($id);
            if (!$citation) {
                return $this->json(['error' => 'Citation not found'], Response::HTTP_NOT_FOUND);
            }

            $data = json_decode($request->getContent(), true);
            
            if (!$data) {
                return $this->json(['error' => 'Invalid JSON'], Response::HTTP_BAD_REQUEST);
            }

            // Validate input
            $constraints = new Assert\Collection([
                'status' => [new Assert\Optional([new Assert\Choice(['claimed', 'unclaimed', 'pending', 'verified'])])],
                'business_name' => [new Assert\Optional([new Assert\NotBlank()])],
                'address' => [new Assert\Optional([new Assert\NotBlank()])],
                'phone' => [new Assert\Optional([new Assert\NotBlank()])],
                'website' => [new Assert\Optional([new Assert\Url()])],
                'notes' => [new Assert\Optional([new Assert\NotBlank()])]
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
                $citation->setStatus($data['status']);
            }

            if (isset($data['business_name'])) {
                $citation->setBusinessName($data['business_name']);
            }

            if (isset($data['address'])) {
                $citation->setAddress($data['address']);
            }

            if (isset($data['phone'])) {
                $citation->setPhone($data['phone']);
            }

            if (isset($data['website'])) {
                $citation->setWebsite($data['website']);
            }

            if (isset($data['notes'])) {
                $citation->setNotes($data['notes']);
            }

            $this->entityManager->flush();

            $citationData = [
                'id' => $citation->getId(),
                'platform' => $citation->getPlatform(),
                'url' => $citation->getUrl(),
                'client_id' => $citation->getClientId(),
                'status' => $citation->getStatus(),
                'business_name' => $citation->getBusinessName(),
                'address' => $citation->getAddress(),
                'phone' => $citation->getPhone(),
                'website' => $citation->getWebsite(),
                'notes' => $citation->getNotes(),
                'created_at' => $citation->getCreatedAt()->format('c'),
                'updated_at' => $citation->getUpdatedAt()->format('c')
            ];

            return $this->json([
                'message' => 'Citation updated successfully',
                'citation' => $citationData
            ]);

        } catch (\Exception $e) {
            return $this->json(['error' => 'Internal server error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
