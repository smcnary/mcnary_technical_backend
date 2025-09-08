<?php

declare(strict_types=1);

namespace App\Service\Check\OnPage;

use App\Entity\Page;
use App\Service\Check\AbstractCheck;
use App\ValueObject\FindingResult;

class MetaDescriptionCheck extends AbstractCheck
{
    public function getCode(): string
    {
        return 'onpage.meta_description';
    }

    public function getCategory(): string
    {
        return 'onpage';
    }

    public function getSeverity(): string
    {
        return 'medium';
    }

    public function getTitle(): string
    {
        return 'Meta Description Issues';
    }

    public function getDescription(): string
    {
        return 'Page has meta description issues that may impact click-through rates';
    }

    public function getRecommendation(): ?string
    {
        return 'Add a compelling meta description between 120-160 characters that summarizes the page content and encourages clicks.';
    }

    public function getEffort(): string
    {
        return 'small';
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
        $metaDescription = $page->getMetaDescription();
        
        if (empty($metaDescription)) {
            $evidence = [
                'url' => $page->getUrl(),
                'issue' => 'missing_meta_description',
            ];
            return $this->createFinding($page, $evidence);
        }

        $descriptionLength = strlen($metaDescription);
        $issues = [];

        if ($descriptionLength < 120) {
            $issues[] = 'too_short';
        } elseif ($descriptionLength > 160) {
            $issues[] = 'too_long';
        }

        if (empty($issues)) {
            return $this->createPassFinding($page);
        }

        $evidence = [
            'url' => $page->getUrl(),
            'meta_description' => $metaDescription,
            'description_length' => $descriptionLength,
            'issues' => $issues,
        ];

        return $this->createFinding($page, $evidence);
    }
}
