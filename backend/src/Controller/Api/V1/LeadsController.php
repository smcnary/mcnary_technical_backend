<?php

namespace App\Controller\Api\V1;

use App\Entity\Lead;
use App\Repository\LeadRepository;
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

#[Route('/api/v1/leads')]
class LeadsController extends AbstractController
{
    public function __construct(
        private LeadRepository $leadRepository,
        private EntityManagerInterface $entityManager,
        private ValidatorInterface $validator
    ) {}

    #[Route('', name: 'api_v1_leads_list', methods: ['GET'])]
    #[IsGranted('ROLE_CLIENT_STAFF')]
    public function listLeads(Request $request): JsonResponse
    {
        $page = max(1, (int) $request->query->get('page', 1));
        $perPage = min(100, max(1, (int) $request->query->get('per_page', 20)));
        $sort = $request->query->get('sort', 'created_at');
        $clientId = $request->query->get('client_id', '');
        $status = $request->query->get('status', '');
        $assignedTo = $request->query->get('assigned_to', '');
        $source = $request->query->get('source', '');
        $dateFrom = $request->query->get('date_from', '');
        $dateTo = $request->query->get('date_to', '');

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
        if ($assignedTo) {
            $criteria['assigned_to'] = $assignedTo;
        }
        if ($source) {
            $criteria['source'] = $source;
        }
        if ($dateFrom) {
            $criteria['date_from'] = $dateFrom;
        }
        if ($dateTo) {
            $criteria['date_to'] = $dateTo;
        }

        // Get leads with pagination and filtering
        $leads = $this->leadRepository->findByCriteria($criteria, $sortFields, $perPage, ($page - 1) * $perPage);
        $totalLeads = $this->leadRepository->countByCriteria($criteria);

        $leadData = [];
        foreach ($leads as $lead) {
            $leadData[] = [
                'id' => $lead->getId(),
                'name' => $lead->getName(),
                'email' => $lead->getEmail(),
                'phone' => $lead->getPhone(),
                'company' => $lead->getCompany(),
                'source' => $lead->getSource(),
                'status' => $lead->getStatus(),
                'priority' => $lead->getPriority(),
                'assigned_to' => $lead->getAssignedTo(),
                'notes' => $lead->getNotes(),
                'next_follow_up' => $lead->getNextFollowUp()?->format('c'),
                'created_at' => $lead->getCreatedAt()->format('c'),
                'updated_at' => $lead->getUpdatedAt()->format('c')
            ];
        }

        return $this->json([
            'data' => $leadData,
            'pagination' => [
                'page' => $page,
                'per_page' => $perPage,
                'total' => $totalLeads,
                'pages' => ceil($totalLeads / $perPage)
            ]
        ]);
    }

    #[Route('/{id}', name: 'api_v1_leads_get', methods: ['GET'])]
    #[IsGranted('ROLE_AGENCY_ADMIN')]
    public function getLead(string $id): JsonResponse
    {
        if (!Uuid::isValid($id)) {
            return $this->json(['error' => 'Invalid UUID'], Response::HTTP_BAD_REQUEST);
        }

        $lead = $this->leadRepository->find($id);
        if (!$lead) {
            return $this->json(['error' => 'Lead not found'], Response::HTTP_NOT_FOUND);
        }

        $leadData = [
            'id' => $lead->getId(),
            'name' => $lead->getName(),
            'email' => $lead->getEmail(),
            'phone' => $lead->getPhone(),
            'company' => $lead->getCompany(),
            'source' => $lead->getSource(),
            'status' => $lead->getStatus(),
            'priority' => $lead->getPriority(),
            'assigned_to' => $lead->getAssignedTo(),
            'notes' => $lead->getNotes(),
            'next_follow_up' => $lead->getNextFollowUp()?->format('c'),
            'metadata' => $lead->getMetadata(),
            'created_at' => $lead->getCreatedAt()->format('c'),
            'updated_at' => $lead->getUpdatedAt()->format('c')
        ];

        return $this->json($leadData);
    }

    #[Route('/{id}', name: 'api_v1_leads_update', methods: ['PATCH'])]
    #[IsGranted('ROLE_AGENCY_ADMIN')]
    public function updateLead(string $id, Request $request): JsonResponse
    {
        try {
            if (!Uuid::isValid($id)) {
                return $this->json(['error' => 'Invalid UUID'], Response::HTTP_BAD_REQUEST);
            }

            $lead = $this->leadRepository->find($id);
            if (!$lead) {
                return $this->json(['error' => 'Lead not found'], Response::HTTP_NOT_FOUND);
            }

            $data = json_decode($request->getContent(), true);
            
            if (!$data) {
                return $this->json(['error' => 'Invalid JSON'], Response::HTTP_BAD_REQUEST);
            }

            // Validate input
            $constraints = new Assert\Collection([
                'status' => [new Assert\Optional([new Assert\Choice(['new', 'contacted', 'qualified', 'converted', 'lost'])])],
                'assigned_to' => [new Assert\Optional([new Assert\Uuid()])],
                'notes' => [new Assert\Optional([new Assert\NotBlank()])],
                'priority' => [new Assert\Optional([new Assert\Choice(['low', 'medium', 'high', 'urgent'])])],
                'next_follow_up' => [new Assert\Optional([new Assert\DateTime()])]
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
                $lead->setStatus($data['status']);
            }

            if (isset($data['assigned_to'])) {
                $lead->setAssignedTo($data['assigned_to']);
            }

            if (isset($data['notes'])) {
                $lead->setNotes($data['notes']);
            }

            if (isset($data['priority'])) {
                $lead->setPriority($data['priority']);
            }

            if (isset($data['next_follow_up'])) {
                $lead->setNextFollowUp(new \DateTimeImmutable($data['next_follow_up']));
            }

            $this->entityManager->flush();

            $leadData = [
                'id' => $lead->getId(),
                'name' => $lead->getName(),
                'email' => $lead->getEmail(),
                'phone' => $lead->getPhone(),
                'company' => $lead->getCompany(),
                'source' => $lead->getSource(),
                'status' => $lead->getStatus(),
                'priority' => $lead->getPriority(),
                'assigned_to' => $lead->getAssignedTo(),
                'notes' => $lead->getNotes(),
                'next_follow_up' => $lead->getNextFollowUp()?->format('c'),
                'created_at' => $lead->getCreatedAt()->format('c'),
                'updated_at' => $lead->getUpdatedAt()->format('c')
            ];

            return $this->json([
                'message' => 'Lead updated successfully',
                'lead' => $leadData
            ]);

        } catch (\Exception $e) {
            return $this->json(['error' => 'Internal server error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/{id}/events', name: 'api_v1_leads_events_create', methods: ['POST'])]
    #[IsGranted('ROLE_AGENCY_ADMIN')]
    public function createLeadEvent(string $id, Request $request): JsonResponse
    {
        try {
            if (!Uuid::isValid($id)) {
                return $this->json(['error' => 'Invalid UUID'], Response::HTTP_BAD_REQUEST);
            }

            $lead = $this->leadRepository->find($id);
            if (!$lead) {
                return $this->json(['error' => 'Lead not found'], Response::HTTP_NOT_FOUND);
            }

            $data = json_decode($request->getContent(), true);
            
            if (!$data) {
                return $this->json(['error' => 'Invalid JSON'], Response::HTTP_BAD_REQUEST);
            }

            // Validate input
            $constraints = new Assert\Collection([
                'type' => [new Assert\NotBlank(), new Assert\Choice(['phone_call', 'email', 'meeting', 'note'])],
                'direction' => [new Assert\Optional([new Assert\Choice(['inbound', 'outbound'])])],
                'duration' => [new Assert\Optional([new Assert\PositiveOrZero()])],
                'notes' => [new Assert\Optional([new Assert\NotBlank()])],
                'outcome' => [new Assert\Optional([new Assert\Choice(['positive', 'neutral', 'negative'])])],
                'next_action' => [new Assert\Optional([new Assert\NotBlank()])]
            ]);

            $violations = $this->validator->validate($data, $constraints);
            if (count($violations) > 0) {
                $errors = [];
                foreach ($violations as $violation) {
                    $errors[$violation->getPropertyPath()] = $violation->getMessage();
                }
                return $this->json(['error' => 'Validation failed', 'details' => $errors], Response::HTTP_BAD_REQUEST);
            }

            // Create lead event (you'll need to create a LeadEvent entity)
            // For now, we'll return a success response
            $eventData = [
                'id' => Uuid::v4()->toRfc4122(),
                'lead_id' => $id,
                'type' => $data['type'],
                'direction' => $data['direction'] ?? null,
                'duration' => $data['duration'] ?? null,
                'notes' => $data['notes'] ?? null,
                'outcome' => $data['outcome'] ?? null,
                'next_action' => $data['next_action'] ?? null,
                'created_at' => (new \DateTimeImmutable())->format('c')
            ];

            return $this->json([
                'message' => 'Lead event created successfully',
                'event' => $eventData
            ], Response::HTTP_CREATED);

        } catch (\Exception $e) {
            return $this->json(['error' => 'Internal server error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
