<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Page;
use App\ValueObject\FindingResult;

interface AnalyzerInterface
{
    /**
     * Analyze a page and return findings
     */
    public function analyze(string $runId, Page $page): array;

    /**
     * Run a specific check on a page
     */
    public function runCheck(string $checkCode, Page $page): ?FindingResult;

    /**
     * Get all available checks
     */
    public function getAvailableChecks(): array;

    /**
     * Get check by code
     */
    public function getCheck(string $checkCode): ?object;
}
