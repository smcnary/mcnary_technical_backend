<?php

declare(strict_types=1);

namespace App\Service;

use App\ValueObject\ReportRef;

interface ReporterInterface
{
    /**
     * Generate a report for an audit run
     */
    public function build(string $runId, string $format): ReportRef;

    /**
     * Generate HTML report
     */
    public function generateHtml(string $runId): ReportRef;

    /**
     * Generate PDF report
     */
    public function generatePdf(string $runId): ReportRef;

    /**
     * Generate CSV export
     */
    public function generateCsv(string $runId): ReportRef;

    /**
     * Generate JSON export
     */
    public function generateJson(string $runId): ReportRef;

    /**
     * Get available report formats
     */
    public function getAvailableFormats(): array;
}
