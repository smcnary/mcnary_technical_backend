<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\AuditRun;
use App\Entity\Finding;
use App\Repository\AuditRunRepository;
use App\ValueObject\Scorecard;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class SeoScorer implements ScorerInterface
{
    private const CATEGORY_WEIGHTS = [
        'technical' => 40,
        'onpage' => 35,
        'local' => 25,
    ];

    private const SEVERITY_WEIGHTS = [
        'critical' => 10,
        'high' => 7,
        'medium' => 4,
        'low' => 1,
    ];

    public function __construct(
        private EntityManagerInterface $entityManager,
        private AuditRunRepository $auditRunRepository,
        private LoggerInterface $logger
    ) {}

    public function score(string $runId): Scorecard
    {
        $auditRun = $this->auditRunRepository->find($runId);
        if (!$auditRun) {
            throw new \InvalidArgumentException("Audit run {$runId} not found");
        }

        $this->logger->info('Starting score calculation', ['run_id' => $runId]);

        $findings = $auditRun->getFindings();
        $categoryScores = $this->calculateCategoryScores($findings);
        $overallScore = $this->calculateOverallScore($categoryScores);
        $metrics = $this->calculateMetrics($auditRun, $findings);
        $topIssues = $this->getTopIssues($findings);
        $quickWins = $this->getQuickWins($findings);

        $scorecard = new Scorecard(
            runId: $runId,
            overallScore: $overallScore,
            categoryScores: $categoryScores,
            metrics: $metrics,
            totalFindings: $findings->count(),
            criticalFindings: count(array_filter($findings->toArray(), fn($f) => $f->isCritical())),
            highFindings: count(array_filter($findings->toArray(), fn($f) => $f->isHigh())),
            mediumFindings: count(array_filter($findings->toArray(), fn($f) => $f->isMedium())),
            lowFindings: count(array_filter($findings->toArray(), fn($f) => $f->isLow())),
            topIssues: $topIssues,
            quickWins: $quickWins
        );

        $this->logger->info('Completed score calculation', [
            'run_id' => $runId,
            'overall_score' => $overallScore,
            'total_findings' => $findings->count()
        ]);

        return $scorecard;
    }

    public function scoreCategory(string $runId, string $category): float
    {
        $auditRun = $this->auditRunRepository->find($runId);
        if (!$auditRun) {
            throw new \InvalidArgumentException("Audit run {$runId} not found");
        }

        $findings = $auditRun->getFindings()->filter(
            fn(Finding $finding) => $finding->getCategory() === $category
        );

        return $this->calculateCategoryScore($findings->toArray(), $category);
    }

    public function scoreOverall(string $runId): float
    {
        $auditRun = $this->auditRunRepository->find($runId);
        if (!$auditRun) {
            throw new \InvalidArgumentException("Audit run {$runId} not found");
        }

        $findings = $auditRun->getFindings();
        $categoryScores = $this->calculateCategoryScores($findings);

        return $this->calculateOverallScore($categoryScores);
    }

    public function compareRuns(string $runId1, string $runId2): array
    {
        $scorecard1 = $this->score($runId1);
        $scorecard2 = $this->score($runId2);

        return [
            'run1' => $scorecard1->toArray(),
            'run2' => $scorecard2->toArray(),
            'comparison' => [
                'overall_score_delta' => $scorecard2->overallScore - $scorecard1->overallScore,
                'category_deltas' => array_map(
                    fn($cat) => $scorecard2->getCategoryScore($cat) - $scorecard1->getCategoryScore($cat),
                    array_keys(self::CATEGORY_WEIGHTS)
                ),
                'findings_delta' => $scorecard2->totalFindings - $scorecard1->totalFindings,
            ]
        ];
    }

    private function calculateCategoryScores($findings): array
    {
        $categoryScores = [];
        
        foreach (array_keys(self::CATEGORY_WEIGHTS) as $category) {
            $categoryFindings = array_filter($findings->toArray(), fn($f) => $f->getCategory() === $category);
            $categoryScores[$category] = $this->calculateCategoryScore($categoryFindings, $category);
        }

        return $categoryScores;
    }

    private function calculateCategoryScore(array $findings, string $category): float
    {
        if (empty($findings)) {
            return 100.0; // Perfect score if no issues
        }

        $maxWeight = self::CATEGORY_WEIGHTS[$category];
        $totalDeduction = 0;

        foreach ($findings as $finding) {
            $severityWeight = self::SEVERITY_WEIGHTS[$finding->getSeverity()] ?? 1;
            $impactScore = $finding->getImpactScore();
            $deduction = ($severityWeight * $impactScore) / 10; // Normalize to 0-10 scale
            $totalDeduction += min($deduction, $maxWeight); // Cap deduction per finding
        }

        $score = max(0, $maxWeight - $totalDeduction);
        return round(($score / $maxWeight) * 100, 1); // Convert to percentage
    }

    private function calculateOverallScore(array $categoryScores): float
    {
        $totalWeight = array_sum(self::CATEGORY_WEIGHTS);
        $weightedScore = 0;

        foreach ($categoryScores as $category => $score) {
            $weight = self::CATEGORY_WEIGHTS[$category];
            $weightedScore += ($score / 100) * $weight;
        }

        return round(($weightedScore / $totalWeight) * 100, 1);
    }

    private function calculateMetrics(AuditRun $auditRun, $findings): array
    {
        $pages = $auditRun->getPages();
        $totalPages = $pages->count();
        $successfulPages = $pages->filter(fn($p) => $p->isSuccessful())->count();
        $indexablePages = $pages->filter(fn($p) => $p->isIndexable())->count();

        return [
            'total_pages' => $totalPages,
            'successful_pages' => $successfulPages,
            'indexable_pages' => $indexablePages,
            'success_rate' => $totalPages > 0 ? round(($successfulPages / $totalPages) * 100, 1) : 0,
            'indexability_rate' => $totalPages > 0 ? round(($indexablePages / $totalPages) * 100, 1) : 0,
            'average_response_time' => $this->calculateAverageResponseTime($pages),
            'total_content_length' => $this->calculateTotalContentLength($pages),
        ];
    }

    private function calculateAverageResponseTime($pages): float
    {
        $totalTime = 0;
        $count = 0;

        foreach ($pages as $page) {
            $totalTime += $page->getResponseTime();
            $count++;
        }

        return $count > 0 ? round($totalTime / $count, 3) : 0;
    }

    private function calculateTotalContentLength($pages): int
    {
        $totalLength = 0;
        foreach ($pages as $page) {
            $totalLength += $page->getContentLength();
        }
        return $totalLength;
    }

    private function getTopIssues($findings): array
    {
        $findingsArray = $findings->toArray();
        
        // Sort by impact score and severity
        usort($findingsArray, function($a, $b) {
            $severityOrder = ['critical' => 4, 'high' => 3, 'medium' => 2, 'low' => 1];
            $aSeverity = $severityOrder[$a->getSeverity()] ?? 0;
            $bSeverity = $severityOrder[$b->getSeverity()] ?? 0;
            
            if ($aSeverity !== $bSeverity) {
                return $bSeverity - $aSeverity;
            }
            
            return $b->getImpactScore() <=> $a->getImpactScore();
        });

        return array_slice(array_map(fn($f) => [
            'title' => $f->getTitle(),
            'severity' => $f->getSeverity(),
            'impact_score' => $f->getImpactScore(),
            'effort' => $f->getEffort(),
            'affected_pages' => $f->getAffectedPagesCount(),
        ], $findingsArray), 0, 10);
    }

    private function getQuickWins($findings): array
    {
        $findingsArray = $findings->toArray();
        
        // Filter for high impact, low effort issues
        $quickWins = array_filter($findingsArray, function($finding) {
            return $finding->getImpactScore() >= 5.0 && $finding->getEffort() === 'small';
        });

        // Sort by impact score / effort ratio
        usort($quickWins, fn($a, $b) => $b->getImpactScore() <=> $a->getImpactScore());

        return array_slice(array_map(fn($f) => [
            'title' => $f->getTitle(),
            'impact_score' => $f->getImpactScore(),
            'effort' => $f->getEffort(),
            'recommendation' => $f->getRecommendation(),
        ], $quickWins), 0, 5);
    }
}
