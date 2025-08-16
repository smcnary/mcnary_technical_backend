<?php

namespace App\Controller\Api\V1;

use App\Entity\AuditRun;
use App\Repository\AuditRunRepository;
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

#[Route('/api/v1/audits')]
class AuditsController extends AbstractController
{
    public function __construct(
        private AuditRunRepository $auditRunRepository,
        private EntityManagerInterface $entityManager,
        private ValidatorInterface $validator
    ) {}

    #[Route('/run', name: 'api_v1_audits_run', methods: ['POST'])]
    #[IsGranted('ROLE_AGENCY_ADMIN')]
    public function runAudit(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            
            if (!$data) {
                return $this->json(['error' => 'Invalid JSON'], Response::HTTP_BAD_REQUEST);
            }

            // Validate input
            $constraints = new Assert\Collection([
                'client_id' => [new Assert\NotBlank(), new Assert\Uuid()],
                'tool' => [new Assert\NotBlank(), new Assert\Choice(['screaming_frog', 'ahrefs', 'semrush', 'moz', 'custom'])],
                'scope' => [new Assert\Optional([new Assert\Choice(['full_site', 'specific_pages', 'competitor_analysis'])])],
                'priority' => [new Assert\Optional([new Assert\Choice(['low', 'medium', 'high', 'urgent'])])],
                'notify_email' => [new Assert\Optional([new Assert\Email()])]
            ]);

            $violations = $this->validator->validate($data, $constraints);
            if (count($violations) > 0) {
                $errors = [];
                foreach ($violations as $violation) {
                    $errors[$violation->getPropertyPath()] = $violation->getMessage();
                }
                return $this->json(['error' => 'Validation failed', 'details' => $errors], Response::HTTP_BAD_REQUEST);
            }

            // Create audit run
            $auditRun = new AuditRun();
            $auditRun->setClientId($data['client_id']);
            $auditRun->setTool($data['tool']);
            $auditRun->setStatus('queued');
            $auditRun->setPriority($data['priority'] ?? 'medium');

            if (isset($data['scope'])) {
                $auditRun->setScope($data['scope']);
            }

            if (isset($data['notify_email'])) {
                $auditRun->setNotifyEmail($data['notify_email']);
            }

            $this->entityManager->persist($auditRun);
            $this->entityManager->flush();

            // Here you would typically enqueue a job to run the audit
            // For now, we'll return a success response
            $auditRunData = [
                'id' => $auditRun->getId(),
                'client_id' => $auditRun->getClientId(),
                'tool' => $auditRun->getTool(),
                'scope' => $auditRun->getScope(),
                'status' => $auditRun->getStatus(),
                'priority' => $auditRun->getPriority(),
                'notify_email' => $auditRun->getNotifyEmail(),
                'created_at' => $auditRun->getCreatedAt()->format('c')
            ];

            return $this->json([
                'message' => 'Audit job queued successfully',
                'audit_run' => $auditRunData
            ], Response::HTTP_CREATED);

        } catch (\Exception $e) {
            return $this->json(['error' => 'Internal server error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/runs', name: 'api_v1_audit_runs_list', methods: ['GET'])]
    #[IsGranted('ROLE_AGENCY_ADMIN')]
    public function listAuditRuns(Request $request): JsonResponse
    {
        $page = max(1, (int) $request->query->get('page', 1));
        $perPage = min(100, max(1, (int) $request->query->get('per_page', 20)));
        $sort = $request->query->get('sort', 'created_at');
        $clientId = $request->query->get('client_id', '');
        $status = $request->query->get('status', '');
        $tool = $request->query->get('tool', '');

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
        if ($tool) {
            $criteria['tool'] = $tool;
        }

        // Get audit runs with pagination and filtering
        $auditRuns = $this->auditRunRepository->findByCriteria($criteria, $sortFields, $perPage, ($page - 1) * $perPage);
        $totalAuditRuns = $this->auditRunRepository->countByCriteria($criteria);

        $auditRunData = [];
        foreach ($auditRuns as $auditRun) {
            $auditRunData[] = [
                'id' => $auditRun->getId(),
                'client_id' => $auditRun->getClientId(),
                'tool' => $auditRun->getTool(),
                'scope' => $auditRun->getScope(),
                'status' => $auditRun->getStatus(),
                'priority' => $auditRun->getPriority(),
                'started_at' => $auditRun->getStartedAt()?->format('c'),
                'completed_at' => $auditRun->getCompletedAt()?->format('c'),
                'findings_count' => $auditRun->getFindingsCount(),
                'created_at' => $auditRun->getCreatedAt()->format('c'),
                'updated_at' => $auditRun->getUpdatedAt()->format('c')
            ];
        }

        return $this->json([
            'data' => $auditRunData,
            'pagination' => [
                'page' => $page,
                'per_page' => $perPage,
                'total' => $totalAuditRuns,
                'pages' => ceil($totalAuditRuns / $perPage)
            ]
        ]);
    }

    #[Route('/runs/{id}', name: 'api_v1_audit_runs_get', methods: ['GET'])]
    #[IsGranted('ROLE_AGENCY_ADMIN')]
    public function getAuditRun(string $id): JsonResponse
    {
        if (!Uuid::isValid($id)) {
            return $this->json(['error' => 'Invalid UUID'], Response::HTTP_BAD_REQUEST);
        }

        $auditRun = $this->auditRunRepository->find($id);
        if (!$auditRun) {
            return $this->json(['error' => 'Audit run not found'], Response::HTTP_NOT_FOUND);
        }

        $auditRunData = [
            'id' => $auditRun->getId(),
            'client_id' => $auditRun->getClientId(),
            'tool' => $auditRun->getTool(),
            'scope' => $auditRun->getScope(),
            'status' => $auditRun->getStatus(),
            'priority' => $auditRun->getPriority(),
            'started_at' => $auditRun->getStartedAt()?->format('c'),
            'completed_at' => $auditRun->getCompletedAt()?->format('c'),
            'findings_count' => $auditRun->getFindingsCount(),
            'metadata' => $auditRun->getMetadata(),
            'created_at' => $auditRun->getCreatedAt()->format('c'),
            'updated_at' => $auditRun->getUpdatedAt()->format('c')
        ];

        return $this->json($auditRunData);
    }
}
