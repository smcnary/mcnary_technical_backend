<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\AuditRun;
use App\Entity\Finding;
use App\Entity\Page;
use App\Repository\AuditRunRepository;
use App\Repository\PageRepository;
use App\Service\Check\CheckInterface;
use App\ValueObject\FindingResult;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class SeoAnalyzer implements AnalyzerInterface
{
    /** @var CheckInterface[] */
    private array $checks = [];

    public function __construct(
        private EntityManagerInterface $entityManager,
        private AuditRunRepository $auditRunRepository,
        private PageRepository $pageRepository,
        private LoggerInterface $logger
    ) {
        $this->registerChecks();
    }

    public function analyze(string $runId, Page $page): array
    {
        $this->logger->info('Starting SEO analysis', [
            'run_id' => $runId,
            'page_url' => $page->getUrl()
        ]);

        $findings = [];
        
        foreach ($this->checks as $check) {
            if (!$check->isApplicable($page)) {
                continue;
            }

            try {
                $result = $check->run($page);
                if ($result) {
                    $findings[] = $this->createFindingEntity($runId, $page, $result);
                }
            } catch (\Exception $e) {
                $this->logger->error('Check failed', [
                    'check_code' => $check->getCode(),
                    'page_url' => $page->getUrl(),
                    'error' => $e->getMessage()
                ]);
            }
        }

        // Persist findings
        foreach ($findings as $finding) {
            $this->entityManager->persist($finding);
        }
        $this->entityManager->flush();

        $this->logger->info('Completed SEO analysis', [
            'run_id' => $runId,
            'page_url' => $page->getUrl(),
            'findings_count' => count($findings)
        ]);

        return $findings;
    }

    public function runCheck(string $checkCode, Page $page): ?FindingResult
    {
        $check = $this->getCheck($checkCode);
        if (!$check || !$check->isApplicable($page)) {
            return null;
        }

        return $check->run($page);
    }

    public function getAvailableChecks(): array
    {
        return array_map(fn(CheckInterface $check) => [
            'code' => $check->getCode(),
            'category' => $check->getCategory(),
            'severity' => $check->getSeverity(),
            'title' => $check->getTitle(),
            'description' => $check->getDescription(),
            'effort' => $check->getEffort(),
            'impact_score' => $check->getImpactScore(),
        ], $this->checks);
    }

    public function getCheck(string $checkCode): ?CheckInterface
    {
        foreach ($this->checks as $check) {
            if ($check->getCode() === $checkCode) {
                return $check;
            }
        }
        return null;
    }

    public function analyzeAuditRun(string $runId): array
    {
        $auditRun = $this->auditRunRepository->find($runId);
        if (!$auditRun) {
            throw new \InvalidArgumentException("Audit run {$runId} not found");
        }

        $this->logger->info('Starting audit run analysis', [
            'run_id' => $runId,
            'pages_count' => $auditRun->getPages()->count()
        ]);

        $allFindings = [];
        
        foreach ($auditRun->getPages() as $page) {
            $findings = $this->analyze($runId, $page);
            $allFindings = array_merge($allFindings, $findings);
        }

        // Update audit run totals
        $this->updateAuditRunTotals($auditRun, $allFindings);

        $this->logger->info('Completed audit run analysis', [
            'run_id' => $runId,
            'total_findings' => count($allFindings)
        ]);

        return $allFindings;
    }

    private function registerChecks(): void
    {
        // Technical SEO Checks
        $this->checks[] = new \App\Service\Check\Technical\HttpStatusCodeCheck();
        $this->checks[] = new \App\Service\Check\Technical\HttpsCheck();
        $this->checks[] = new \App\Service\Check\Technical\MobileFriendlyCheck();
        $this->checks[] = new \App\Service\Check\Technical\RobotsDirectivesCheck();

        // On-Page SEO Checks
        $this->checks[] = new \App\Service\Check\OnPage\TitleTagCheck();
        $this->checks[] = new \App\Service\Check\OnPage\MetaDescriptionCheck();
        $this->checks[] = new \App\Service\Check\OnPage\H1TagCheck();
        $this->checks[] = new \App\Service\Check\OnPage\ImageAltTextCheck();

        // Local SEO Checks
        $this->checks[] = new \App\Service\Check\Local\LocalBusinessSchemaCheck();
    }

    private function createFindingEntity(string $runId, Page $page, FindingResult $result): Finding
    {
        $auditRun = $this->auditRunRepository->find($runId);
        
        $finding = new Finding();
        $finding->setAuditRun($auditRun);
        $finding->setPage($page);
        $finding->setCategory($result->category);
        $finding->setSeverity($result->severity);
        $finding->setTitle($result->title);
        $finding->setDescription($result->description);
        $finding->setRecommendation($result->recommendation);
        $finding->setEvidence($result->evidence);
        $finding->setImpactScore($result->impactScore);
        $finding->setEffort($result->effort);
        $finding->setCheckKey($result->checkCode);
        $finding->setAffectedPagesCount(1);

        return $finding;
    }

    private function updateAuditRunTotals(AuditRun $auditRun, array $findings): void
    {
        $totals = $auditRun->getTotals();
        
        $totals['findings'] = [
            'total' => count($findings),
            'critical' => count(array_filter($findings, fn($f) => $f->isCritical())),
            'high' => count(array_filter($findings, fn($f) => $f->isHigh())),
            'medium' => count(array_filter($findings, fn($f) => $f->isMedium())),
            'low' => count(array_filter($findings, fn($f) => $f->isLow())),
        ];

        $auditRun->setTotals($totals);
        $this->entityManager->flush();
    }
}
