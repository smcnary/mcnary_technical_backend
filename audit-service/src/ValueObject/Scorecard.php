<?php

declare(strict_types=1);

namespace App\ValueObject;

final class Scorecard
{
    public function __construct(
        public readonly string $runId,
        public readonly float $overallScore,
        public readonly array $categoryScores,
        public readonly array $metrics,
        public readonly int $totalFindings,
        public readonly int $criticalFindings,
        public readonly int $highFindings,
        public readonly int $mediumFindings,
        public readonly int $lowFindings,
        public readonly array $topIssues = [],
        public readonly array $quickWins = []
    ) {}

    public function getCategoryScore(string $category): float
    {
        return $this->categoryScores[$category] ?? 0.0;
    }

    public function getMetric(string $name): mixed
    {
        return $this->metrics[$name] ?? null;
    }

    public function getTotalIssues(): int
    {
        return $this->criticalFindings + $this->highFindings + $this->mediumFindings + $this->lowFindings;
    }

    public function getIssuesBySeverity(string $severity): int
    {
        return match ($severity) {
            'critical' => $this->criticalFindings,
            'high' => $this->highFindings,
            'medium' => $this->mediumFindings,
            'low' => $this->lowFindings,
            default => 0,
        };
    }

    public function toArray(): array
    {
        return [
            'run_id' => $this->runId,
            'overall_score' => $this->overallScore,
            'category_scores' => $this->categoryScores,
            'metrics' => $this->metrics,
            'total_findings' => $this->totalFindings,
            'critical_findings' => $this->criticalFindings,
            'high_findings' => $this->highFindings,
            'medium_findings' => $this->mediumFindings,
            'low_findings' => $this->lowFindings,
            'top_issues' => $this->topIssues,
            'quick_wins' => $this->quickWins,
        ];
    }
}