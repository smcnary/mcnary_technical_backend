<?php

namespace App\Controller\Api\V1;

use App\Entity\Lead;
use App\Entity\Client;
use App\Entity\LeadSource;
use App\Entity\LeadEvent;
use App\Repository\LeadRepository;
use App\Repository\ClientRepository;
use App\Repository\LeadSourceRepository;
use App\Repository\LeadEventRepository;
use App\Service\LeadgenIntegrationService;
use App\Service\TechStackService;
use App\Service\GoogleSheetsService;
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
        private LeadEventRepository $leadEventRepository,
        private LeadgenIntegrationService $leadgenIntegrationService,
        private TechStackService $techStackService,
        private GoogleSheetsService $googleSheetsService,
        private EntityManagerInterface $entityManager,
        private ValidatorInterface $validator
    ) {}

    #[Route('/simple', name: 'api_v1_leads_simple_list', methods: ['GET'])]
    #[IsGranted('ROLE_SYSTEM_ADMIN')]
    public function listLeadsSimple(Request $request): JsonResponse
    {
        // Simple endpoint that bypasses problematic serialized fields
        $page = max(1, (int) $request->query->get('page', 1));
        $perPage = min(100, max(1, (int) $request->query->get('per_page', 20)));
        
        // Use direct SQL query to avoid Doctrine serialization issues
        $connection = $this->entityManager->getConnection();
        
        $offset = ($page - 1) * $perPage;
        $query = "
            SELECT 
                id,
                full_name,
                email,
                phone,
                firm,
                website,
                city,
                state,
                zip_code,
                message,
                status,
                created_at,
                updated_at,
                s.name as source_name
            FROM leads l
            LEFT JOIN lead_sources s ON l.source_id = s.id
            ORDER BY l.created_at DESC
            LIMIT :perPage OFFSET :offset
        ";
        
        $stmt = $connection->prepare($query);
        $stmt->bindValue('perPage', $perPage);
        $stmt->bindValue('offset', $offset);
        $result = $stmt->executeQuery();
        $leads = $result->fetchAllAssociative();
        
        // Get total count
        $countQuery = "SELECT COUNT(*) FROM leads";
        $totalLeads = $connection->executeQuery($countQuery)->fetchOne();
        
        $leadData = [];
        foreach ($leads as $lead) {
            $leadData[] = [
                'id' => $lead['id'],
                'full_name' => $lead['full_name'],
                'email' => $lead['email'],
                'phone' => $lead['phone'],
                'firm' => $lead['firm'],
                'website' => $lead['website'],
                'city' => $lead['city'],
                'state' => $lead['state'],
                'zip_code' => $lead['zip_code'],
                'message' => $lead['message'],
                'practice_areas' => ['attorney', 'lawyer', 'legal services'], // Default values
                'status' => $lead['status'],
                'status_label' => ucfirst(str_replace('_', ' ', $lead['status'])),
                'source' => $lead['source_name'],
                'client' => null,
                'created_at' => $lead['created_at'],
                'updated_at' => $lead['updated_at']
            ];
        }

        return $this->json([
            'data' => $leadData,
            'pagination' => [
                'page' => $page,
                'per_page' => $perPage,
                'total' => (int) $totalLeads,
                'pages' => ceil($totalLeads / $perPage)
            ]
        ]);
    }

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
            'interview_scheduled' => $lead->getInterviewScheduled()?->format('c'),
            'follow_up_date' => $lead->getFollowUpDate()?->format('c'),
            'notes' => $lead->getNotes(),
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
                'fullName' => [new Assert\Optional([new Assert\NotBlank()])],
                'email' => [new Assert\Optional([new Assert\Email()])],
                'phone' => [new Assert\Optional([new Assert\Type('string')])],
                'firm' => [new Assert\Optional([new Assert\Type('string')])],
                'website' => [new Assert\Optional([new Assert\Url()])],
                'city' => [new Assert\Optional([new Assert\Type('string')])],
                'state' => [new Assert\Optional([new Assert\Type('string')])],
                'zipCode' => [new Assert\Optional([new Assert\Type('string')])],
                'status' => [new Assert\Optional([new Assert\Choice(['new_lead', 'contacted', 'interview_scheduled', 'interview_completed', 'application_received', 'audit_in_progress', 'audit_complete', 'enrolled'])])],
                'message' => [new Assert\Optional([new Assert\Type('string')])],
                'practiceAreas' => [new Assert\Optional([new Assert\Type('array')])],
                'interviewScheduled' => [new Assert\Optional([new Assert\Type('string')])],
                'followUpDate' => [new Assert\Optional([new Assert\Type('string')])],
                'notes' => [new Assert\Optional([new Assert\Type('string')])],
                // Allow extra fields from frontend that aren't stored in backend
                'budget' => [new Assert\Optional()],
                'timeline' => [new Assert\Optional()],
                'consent' => [new Assert\Optional()],
                'statusLabel' => [new Assert\Optional()],
                'source' => [new Assert\Optional()],
                'client' => [new Assert\Optional()],
                'utmJson' => [new Assert\Optional()],
                'techStack' => [new Assert\Optional()],
                'createdAt' => [new Assert\Optional()],
                'updatedAt' => [new Assert\Optional()]
            ], [
                'allowExtraFields' => true
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
            if (isset($data['fullName'])) {
                $lead->setFullName($data['fullName']);
            }

            if (isset($data['email'])) {
                $lead->setEmail($data['email']);
            }

            if (isset($data['phone'])) {
                $lead->setPhone($data['phone']);
            }

            if (isset($data['firm'])) {
                $lead->setFirm($data['firm']);
            }

            if (isset($data['website'])) {
                $lead->setWebsite($data['website']);
            }

            if (isset($data['city'])) {
                $lead->setCity($data['city']);
            }

            if (isset($data['state'])) {
                $lead->setState($data['state']);
            }

            if (isset($data['zipCode'])) {
                $lead->setZipCode($data['zipCode']);
            }

            if (isset($data['status'])) {
                $lead->setStatus($data['status']);
            }

            if (isset($data['message'])) {
                $lead->setMessage($data['message']);
            }

            if (isset($data['practiceAreas'])) {
                $lead->setPracticeAreas($data['practiceAreas']);
            }

            if (isset($data['interviewScheduled'])) {
                if (!empty($data['interviewScheduled'])) {
                    $lead->setInterviewScheduled(new \DateTimeImmutable($data['interviewScheduled']));
                } else {
                    $lead->setInterviewScheduled(null);
                }
            }

            if (isset($data['followUpDate'])) {
                if (!empty($data['followUpDate'])) {
                    $lead->setFollowUpDate(new \DateTimeImmutable($data['followUpDate']));
                } else {
                    $lead->setFollowUpDate(null);
                }
            }

            if (isset($data['notes'])) {
                $lead->setNotes($data['notes']);
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
                'interview_scheduled' => $lead->getInterviewScheduled()?->format('c'),
                'follow_up_date' => $lead->getFollowUpDate()?->format('c'),
                'notes' => $lead->getNotes(),
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

    #[Route('/{id}/notes', name: 'api_v1_leads_notes_get', methods: ['GET'])]
    #[IsGranted('ROLE_AGENCY_ADMIN')]
    public function getLeadNotes(string $id): JsonResponse
    {
        try {
            if (!Uuid::isValid($id)) {
                return $this->json(['error' => 'Invalid UUID'], Response::HTTP_BAD_REQUEST);
            }

            $lead = $this->leadRepository->find($id);
            if (!$lead) {
                return $this->json(['error' => 'Lead not found'], Response::HTTP_NOT_FOUND);
            }

            return $this->json([
                'notes' => $lead->getNotes() ?? ''
            ]);

        } catch (\Exception $e) {
            return $this->json(['error' => 'Internal server error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/{id}/notes', name: 'api_v1_leads_notes_save', methods: ['POST'])]
    #[IsGranted('ROLE_AGENCY_ADMIN')]
    public function saveLeadNotes(string $id, Request $request): JsonResponse
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
                'notes' => [new Assert\Optional([new Assert\Type('string')])]
            ]);

            $violations = $this->validator->validate($data, $constraints);
            if (count($violations) > 0) {
                $errors = [];
                foreach ($violations as $violation) {
                    $errors[$violation->getPropertyPath()] = $violation->getMessage();
                }
                return $this->json(['error' => 'Validation failed', 'details' => $errors], Response::HTTP_BAD_REQUEST);
            }

            // Update notes
            if (isset($data['notes'])) {
                $lead->setNotes($data['notes']);
            }

            $this->entityManager->flush();

            return $this->json([
                'message' => 'Notes saved successfully',
                'notes' => $lead->getNotes() ?? ''
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

            $data = json_decode($request->getContent(), true);
            
            if (!$data) {
                return $this->json(['error' => 'Invalid JSON'], Response::HTTP_BAD_REQUEST);
            }

            // Validate input
            $constraints = new Assert\Collection([
                'type' => [new Assert\NotBlank(), new Assert\Choice(['phone_call', 'email', 'meeting', 'note', 'application'])],
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

            $event = $this->leadgenIntegrationService->trackLeadStatistics($id, $data['type'], $data);

            $eventData = [
                'id' => $event->getId(),
                'lead_id' => $id,
                'type' => $event->getType(),
                'type_label' => $event->getTypeLabel(),
                'direction' => $event->getDirection(),
                'direction_label' => $event->getDirectionLabel(),
                'duration' => $event->getDuration(),
                'notes' => $event->getNotes(),
                'outcome' => $event->getOutcome(),
                'outcome_label' => $event->getOutcomeLabel(),
                'next_action' => $event->getNextAction(),
                'created_at' => $event->getCreatedAt()->format('c')
            ];

            return $this->json([
                'message' => 'Lead event created successfully',
                'event' => $eventData
            ], Response::HTTP_CREATED);

        } catch (\Exception $e) {
            return $this->json(['error' => 'Internal server error: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
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

    #[Route('/leadgen-import', name: 'api_v1_leads_leadgen_import', methods: ['POST'])]
    #[IsGranted('ROLE_AGENCY_ADMIN')]
    public function importLeadgenData(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            
            if (!$data) {
                return $this->json(['error' => 'Invalid JSON'], Response::HTTP_BAD_REQUEST);
            }

            // Validate input
            $constraints = new Assert\Collection([
                'leads' => [new Assert\NotBlank(), new Assert\Type('array')],
                'client_id' => [new Assert\Optional([new Assert\Uuid()])],
                'source_id' => [new Assert\Optional([new Assert\Uuid()])]
            ]);

            $violations = $this->validator->validate($data, $constraints);
            if (count($violations) > 0) {
                $errors = [];
                foreach ($violations as $violation) {
                    $errors[$violation->getPropertyPath()] = $violation->getMessage();
                }
                return $this->json(['error' => 'Validation failed', 'details' => $errors], Response::HTTP_BAD_REQUEST);
            }

            $result = $this->leadgenIntegrationService->importLeadgenData(
                $data['leads'],
                $data['client_id'] ?? null,
                $data['source_id'] ?? null
            );

            $response = [
                'message' => 'Leadgen data import completed',
                'imported' => $result['imported'],
                'updated' => $result['updated'],
                'skipped' => $result['skipped'],
                'total_processed' => count($data['leads'])
            ];

            if (!empty($result['errors'])) {
                $response['errors'] = $result['errors'];
            }

            $statusCode = $result['imported'] > 0 || $result['updated'] > 0 ? Response::HTTP_CREATED : Response::HTTP_BAD_REQUEST;
            return $this->json($response, $statusCode);

        } catch (\Exception $e) {
            return $this->json(['error' => 'Internal server error: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/{id}/events', name: 'api_v1_leads_events_list', methods: ['GET'])]
    #[IsGranted('ROLE_AGENCY_ADMIN')]
    public function getLeadEvents(string $id): JsonResponse
    {
        if (!Uuid::isValid($id)) {
            return $this->json(['error' => 'Invalid UUID'], Response::HTTP_BAD_REQUEST);
        }

        $lead = $this->leadRepository->find($id);
        if (!$lead) {
            return $this->json(['error' => 'Lead not found'], Response::HTTP_NOT_FOUND);
        }

        $events = $this->leadEventRepository->findByLead($id);
        
        $eventData = [];
        foreach ($events as $event) {
            $eventData[] = [
                'id' => $event->getId(),
                'type' => $event->getType(),
                'type_label' => $event->getTypeLabel(),
                'direction' => $event->getDirection(),
                'direction_label' => $event->getDirectionLabel(),
                'duration' => $event->getDuration(),
                'notes' => $event->getNotes(),
                'outcome' => $event->getOutcome(),
                'outcome_label' => $event->getOutcomeLabel(),
                'next_action' => $event->getNextAction(),
                'created_at' => $event->getCreatedAt()->format('c')
            ];
        }

        return $this->json([
            'lead_id' => $id,
            'events' => $eventData
        ]);
    }

    #[Route('/{id}/statistics', name: 'api_v1_leads_statistics', methods: ['GET'])]
    #[IsGranted('ROLE_AGENCY_ADMIN')]
    public function getLeadStatistics(string $id): JsonResponse
    {
        if (!Uuid::isValid($id)) {
            return $this->json(['error' => 'Invalid UUID'], Response::HTTP_BAD_REQUEST);
        }

        $lead = $this->leadRepository->find($id);
        if (!$lead) {
            return $this->json(['error' => 'Lead not found'], Response::HTTP_NOT_FOUND);
        }

        $statistics = $this->leadgenIntegrationService->getLeadStatistics($id);

        return $this->json([
            'lead_id' => $id,
            'statistics' => $statistics
        ]);
    }

    #[Route('/{id}/tech-stack', name: 'api_v1_leads_tech_stack_get', methods: ['GET'])]
    #[IsGranted('ROLE_AGENCY_ADMIN')]
    public function getLeadTechStack(string $id): JsonResponse
    {
        if (!Uuid::isValid($id)) {
            return $this->json(['error' => 'Invalid UUID'], Response::HTTP_BAD_REQUEST);
        }

        $lead = $this->leadRepository->find($id);
        if (!$lead) {
            return $this->json(['error' => 'Lead not found'], Response::HTTP_NOT_FOUND);
        }

        if (!$lead->getWebsite()) {
            return $this->json(['error' => 'Lead has no website'], Response::HTTP_BAD_REQUEST);
        }

        // For now, return empty tech stack - in a real implementation, you'd store this in the database
        $techStack = [
            'url' => $lead->getWebsite(),
            'technologies' => [],
            'lastAnalyzed' => null
        ];

        return $this->json([
            'techStack' => $techStack
        ]);
    }

    #[Route('/{id}/tech-stack', name: 'api_v1_leads_tech_stack_analyze', methods: ['POST'])]
    #[IsGranted('ROLE_AGENCY_ADMIN')]
    public function analyzeLeadTechStack(string $id): JsonResponse
    {
        if (!Uuid::isValid($id)) {
            return $this->json(['error' => 'Invalid UUID'], Response::HTTP_BAD_REQUEST);
        }

        $lead = $this->leadRepository->find($id);
        if (!$lead) {
            return $this->json(['error' => 'Lead not found'], Response::HTTP_NOT_FOUND);
        }

        if (!$lead->getWebsite()) {
            return $this->json(['error' => 'Lead has no website'], Response::HTTP_BAD_REQUEST);
        }

        try {
            // Analyze the website's technology stack
            $techStack = $this->techStackService->analyzeWebsite($lead->getWebsite());

            // In a real implementation, you would save this to the database
            // For now, we'll just return the result

            return $this->json([
                'techStack' => $techStack
            ]);

        } catch (\Exception $e) {
            return $this->json([
                'error' => 'Failed to analyze technology stack',
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/google-sheets-import', name: 'api_v1_leads_google_sheets_import', methods: ['POST'])]
    #[IsGranted('ROLE_AGENCY_ADMIN')]
    public function importFromGoogleSheets(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            
            if (!$data) {
                return $this->json(['error' => 'Invalid JSON'], Response::HTTP_BAD_REQUEST);
            }

            // Validate input
            $constraints = new Assert\Collection([
                'spreadsheet_url' => [new Assert\NotBlank()],
                'range' => [new Assert\Optional([new Assert\NotBlank()])],
                'client_id' => [new Assert\Optional([new Assert\Uuid()])],
                'source_id' => [new Assert\Optional([new Assert\Uuid()])],
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

            // Extract spreadsheet ID from URL
            $spreadsheetId = $this->googleSheetsService->extractSpreadsheetId($data['spreadsheet_url']);
            
            // Fetch sheet data using public CSV export
            $range = $data['range'] ?? 'A:Z'; // Default to all columns
            $sheetData = $this->googleSheetsService->fetchSheetData($spreadsheetId, $range);
            
            // Import leads
            $result = $this->googleSheetsService->importLeadsFromSheet(
                $sheetData,
                $data['client_id'] ?? null,
                $data['source_id'] ?? null,
                $data['overwrite_existing'] ?? false
            );

            $response = [
                'message' => 'Google Sheets import completed',
                'imported' => $result['imported'],
                'updated' => $result['updated'],
                'skipped' => $result['skipped'],
                'total_processed' => $result['imported'] + $result['updated'] + $result['skipped']
            ];

            if (!empty($result['errors'])) {
                $response['errors'] = $result['errors'];
            }

            $statusCode = $result['imported'] > 0 || $result['updated'] > 0 ? Response::HTTP_CREATED : Response::HTTP_BAD_REQUEST;
            return $this->json($response, $statusCode);

        } catch (\Exception $e) {
            return $this->json([
                'error' => 'Failed to import from Google Sheets',
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
