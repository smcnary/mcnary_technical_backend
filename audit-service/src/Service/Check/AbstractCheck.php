<?php

declare(strict_types=1);

namespace App\Service\Check;

use App\Entity\Page;
use App\ValueObject\FindingResult;

abstract class AbstractCheck implements CheckInterface
{
    protected function createFinding(Page $page, array $evidence = []): FindingResult
    {
        return new FindingResult(
            checkCode: $this->getCode(),
            category: $this->getCategory(),
            severity: $this->getSeverity(),
            title: $this->getTitle(),
            description: $this->getDescription(),
            recommendation: $this->getRecommendation(),
            evidence: $evidence,
            impactScore: $this->getImpactScore(),
            effort: $this->getEffort()
        );
    }

    protected function createPassFinding(Page $page): ?FindingResult
    {
        // Return null for passing checks to avoid cluttering the findings
        return null;
    }

    protected function extractTextFromHtml(string $html): string
    {
        return strip_tags($html);
    }

    protected function extractMetaContent(string $html, string $name): ?string
    {
        if (preg_match('/<meta[^>]+name=["\']' . preg_quote($name, '/') . '["\'][^>]+content=["\']([^"\']+)["\'][^>]*>/i', $html, $matches)) {
            return trim($matches[1]);
        }
        return null;
    }

    protected function extractMetaProperty(string $html, string $property): ?string
    {
        if (preg_match('/<meta[^>]+property=["\']' . preg_quote($property, '/') . '["\'][^>]+content=["\']([^"\']+)["\'][^>]*>/i', $html, $matches)) {
            return trim($matches[1]);
        }
        return null;
    }

    protected function extractTitle(string $html): ?string
    {
        if (preg_match('/<title[^>]*>([^<]+)<\/title>/i', $html, $matches)) {
            return trim($matches[1]);
        }
        return null;
    }

    protected function extractH1(string $html): array
    {
        $h1s = [];
        if (preg_match_all('/<h1[^>]*>([^<]+)<\/h1>/i', $html, $matches)) {
            $h1s = array_map('trim', $matches[1]);
        }
        return $h1s;
    }

    protected function extractImages(string $html): array
    {
        $images = [];
        if (preg_match_all('/<img[^>]+src=["\']([^"\']+)["\'][^>]*>/i', $html, $matches)) {
            $images = $matches[1];
        }
        return $images;
    }

    protected function extractImageAltTexts(string $html): array
    {
        $altTexts = [];
        if (preg_match_all('/<img[^>]+alt=["\']([^"\']*)["\'][^>]*>/i', $html, $matches)) {
            $altTexts = $matches[1];
        }
        return $altTexts;
    }

    protected function extractLinks(string $html): array
    {
        $links = [];
        if (preg_match_all('/<a[^>]+href=["\']([^"\']+)["\'][^>]*>/i', $html, $matches)) {
            $links = $matches[1];
        }
        return $links;
    }

    protected function extractSchemaMarkup(string $html): array
    {
        $schemas = [];
        if (preg_match_all('/<script[^>]+type=["\']application\/ld\+json["\'][^>]*>(.*?)<\/script>/is', $html, $matches)) {
            foreach ($matches[1] as $json) {
                $decoded = json_decode(trim($json), true);
                if ($decoded) {
                    $schemas[] = $decoded;
                }
            }
        }
        return $schemas;
    }

    protected function countWords(string $text): int
    {
        return str_word_count(strip_tags($text));
    }

    protected function isInternalLink(string $url, string $baseUrl): bool
    {
        $baseHost = parse_url($baseUrl, PHP_URL_HOST);
        $urlHost = parse_url($url, PHP_URL_HOST);
        return $baseHost === $urlHost;
    }

    protected function isExternalLink(string $url, string $baseUrl): bool
    {
        return !$this->isInternalLink($url, $baseUrl);
    }
}
