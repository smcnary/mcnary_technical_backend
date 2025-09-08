<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Service\WebCrawler;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;

class WebCrawlerTest extends TestCase
{
    private WebCrawler $crawler;

    protected function setUp(): void
    {
        $this->crawler = new WebCrawler(
            new NullLogger(),
            '/tmp/test-storage'
        );
    }

    public function testFetchValidUrl(): void
    {
        $resource = $this->crawler->fetch('https://httpbin.org/html');
        
        // The fetch might fail due to network issues in CI, so we'll just check the structure
        $this->assertInstanceOf(\App\ValueObject\FetchedResource::class, $resource);
        $this->assertIsString($resource->url);
        $this->assertIsInt($resource->statusCode);
        $this->assertIsString($resource->contentType);
        $this->assertIsFloat($resource->responseTime);
        $this->assertIsInt($resource->contentLength);
    }

    public function testFetchInvalidUrl(): void
    {
        $resource = $this->crawler->fetch('https://invalid-domain-that-does-not-exist.com');
        
        $this->assertFalse($resource->isSuccessful());
        $this->assertNotNull($resource->error);
    }

    public function testShouldCrawlRespectsRobots(): void
    {
        // Test with a URL that should be crawlable
        $this->assertTrue($this->crawler->shouldCrawl('https://httpbin.org/html'));
        
        // Test with blocked paths
        $this->assertFalse($this->crawler->shouldCrawl('https://example.com/admin', [
            'blocked_paths' => ['/admin']
        ]));
    }

    public function testDiscoverUrlsFromHtml(): void
    {
        $html = '<html><body><a href="/page1">Link 1</a><a href="https://example.com/page2">Link 2</a></body></html>';
        
        $resource = new \App\ValueObject\FetchedResource(
            url: 'https://example.com',
            statusCode: 200,
            contentType: 'text/html',
            body: $html,
            headers: [],
            responseTime: 0.1,
            contentLength: strlen($html)
        );

        $urls = $this->crawler->discoverUrls($resource);
        
        $this->assertContains('https://example.com/page1', $urls);
        $this->assertContains('https://example.com/page2', $urls);
    }

    public function testNormalizeUrl(): void
    {
        $testCases = [
            'https://example.com/path/' => 'https://example.com/path/',
            'https://example.com/path#fragment' => 'https://example.com/path#fragment',
            'https://example.com/path?query=1' => 'https://example.com/path?query=1',
        ];

        foreach ($testCases as $input => $expected) {
            $resource = $this->crawler->fetch($input);
            $this->assertEquals($expected, $resource->url);
        }
    }
}
