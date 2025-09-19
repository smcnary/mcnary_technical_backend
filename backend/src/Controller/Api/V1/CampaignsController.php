<?php

namespace App\Controller\Api\V1;

use App\Entity\Campaign;
use App\Repository\CampaignRepository;
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

#[Route('/api/v1/campaigns')]
class CampaignsController extends AbstractController
{
    public function __construct(
        private CampaignRepository $campaignRepository,
        private EntityManagerInterface $entityManager,
        private ValidatorInterface $validator
    ) {}

    #[Route('', name: 'api_v1_campaigns_list', methods: ['GET'])]
    #[IsGranted('ROLE_CLIENT_STAFF')]
    public function listCampaigns(Request $request): JsonResponse
    {
        $page = max(1, (int) $request->query->get('page', 1));
        $perPage = min(100, max(1, (int) $request->query->get('per_page', 20)));
        $sort = $request->query->get('sort', 'created_at');
        $clientId = $request->query->get('client_id', '');
        $status = $request->query->get('status', '');
        $type = $request->query->get('type', '');

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

        // Get campaigns with pagination and filtering
        $campaigns = $this->campaignRepository->findByCriteria($criteria, $sortFields, $perPage, ($page - 1) * $perPage);
        $totalCampaigns = $this->campaignRepository->countByCriteria($criteria);

        $campaignData = [];
        foreach ($campaigns as $campaign) {
            $campaignData[] = [
                'id' => $campaign->getId(),
                'name' => $campaign->getName(),
                'client_id' => $campaign->getClientId(),
                'type' => $campaign->getType(),
                'status' => $campaign->getStatus(),
                'start_date' => $campaign->getStartDate()?->format('Y-m-d'),
                'end_date' => $campaign->getEndDate()?->format('Y-m-d'),
                'budget' => $campaign->getBudget(),
                'goals' => $campaign->getGoals(),
                'created_at' => $campaign->getCreatedAt()->format('c'),
                'updated_at' => $campaign->getUpdatedAt()->format('c')
            ];
        }

        return $this->json([
            'data' => $campaignData,
            'pagination' => [
                'page' => $page,
                'per_page' => $perPage,
                'total' => $totalCampaigns,
                'pages' => ceil($totalCampaigns / $perPage)
            ]
        ]);
    }

    #[Route('', name: 'api_v1_campaigns_create', methods: ['POST'])]
    #[IsGranted('ROLE_AGENCY_ADMIN')]
    public function createCampaign(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            
            if (!$data) {
                return $this->json(['error' => 'Invalid JSON'], Response::HTTP_BAD_REQUEST);
            }

            // Validate input
            $constraints = new Assert\Collection([
                'name' => [new Assert\NotBlank()],
                'client_id' => [new Assert\NotBlank(), new Assert\Uuid()],
                'type' => [new Assert\NotBlank(), new Assert\Choice(['SEO', 'PPC', 'Social', 'Email', 'Content'])],
                'start_date' => [new Assert\Optional([new Assert\Date()])],
                'end_date' => [new Assert\Optional([new Assert\Date()])],
                'budget' => [new Assert\Optional([new Assert\Positive()])],
                'goals' => [new Assert\Optional([new Assert\Type('array')])]
            ]);

            $violations = $this->validator->validate($data, $constraints);
            if (count($violations) > 0) {
                $errors = [];
                foreach ($violations as $violation) {
                    $errors[$violation->getPropertyPath()] = $violation->getMessage();
                }
                return $this->json(['error' => 'Validation failed', 'details' => $errors], Response::HTTP_BAD_REQUEST);
            }

            // Create campaign
            $campaign = new Campaign();
            $campaign->setName($data['name']);
            $campaign->setClientId($data['client_id']);
            $campaign->setType($data['type']);
            $campaign->setStatus('active');

            if (isset($data['start_date'])) {
                $campaign->setStartDate(new \DateTimeImmutable($data['start_date']));
            }

            if (isset($data['end_date'])) {
                $campaign->setEndDate(new \DateTimeImmutable($data['end_date']));
            }

            if (isset($data['budget'])) {
                $campaign->setBudget($data['budget']);
            }

            if (isset($data['goals'])) {
                $campaign->setGoals($data['goals']);
            }

            $this->entityManager->persist($campaign);
            $this->entityManager->flush();

            $campaignData = [
                'id' => $campaign->getId(),
                'name' => $campaign->getName(),
                'client_id' => $campaign->getClientId(),
                'type' => $campaign->getType(),
                'status' => $campaign->getStatus(),
                'start_date' => $campaign->getStartDate()?->format('Y-m-d'),
                'end_date' => $campaign->getEndDate()?->format('Y-m-d'),
                'budget' => $campaign->getBudget(),
                'goals' => $campaign->getGoals(),
                'created_at' => $campaign->getCreatedAt()->format('c'),
                'updated_at' => $campaign->getUpdatedAt()->format('c')
            ];

            return $this->json([
                'message' => 'Campaign created successfully',
                'campaign' => $campaignData
            ], Response::HTTP_CREATED);

        } catch (\Exception $e) {
            return $this->json(['error' => 'Internal server error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/{id}', name: 'api_v1_campaigns_get', methods: ['GET'])]
    #[IsGranted('ROLE_AGENCY_ADMIN')]
    public function getCampaign(string $id): JsonResponse
    {
        if (!Uuid::isValid($id)) {
            return $this->json(['error' => 'Invalid UUID'], Response::HTTP_BAD_REQUEST);
        }

        $campaign = $this->campaignRepository->find($id);
        if (!$campaign) {
            return $this->json(['error' => 'Campaign not found'], Response::HTTP_NOT_FOUND);
        }

        $campaignData = [
            'id' => $campaign->getId(),
            'name' => $campaign->getName(),
            'client_id' => $campaign->getClientId(),
            'type' => $campaign->getType(),
            'status' => $campaign->getStatus(),
            'start_date' => $campaign->getStartDate()?->format('Y-m-d'),
            'end_date' => $campaign->getEndDate()?->format('Y-m-d'),
            'budget' => $campaign->getBudget(),
            'goals' => $campaign->getGoals(),
            'metadata' => $campaign->getMetadata(),
            'created_at' => $campaign->getCreatedAt()->format('c'),
            'updated_at' => $campaign->getUpdatedAt()->format('c')
        ];

        return $this->json($campaignData);
    }
}
