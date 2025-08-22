<?php

declare(strict_types=1);

namespace App\Service;

use App\ValueObject\FetchedResource;

interface CrawlerInterface
{
    /**
     * Fetch a web page and return the resource with metadata
     */
    public function fetch(string $url, array $options = []): FetchedResource;

    /**
     * Check if a URL should be crawled based on robots.txt and other rules
     */
    public function shouldCrawl(string $url, array $options = []): bool;

    /**
     * Discover new URLs from a page (links, sitemaps, etc.)
     */
    public function discoverUrls(FetchedResource $resource, array $options = []): array;

    /**
     * Take a screenshot of a page (optional)
     */
    public function takeScreenshot(string $url, array $options = []): ?string;
}
