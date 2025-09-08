<?php

declare(strict_types=1);

namespace App\Service\Check\Technical;

use App\Entity\Page;
use App\Service\Check\AbstractCheck;
use App\ValueObject\FindingResult;

class RobotsDirectivesCheck extends AbstractCheck
{
    public function getCode(): string
    {
        return 'technical.robots_directives';
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
        return 'Robots Directives Blocking Indexing';
    }

    public function getDescription(): string
    {
        return 'Page has robots directives that prevent search engine indexing';
    }

    public function getRecommendation(): ?string
    {
        return 'Remove or modify robots directives (noindex, nofollow) to allow search engines to index and follow links on this page.';
    }

    public function getEffort(): string
    {
        return 'small';
    }

    public function getImpactScore(): float
    {
        return 8.0;
    }

    public function isApplicable(Page $page): bool
    {
        return $page->isHtml();
    }

    public function run(Page $page): ?FindingResult
    {
        $robotsDirectives = $page->getRobotsDirectives();
        
        if (empty($robotsDirectives)) {
            return $this->createPassFinding($page);
        }

        $blockingDirectives = array_intersect(['noindex', 'nofollow'], $robotsDirectives);
        
        if (empty($blockingDirectives)) {
            return $this->createPassFinding($page);
        }

        $evidence = [
            'url' => $page->getUrl(),
            'robots_directives' => $robotsDirectives,
            'blocking_directives' => $blockingDirectives,
        ];

        return $this->createFinding($page, $evidence);
    }
}
