<?php

declare(strict_types=1);

namespace App\Service\Check\Technical;

use App\Entity\Page;
use App\Service\Check\AbstractCheck;
use App\ValueObject\FindingResult;

class HttpStatusCodeCheck extends AbstractCheck
{
    public function getCode(): string
    {
        return 'technical.http_status_code';
    }

    public function getCategory(): string
    {
        return 'technical';
    }

    public function getSeverity(): string
    {
        return 'critical';
    }

    public function getTitle(): string
    {
        return 'HTTP Status Code Issue';
    }

    public function getDescription(): string
    {
        return 'Page returns a non-200 HTTP status code that may impact SEO';
    }

    public function getRecommendation(): ?string
    {
        return 'Ensure all important pages return a 200 OK status code. Fix redirects, server errors, or client errors as appropriate.';
    }

    public function getEffort(): string
    {
        return 'medium';
    }

    public function getImpactScore(): float
    {
        return 10.0;
    }

    public function isApplicable(Page $page): bool
    {
        return true;
    }

    public function run(Page $page): ?FindingResult
    {
        if ($page->isSuccessful()) {
            return $this->createPassFinding($page);
        }

        $statusCode = $page->getStatusCode();
        $evidence = [
            'status_code' => $statusCode,
            'url' => $page->getUrl(),
        ];

        $description = match (true) {
            $page->isRedirect() => "Page returns a redirect status code ({$statusCode})",
            $page->isClientError() => "Page returns a client error status code ({$statusCode})",
            $page->isServerError() => "Page returns a server error status code ({$statusCode})",
            default => "Page returns an unexpected status code ({$statusCode})"
        };

        return $this->createFinding($page, $evidence);
    }
}
