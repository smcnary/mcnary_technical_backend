<?php

declare(strict_types=1);

namespace App\Service\Check\OnPage;

use App\Entity\Page;
use App\Service\Check\AbstractCheck;
use App\ValueObject\FindingResult;

class H1TagCheck extends AbstractCheck
{
    public function getCode(): string
    {
        return 'onpage.h1_tag';
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
        return 'H1 Tag Issues';
    }

    public function getDescription(): string
    {
        return 'Page has H1 tag issues that impact SEO structure';
    }

    public function getRecommendation(): ?string
    {
        return 'Add a single, descriptive H1 tag that includes target keywords and clearly describes the page content.';
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
        // We need to extract H1 tags from the HTML content
        // For now, we'll use a placeholder approach
        $html = ''; // This would need to be the actual HTML content
        
        $h1s = $this->extractH1($html);
        
        if (empty($h1s)) {
            $evidence = [
                'url' => $page->getUrl(),
                'issue' => 'missing_h1',
            ];
            return $this->createFinding($page, $evidence);
        }

        if (count($h1s) > 1) {
            $evidence = [
                'url' => $page->getUrl(),
                'issue' => 'multiple_h1',
                'h1_count' => count($h1s),
                'h1_tags' => $h1s,
            ];
            return $this->createFinding($page, $evidence);
        }

        return $this->createPassFinding($page);
    }
}
