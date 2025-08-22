<?php

declare(strict_types=1);

namespace App\ValueObject;

final class Scorecard
{
    public function __construct(
        public readonly string $runId,
        public readonly float $overallScore,
        public readonly array $categoryScores,
        public readonly array $findings,
        public readonly array $metrics,
        public readonly \DateTimeImmutable $generatedAt
    ) {}

    public function getCategoryScore(string $category): float
    {
        return $this->categoryScores[$category] ?? 0.0;
    }

    public function getTechnicalScore(): float
    {
        return $this->getCategoryScore('technical');
    }

    public function getContentScore(): float
    {
        return $this->getCategoryScore('content');
    }

    public function getAuthorityScore(): float
    {
        return $this->getCategoryScore('authority');
    }

    public function getUxScore(): float
    {
        return $this->getCategoryScore('ux');
    }

    public function getFindingsByCategory(string $category): array
    {
        return array_filter($this->findings, fn($finding) => $finding['category'] === $category);
    }

    public function getFindingsBySeverity(string $severity): array
    {
        return array_filter($this->findings, fn($finding) => $finding['severity'] === $severity);
    }

    public function getCriticalFindings(): array
    {
        return $this->getFindingsBySeverity('critical');
    }

    public function getHighFindings(): array
    {
        return $this->getFindingsBySeverity('high');
    }

    public function getTotalFindings(): int
    {
        return count($this->findings);
    }

    public function getPassedChecks(): int
    {
        return count(array_filter($this->findings, fn($finding) => $finding['status'] === 'pass'));
    }

    public function getFailedChecks(): int
    {
        return count(array_filter($this->findings, fn($finding) => $finding['status'] === 'fail'));
    }

    public function toArray(): array
    {
        return [
            'runId' => $this->runId,
            'overallScore' => $this->overallScore,
            'categoryScores' => $this->categoryScores,
            'findings' => $this->findings,
            'metrics' => $this->metrics,
            'generatedAt' => $this->generatedAt->format('c'),
            'summary' => [
                'totalFindings' => $this->getTotalFindings(),
                'passedChecks' => $this->getPassedChecks(),
                'failedChecks' => $this->getFailedChecks(),
                'criticalFindings' => count($this->getCriticalFindings()),
                'highFindings' => count($this->getHighFindings()),
            ]
        ];
    }
}
