<?php

declare(strict_types=1);

namespace App\Service\Check\Technical;

use App\Entity\Page;
use App\Service\Check\AbstractCheck;
use App\ValueObject\FindingResult;

class HttpsCheck extends AbstractCheck
{
    public function getCode(): string
    {
        return 'technical.https';
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
        return 'HTTPS Not Implemented';
    }

    public function getDescription(): string
    {
        return 'Page is not served over HTTPS, which is required for modern SEO';
    }

    public function getRecommendation(): ?string
    {
        return 'Implement SSL/TLS certificate and redirect all HTTP traffic to HTTPS. Update internal links to use HTTPS URLs.';
    }

    public function getEffort(): string
    {
        return 'medium';
    }

    public function getImpactScore(): float
    {
        return 8.0;
    }

    public function isApplicable(Page $page): bool
    {
        return true;
    }

    public function run(Page $page): ?FindingResult
    {
        $url = $page->getUrl();
        $isHttps = str_starts_with($url, 'https://');

        if ($isHttps) {
            return $this->createPassFinding($page);
        }

        $evidence = [
            'url' => $url,
            'protocol' => 'http',
        ];

        return $this->createFinding($page, $evidence);
    }
}
