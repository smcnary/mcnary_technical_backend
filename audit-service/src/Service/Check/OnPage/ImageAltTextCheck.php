<?php

declare(strict_types=1);

namespace App\Service\Check\OnPage;

use App\Entity\Page;
use App\Service\Check\AbstractCheck;
use App\ValueObject\FindingResult;

class ImageAltTextCheck extends AbstractCheck
{
    public function getCode(): string
    {
        return 'onpage.image_alt_text';
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
        return 'Image Alt Text Issues';
    }

    public function getDescription(): string
    {
        return 'Images are missing alt text, which impacts accessibility and SEO';
    }

    public function getRecommendation(): ?string
    {
        return 'Add descriptive alt text to all images. Use empty alt="" for decorative images and descriptive text for content images.';
    }

    public function getEffort(): string
    {
        return 'medium';
    }

    public function getImpactScore(): float
    {
        return 5.0;
    }

    public function isApplicable(Page $page): bool
    {
        return $page->isHtml();
    }

    public function run(Page $page): ?FindingResult
    {
        // We need to extract images from the HTML content
        $html = ''; // This would need to be the actual HTML content
        
        $images = $this->extractImages($html);
        $altTexts = $this->extractImageAltTexts($html);
        
        if (empty($images)) {
            return $this->createPassFinding($page);
        }

        $missingAltCount = count($images) - count($altTexts);
        $missingAltPercentage = $missingAltCount / count($images) * 100;

        if ($missingAltPercentage > 50) {
            $evidence = [
                'url' => $page->getUrl(),
                'total_images' => count($images),
                'missing_alt_count' => $missingAltCount,
                'missing_alt_percentage' => round($missingAltPercentage, 2),
            ];
            return $this->createFinding($page, $evidence);
        }

        return $this->createPassFinding($page);
    }
}
