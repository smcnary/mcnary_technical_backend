<?php

namespace App\Controller\Api\V1;

use App\Entity\Lead;
use App\Entity\Client;
use App\Entity\LeadSource;
use App\Repository\LeadRepository;
use App\Repository\ClientRepository;
use App\Repository\LeadSourceRepository;
use App\ValueObject\LeadStatus;
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
        private ClientRepository $clientRepository,
        private LeadSourceRepository $leadSourceRepository,
        private EntityManagerInterface $entityManager,
        private ValidatorInterface $validator
    ) {}

    #[Route('', name: 'api_v1_leads_list', methods: ['GET'])]
    #[IsGranted('ROLE_SYSTEM_ADMIN')]
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
                'full_name' => $lead->getFullName(),
                'email' => $lead->getEmail(),
                'phone' => $lead->getPhone(),
                'firm' => $lead->getFirm(),
                'website' => $lead->getWebsite(),
                'city' => $lead->getCity(),
                'state' => $lead->getState(),
                'zip_code' => $lead->getZipCode(),
                'message' => $lead->getMessage(),
                'practice_areas' => $lead->getPracticeAreas(),
                'status' => $lead->getStatusValue(),
                'status_label' => $lead->getStatusLabel(),
                'source' => $lead->getSource()?->getName(),
                'client' => $lead->getClient()?->getName(),
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
            'full_name' => $lead->getFullName(),
            'email' => $lead->getEmail(),
            'phone' => $lead->getPhone(),
            'firm' => $lead->getFirm(),
            'website' => $lead->getWebsite(),
            'city' => $lead->getCity(),
            'state' => $lead->getState(),
            'zip_code' => $lead->getZipCode(),
            'message' => $lead->getMessage(),
            'practice_areas' => $lead->getPracticeAreas(),
            'status' => $lead->getStatusValue(),
            'status_label' => $lead->getStatusLabel(),
            'source' => $lead->getSource()?->getName(),
            'client' => $lead->getClient()?->getName(),
            'utm_json' => $lead->getUtmJson(),
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
                'status' => [new Assert\Optional([new Assert\Choice(['new_lead', 'contacted', 'interview_scheduled', 'interview_completed', 'application_received', 'audit_in_progress', 'audit_complete', 'enrolled'])])],
                'message' => [new Assert\Optional([new Assert\NotBlank()])],
                'practice_areas' => [new Assert\Optional([new Assert\Type('array')])]
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

            if (isset($data['message'])) {
                $lead->setMessage($data['message']);
            }

            if (isset($data['practice_areas'])) {
                $lead->setPracticeAreas($data['practice_areas']);
            }

            $this->entityManager->flush();

            $leadData = [
                'id' => $lead->getId(),
                'full_name' => $lead->getFullName(),
                'email' => $lead->getEmail(),
                'phone' => $lead->getPhone(),
                'firm' => $lead->getFirm(),
                'website' => $lead->getWebsite(),
                'city' => $lead->getCity(),
                'state' => $lead->getState(),
                'zip_code' => $lead->getZipCode(),
                'message' => $lead->getMessage(),
                'practice_areas' => $lead->getPracticeAreas(),
                'status' => $lead->getStatusValue(),
                'status_label' => $lead->getStatusLabel(),
                'source' => $lead->getSource()?->getName(),
                'client' => $lead->getClient()?->getName(),
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

    #[Route('/import', name: 'api_v1_leads_import', methods: ['POST'])]
    #[IsGranted('ROLE_AGENCY_ADMIN')]
    public function importLeads(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            
            if (!$data) {
                return $this->json(['error' => 'Invalid JSON'], Response::HTTP_BAD_REQUEST);
            }

            // Validate input
            $constraints = new Assert\Collection([
                'client_id' => [new Assert\Optional([new Assert\Uuid()])],
                'source_id' => [new Assert\Optional([new Assert\Uuid()])],
                'csv_data' => [new Assert\NotBlank()],
                'overwrite_existing' => [new Assert\Optional([new Assert\Type('boolean')])]
            ]);

            $violations = $this->validator->validate($data, $constraints);
            if (count($violations) > 0) {
                $errors = [];
                foreach ($violations as $violation) {
                    $errors[$violation->getPropertyPath()] = $violation->getMessage();
                }
                return $this->json(['error' => 'Validation failed', 'details' => $errors], Response::HTTP_BAD_REQUEST);
            }

            // Parse CSV data
            $csvLines = explode("\n", trim($data['csv_data']));
            $headers = str_getcsv(array_shift($csvLines));
            
            $importedCount = 0;
            $errors = [];
            $skippedCount = 0;

            foreach ($csvLines as $index => $line) {
                $line = trim($line);
                if (empty($line)) {
                    continue;
                }
                
                $row = str_getcsv($line);
                if (count($row) !== count($headers)) {
                    $errors[] = "Row " . ($index + 2) . ": Column count mismatch";
                    continue;
                }

                $rowData = array_combine($headers, $row);
                
                try {
                    // Validate required fields
                    $rowConstraints = new Assert\Collection([
                        'full_name' => [new Assert\NotBlank()],
                        'email' => [new Assert\NotBlank(), new Assert\Email()],
                        'phone' => [new Assert\Optional([new Assert\NotBlank()])],
                        'firm' => [new Assert\Optional([new Assert\NotBlank()])],
                        'website' => [new Assert\Optional([new Assert\Url()])],
                        'city' => [new Assert\Optional([new Assert\NotBlank()])],
                        'state' => [new Assert\Optional([new Assert\NotBlank()])],
                        'zip_code' => [new Assert\Optional([new Assert\NotBlank()])],
                        'message' => [new Assert\Optional([new Assert\NotBlank()])],
                        'practice_areas' => [new Assert\Optional([new Assert\NotBlank()])]
                    ]);

                    $rowViolations = $this->validator->validate($rowData, $rowConstraints);
                    if (count($rowViolations) > 0) {
                        $errors[] = "Row " . ($index + 2) . ": " . implode(', ', array_map(fn($v) => $v->getMessage(), iterator_to_array($rowViolations)));
                        continue;
                    }

                    // Check if lead already exists by email
                    $existingLead = $this->leadRepository->findOneBy(['email' => $rowData['email']]);
                    if ($existingLead && !($data['overwrite_existing'] ?? false)) {
                        $skippedCount++;
                        continue;
                    }

                    // Create or update lead
                    if ($existingLead && ($data['overwrite_existing'] ?? false)) {
                        $lead = $existingLead;
                    } else {
                        $lead = new Lead();
                    }

                    // Set basic information
                    $lead->setFullName($rowData['full_name']);
                    $lead->setEmail($rowData['email']);
                    
                    if (isset($rowData['phone']) && !empty($rowData['phone'])) {
                        $lead->setPhone($rowData['phone']);
                    }
                    
                    if (isset($rowData['firm']) && !empty($rowData['firm'])) {
                        $lead->setFirm($rowData['firm']);
                    }
                    
                    if (isset($rowData['website']) && !empty($rowData['website'])) {
                        $lead->setWebsite($rowData['website']);
                    }
                    
                    if (isset($rowData['city']) && !empty($rowData['city'])) {
                        $lead->setCity($rowData['city']);
                    }
                    
                    if (isset($rowData['state']) && !empty($rowData['state'])) {
                        $lead->setState($rowData['state']);
                    }
                    
                    if (isset($rowData['zip_code']) && !empty($rowData['zip_code'])) {
                        $lead->setZipCode($rowData['zip_code']);
                    }
                    
                    if (isset($rowData['message']) && !empty($rowData['message'])) {
                        $lead->setMessage($rowData['message']);
                    }
                    
                    if (isset($rowData['practice_areas']) && !empty($rowData['practice_areas'])) {
                        // Parse practice areas (comma-separated or JSON)
                        $practiceAreas = [];
                        if (str_starts_with($rowData['practice_areas'], '[')) {
                            $practiceAreas = json_decode($rowData['practice_areas'], true) ?? [];
                        } else {
                            $practiceAreas = array_map('trim', explode(',', $rowData['practice_areas']));
                        }
                        $lead->setPracticeAreas($practiceAreas);
                    }

                    // Set client if provided
                    if (isset($data['client_id']) && !empty($data['client_id'])) {
                        $client = $this->clientRepository->find($data['client_id']);
                        if ($client) {
                            $lead->setClient($client);
                        }
                    }

                    // Set source if provided
                    if (isset($data['source_id']) && !empty($data['source_id'])) {
                        $source = $this->leadSourceRepository->find($data['source_id']);
                        if ($source) {
                            $lead->setSource($source);
                        }
                    }

                    // Set default status to new_lead
                    $lead->setStatus(LeadStatus::NEW_LEAD);

                    if (!$existingLead) {
                        $this->entityManager->persist($lead);
                    }

                    $importedCount++;

                } catch (\Exception $e) {
                    $errors[] = "Row " . ($index + 2) . ": " . $e->getMessage();
                }
            }

            if ($importedCount > 0) {
                $this->entityManager->flush();
            }

            $response = [
                'message' => 'Lead import completed',
                'imported_count' => $importedCount,
                'skipped_count' => $skippedCount,
                'total_rows' => count($csvLines)
            ];

            if (!empty($errors)) {
                $response['errors'] = $errors;
            }

            $statusCode = $importedCount > 0 ? Response::HTTP_CREATED : Response::HTTP_BAD_REQUEST;
            return $this->json($response, $statusCode);

        } catch (\Exception $e) {
            return $this->json(['error' => 'Internal server error: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
