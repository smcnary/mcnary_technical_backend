<?php

namespace App\Controller\Api\V1;

use App\Entity\AuditFinding;
use App\Repository\AuditFindingRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Uid\Uuid;

#[Route('/api/v1/audit-findings')]
class AuditFindingsController extends AbstractController
{
    public function __construct(
        private AuditFindingRepository $auditFindingRepository
    ) {}

    #[Route('', name: 'api_v1_audit_findings_list', methods: ['GET'])]
    #[IsGranted('ROLE_AGENCY_ADMIN')]
    public function listAuditFindings(Request $request): JsonResponse
    {
        $page = max(1, (int) $request->query->get('page', 1));
        $perPage = min(100, max(1, (int) $request->query->get('per_page', 20)));
        $sort = $request->query->get('sort', 'severity');
        $auditRunId = $request->query->get('audit_run_id', '');
        $severity = $request->query->get('severity', '');
        $category = $request->query->get('category', '');

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
        if ($auditRunId) {
            $criteria['audit_run_id'] = $auditRunId;
        }
        if ($severity) {
            $criteria['severity'] = $severity;
        }
        if ($category) {
            $criteria['category'] = $category;
        }

        // Get audit findings with pagination and filtering
        $auditFindings = $this->auditFindingRepository->findByCriteria($criteria, $sortFields, $perPage, ($page - 1) * $perPage);
        $totalAuditFindings = $this->auditFindingRepository->countByCriteria($criteria);

        $auditFindingData = [];
        foreach ($auditFindings as $auditFinding) {
            $auditFindingData[] = [
                'id' => $auditFinding->getId(),
                'audit_run_id' => $auditFinding->getAuditRunId(),
                'title' => $auditFinding->getTitle(),
                'description' => $auditFinding->getDescription(),
                'severity' => $auditFinding->getSeverity(),
                'category' => $auditFinding->getCategory(),
                'url' => $auditFinding->getUrl(),
                'line_number' => $auditFinding->getLineNumber(),
                'code_snippet' => $auditFinding->getCodeSnippet(),
                'recommendation' => $auditFinding->getRecommendation(),
                'impact_score' => $auditFinding->getImpactScore(),
                'created_at' => $auditFinding->getCreatedAt()->format('c')
            ];
        }

        return $this->json([
            'data' => $auditFindingData,
            'pagination' => [
                'page' => $page,
                'per_page' => $perPage,
                'total' => $totalAuditFindings,
                'pages' => ceil($totalAuditFindings / $perPage)
            ]
        ]);
    }

    #[Route('/{id}', name: 'api_v1_audit_findings_get', methods: ['GET'])]
    #[IsGranted('ROLE_AGENCY_ADMIN')]
    public function getAuditFinding(string $id): JsonResponse
    {
        if (!Uuid::isValid($id)) {
            return $this->json(['error' => 'Invalid UUID'], Response::HTTP_BAD_REQUEST);
        }

        $auditFinding = $this->auditFindingRepository->find($id);
        if (!$auditFinding) {
            return $this->json(['error' => 'Audit finding not found'], Response::HTTP_NOT_FOUND);
        }

        $auditFindingData = [
            'id' => $auditFinding->getId(),
            'audit_run_id' => $auditFinding->getAuditRunId(),
            'title' => $auditFinding->getTitle(),
            'description' => $auditFinding->getDescription(),
            'severity' => $auditFinding->getSeverity(),
            'category' => $auditFinding->getCategory(),
            'url' => $auditFinding->getUrl(),
            'line_number' => $auditFinding->getLineNumber(),
            'code_snippet' => $auditFinding->getCodeSnippet(),
            'recommendation' => $auditFinding->getRecommendation(),
            'impact_score' => $auditFinding->getImpactScore(),
            'metadata' => $auditFinding->getMetadata(),
            'created_at' => $auditFinding->getCreatedAt()->format('c'),
            'updated_at' => $auditFinding->getUpdatedAt()->format('c')
        ];

        return $this->json($auditFindingData);
    }
}
