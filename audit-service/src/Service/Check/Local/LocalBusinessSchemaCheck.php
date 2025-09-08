<?php

declare(strict_types=1);

namespace App\Service\Check\Local;

use App\Entity\Page;
use App\Service\Check\AbstractCheck;
use App\ValueObject\FindingResult;

class LocalBusinessSchemaCheck extends AbstractCheck
{
    public function getCode(): string
    {
        return 'local.business_schema';
    }

    public function getCategory(): string
    {
        return 'local';
    }

    public function getSeverity(): string
    {
        return 'high';
    }

    public function getTitle(): string
    {
        return 'Local Business Schema Missing';
    }

    public function getDescription(): string
    {
        return 'Page is missing LocalBusiness schema markup for local SEO';
    }

    public function getRecommendation(): ?string
    {
        return 'Add LocalBusiness schema markup with business name, address, phone number, and other relevant business information.';
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
        return $page->isHtml();
    }

    public function run(Page $page): ?FindingResult
    {
        // We need to extract schema markup from the HTML content
        $html = ''; // This would need to be the actual HTML content
        
        $schemas = $this->extractSchemaMarkup($html);
        
        $hasLocalBusiness = false;
        foreach ($schemas as $schema) {
            if (isset($schema['@type']) && $schema['@type'] === 'LocalBusiness') {
                $hasLocalBusiness = true;
                break;
            }
        }

        if ($hasLocalBusiness) {
            return $this->createPassFinding($page);
        }

        $evidence = [
            'url' => $page->getUrl(),
            'schema_types_found' => array_column($schemas, '@type'),
        ];

        return $this->createFinding($page, $evidence);
    }
}
