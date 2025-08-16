<?php

namespace App\Controller\Api\V1;

use App\Entity\Backlink;
use App\Repository\BacklinkRepository;
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

#[Route('/api/v1/backlinks')]
class BacklinksController extends AbstractController
{
    public function __construct(
        private BacklinkRepository $backlinkRepository,
        private EntityManagerInterface $entityManager,
        private ValidatorInterface $validator
    ) {}

    #[Route('', name: 'api_v1_backlinks_list', methods: ['GET'])]
    #[IsGranted('ROLE_AGENCY_ADMIN')]
    public function listBacklinks(Request $request): JsonResponse
    {
        $page = max(1, (int) $request->query->get('page', 1));
        $perPage = min(100, max(1, (int) $request->query->get('per_page', 20)));
        $sort = $request->query->get('sort', 'created_at');
        $clientId = $request->query->get('client_id', '');
        $status = $request->query->get('status', '');
        $qualityScore = $request->query->get('quality_score', '');
        $sourceDomain = $request->query->get('source_domain', '');

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
        if ($qualityScore) {
            $criteria['quality_score'] = (int) $qualityScore;
        }
        if ($sourceDomain) {
            $criteria['source_domain'] = $sourceDomain;
        }

        // Get backlinks with pagination and filtering
        $backlinks = $this->backlinkRepository->findByCriteria($criteria, $sortFields, $perPage, ($page - 1) * $perPage);
        $totalBacklinks = $this->backlinkRepository->countByCriteria($criteria);

        $backlinkData = [];
        foreach ($backlinks as $backlink) {
            $backlinkData[] = [
                'id' => $backlink->getId(),
                'source_url' => $backlink->getSourceUrl(),
                'source_domain' => $backlink->getSourceDomain(),
                'client_id' => $backlink->getClientId(),
                'status' => $backlink->getStatus(),
                'anchor_text' => $backlink->getAnchorText(),
                'quality_score' => $backlink->getQualityScore(),
                'outreach_notes' => $backlink->getOutreachNotes(),
                'date_acquired' => $backlink->getDateAcquired()?->format('Y-m-d'),
                'created_at' => $backlink->getCreatedAt()->format('c'),
                'updated_at' => $backlink->getUpdatedAt()->format('c')
            ];
        }

        return $this->json([
            'data' => $backlinkData,
            'pagination' => [
                'page' => $page,
                'per_page' => $perPage,
                'total' => $totalBacklinks,
                'pages' => ceil($totalBacklinks / $perPage)
            ]
        ]);
    }

    #[Route('', name: 'api_v1_backlinks_create', methods: ['POST'])]
    #[IsGranted('ROLE_AGENCY_ADMIN')]
    public function createBacklink(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            
            if (!$data) {
                return $this->json(['error' => 'Invalid JSON'], Response::HTTP_BAD_REQUEST);
            }

            // Validate input
            $constraints = new Assert\Collection([
                'source_url' => [new Assert\NotBlank(), new Assert\Url()],
                'source_domain' => [new Assert\NotBlank()],
                'client_id' => [new Assert\NotBlank(), new Assert\Uuid()],
                'status' => [new Assert\Optional([new Assert\Choice(['live', 'pending', 'lost'])])],
                'anchor_text' => [new Assert\Optional([new Assert\NotBlank()])],
                'quality_score' => [new Assert\Optional([new Assert\Range(['min' => 1, 'max' => 100])])],
                'outreach_notes' => [new Assert\Optional([new Assert\NotBlank()])],
                'date_acquired' => [new Assert\Optional([new Assert\Date()])]
            ]);

            $violations = $this->validator->validate($data, $constraints);
            if (count($violations) > 0) {
                $errors = [];
                foreach ($violations as $violation) {
                    $errors[$violation->getPropertyPath()] = $violation->getMessage();
                }
                return $this->json(['error' => 'Validation failed', 'details' => $errors], Response::HTTP_BAD_REQUEST);
            }

            // Create backlink
            $backlink = new Backlink();
            $backlink->setSourceUrl($data['source_url']);
            $backlink->setSourceDomain($data['source_domain']);
            $backlink->setClientId($data['client_id']);
            $backlink->setStatus($data['status'] ?? 'pending');

            if (isset($data['anchor_text'])) {
                $backlink->setAnchorText($data['anchor_text']);
            }

            if (isset($data['quality_score'])) {
                $backlink->setQualityScore($data['quality_score']);
            }

            if (isset($data['outreach_notes'])) {
                $backlink->setOutreachNotes($data['outreach_notes']);
            }

            if (isset($data['date_acquired'])) {
                $backlink->setDateAcquired(new \DateTimeImmutable($data['date_acquired']));
            }

            $this->entityManager->persist($backlink);
            $this->entityManager->flush();

            $backlinkData = [
                'id' => $backlink->getId(),
                'source_url' => $backlink->getSourceUrl(),
                'source_domain' => $backlink->getSourceDomain(),
                'client_id' => $backlink->getClientId(),
                'status' => $backlink->getStatus(),
                'anchor_text' => $backlink->getAnchorText(),
                'quality_score' => $backlink->getQualityScore(),
                'outreach_notes' => $backlink->getOutreachNotes(),
                'date_acquired' => $backlink->getDateAcquired()?->format('Y-m-d'),
                'created_at' => $backlink->getCreatedAt()->format('c'),
                'updated_at' => $backlink->getUpdatedAt()->format('c')
            ];

            return $this->json([
                'message' => 'Backlink created successfully',
                'backlink' => $backlinkData
            ], Response::HTTP_CREATED);

        } catch (\Exception $e) {
            return $this->json(['error' => 'Internal server error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/import', name: 'api_v1_backlinks_import', methods: ['POST'])]
    #[IsGranted('ROLE_AGENCY_ADMIN')]
    public function importBacklinks(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            
            if (!$data) {
                return $this->json(['error' => 'Invalid JSON'], Response::HTTP_BAD_REQUEST);
            }

            // Validate input
            $constraints = new Assert\Collection([
                'client_id' => [new Assert\NotBlank(), new Assert\Uuid()],
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

            foreach ($csvLines as $index => $line) {
                if (empty(trim($line))) continue;
                
                $row = str_getcsv($line);
                if (count($row) !== count($headers)) {
                    $errors[] = "Row " . ($index + 2) . ": Column count mismatch";
                    continue;
                }

                $rowData = array_combine($headers, $row);
                
                try {
                    // Validate row data
                    $rowConstraints = new Assert\Collection([
                        'source_url' => [new Assert\NotBlank(), new Assert\Url()],
                        'source_domain' => [new Assert\NotBlank()],
                        'anchor_text' => [new Assert\Optional([new Assert\NotBlank()])],
                        'status' => [new Assert\Optional([new Assert\Choice(['live', 'pending', 'lost'])])]
                    ]);

                    $rowViolations = $this->validator->validate($rowData, $rowConstraints);
                    if (count($rowViolations) > 0) {
                        $errors[] = "Row " . ($index + 2) . ": " . implode(', ', array_map(fn($v) => $v->getMessage(), iterator_to_array($rowViolations)));
                        continue;
                    }

                    // Check if backlink already exists
                    $existingBacklink = $this->backlinkRepository->findBySourceUrl($rowData['source_url']);
                    if ($existingBacklink && !($data['overwrite_existing'] ?? false)) {
                        $errors[] = "Row " . ($index + 2) . ": Backlink already exists";
                        continue;
                    }

                    // Create or update backlink
                    if ($existingBacklink && ($data['overwrite_existing'] ?? false)) {
                        $backlink = $existingBacklink;
                    } else {
                        $backlink = new Backlink();
                    }

                    $backlink->setSourceUrl($rowData['source_url']);
                    $backlink->setSourceDomain($rowData['source_domain']);
                    $backlink->setClientId($data['client_id']);
                    $backlink->setStatus($rowData['status'] ?? 'pending');

                    if (isset($rowData['anchor_text'])) {
                        $backlink->setAnchorText($rowData['anchor_text']);
                    }

                    if (!$existingBacklink) {
                        $this->entityManager->persist($backlink);
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
                'message' => 'Backlink import completed',
                'imported_count' => $importedCount,
                'total_rows' => count($csvLines)
            ];

            if (!empty($errors)) {
                $response['errors'] = $errors;
            }

            $statusCode = $importedCount > 0 ? Response::HTTP_CREATED : Response::HTTP_BAD_REQUEST;
            return $this->json($response, $statusCode);

        } catch (\Exception $e) {
            return $this->json(['error' => 'Internal server error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
