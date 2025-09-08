<?php

declare(strict_types=1);

namespace App\Service\Check\Technical;

use App\Entity\Page;
use App\Service\Check\AbstractCheck;
use App\ValueObject\FindingResult;

class MobileFriendlyCheck extends AbstractCheck
{
    public function getCode(): string
    {
        return 'technical.mobile_friendly';
    }

    public function getCategory(): string
    {
        return 'technical';
    }

    public function getSeverity(): string
    {
        return 'high';
    }

    public function getTitle(): string
    {
        return 'Mobile-Friendly Issues';
    }

    public function getDescription(): string
    {
        return 'Page may not be mobile-friendly, which impacts mobile search rankings';
    }

    public function getRecommendation(): ?string
    {
        return 'Add responsive viewport meta tag and ensure the page layout adapts to mobile screens. Test with Google Mobile-Friendly Test.';
    }

    public function getEffort(): string
    {
        return 'medium';
    }

    public function getImpactScore(): float
    {
        return 6.0;
    }

    public function isApplicable(Page $page): bool
    {
        return $page->isHtml();
    }

    public function run(Page $page): ?FindingResult
    {
        // This would ideally use Google's Mobile-Friendly Test API
        // For now, we'll check for basic mobile-friendly indicators
        
        $html = $page->getBodyHash(); // We'd need the actual HTML content
        $evidence = [
            'url' => $page->getUrl(),
            'content_type' => $page->getContentType(),
        ];

        // Check for viewport meta tag
        $viewportMeta = $this->extractMetaContent($html, 'viewport');
        if (!$viewportMeta) {
            $evidence['missing_viewport'] = true;
            return $this->createFinding($page, $evidence);
        }

        // Check if viewport is responsive
        if (!str_contains(strtolower($viewportMeta), 'width=device-width')) {
            $evidence['non_responsive_viewport'] = $viewportMeta;
            return $this->createFinding($page, $evidence);
        }

        return $this->createPassFinding($page);
    }
}
