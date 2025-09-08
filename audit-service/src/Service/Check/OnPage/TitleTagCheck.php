<?php

declare(strict_types=1);

namespace App\Service\Check\OnPage;

use App\Entity\Page;
use App\Service\Check\AbstractCheck;
use App\ValueObject\FindingResult;

class TitleTagCheck extends AbstractCheck
{
    public function getCode(): string
    {
        return 'onpage.title_tag';
    }

    public function getCategory(): string
    {
        return 'onpage';
    }

    public function getSeverity(): string
    {
        return 'high';
    }

    public function getTitle(): string
    {
        return 'Title Tag Issues';
    }

    public function getDescription(): string
    {
        return 'Page has title tag issues that impact SEO performance';
    }

    public function getRecommendation(): ?string
    {
        return 'Add a unique, descriptive title tag between 30-60 characters that includes target keywords and describes the page content.';
    }

    public function getEffort(): string
    {
        return 'small';
    }

    public function getImpactScore(): float
    {
        return 10.0;
    }

    public function isApplicable(Page $page): bool
    {
        return $page->isHtml();
    }

    public function run(Page $page): ?FindingResult
    {
        $title = $page->getTitle();
        
        if (empty($title)) {
            $evidence = [
                'url' => $page->getUrl(),
                'issue' => 'missing_title',
            ];
            return $this->createFinding($page, $evidence);
        }

        $titleLength = strlen($title);
        $issues = [];

        if ($titleLength < 30) {
            $issues[] = 'too_short';
        } elseif ($titleLength > 60) {
            $issues[] = 'too_long';
        }

        if (empty($issues)) {
            return $this->createPassFinding($page);
        }

        $evidence = [
            'url' => $page->getUrl(),
            'title' => $title,
            'title_length' => $titleLength,
            'issues' => $issues,
        ];

        return $this->createFinding($page, $evidence);
    }
}
