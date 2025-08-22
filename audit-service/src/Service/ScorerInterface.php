<?php

declare(strict_types=1);

namespace App\Service;

use App\ValueObject\Scorecard;

interface ScorerInterface
{
    /**
     * Calculate scores for an audit run
     */
    public function score(string $runId): Scorecard;

    /**
     * Calculate category scores
     */
    public function scoreCategory(string $runId, string $category): float;

    /**
     * Calculate overall score
     */
    public function scoreOverall(string $runId): float;

    /**
     * Compare scores between two runs
     */
    public function compareRuns(string $runId1, string $runId2): array;
}
