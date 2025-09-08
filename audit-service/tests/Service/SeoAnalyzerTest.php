<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Entity\Page;
use App\Entity\AuditRun;
use App\Service\Check\Technical\HttpStatusCodeCheck;
use App\Service\Check\OnPage\TitleTagCheck;
use App\Service\SeoAnalyzer;
use App\Service\SeoScorer;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;

class SeoAnalyzerTest extends TestCase
{
    private SeoAnalyzer $analyzer;
    private SeoScorer $scorer;

    protected function setUp(): void
    {
        // Mock dependencies
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $auditRunRepository = $this->createMock(\App\Repository\AuditRunRepository::class);
        $pageRepository = $this->createMock(\App\Repository\PageRepository::class);
        
        $this->analyzer = new SeoAnalyzer(
            $entityManager,
            $auditRunRepository,
            $pageRepository,
            new NullLogger()
        );

        $this->scorer = new SeoScorer(
            $entityManager,
            $auditRunRepository,
            new NullLogger()
        );
    }

    public function testHttpStatusCodeCheck(): void
    {
        $check = new HttpStatusCodeCheck();
        
        // Test successful page
        $page = $this->createMockPage(200, 'text/html');
        $result = $check->run($page);
        $this->assertNull($result); // Should pass
        
        // Test error page
        $page = $this->createMockPage(404, 'text/html');
        $result = $check->run($page);
        $this->assertNotNull($result);
        $this->assertEquals('critical', $result->severity);
        $this->assertEquals('technical', $result->category);
    }

    public function testTitleTagCheck(): void
    {
        $check = new TitleTagCheck();
        
        // Test page with good title
        $page = $this->createMockPage(200, 'text/html', 'Good Title Between 30-60 Characters');
        $result = $check->run($page);
        $this->assertNull($result); // Should pass
        
        // Test page with missing title
        $page = $this->createMockPage(200, 'text/html', '');
        $result = $check->run($page);
        $this->assertNotNull($result);
        $this->assertEquals('high', $result->severity);
        $this->assertEquals('onpage', $result->category);
    }

    public function testAvailableChecks(): void
    {
        $checks = $this->analyzer->getAvailableChecks();
        
        $this->assertIsArray($checks);
        $this->assertGreaterThan(0, count($checks));
        
        // Check that we have checks from all categories
        $categories = array_unique(array_column($checks, 'category'));
        $this->assertContains('technical', $categories);
        $this->assertContains('onpage', $categories);
        $this->assertContains('local', $categories);
    }

    public function testScoringSystem(): void
    {
        // Test category weights
        $reflection = new \ReflectionClass($this->scorer);
        $weights = $reflection->getConstant('CATEGORY_WEIGHTS');
        
        $this->assertEquals(40, $weights['technical']);
        $this->assertEquals(35, $weights['onpage']);
        $this->assertEquals(25, $weights['local']);
        
        // Test severity weights
        $severityWeights = $reflection->getConstant('SEVERITY_WEIGHTS');
        $this->assertEquals(10, $severityWeights['critical']);
        $this->assertEquals(7, $severityWeights['high']);
        $this->assertEquals(4, $severityWeights['medium']);
        $this->assertEquals(1, $severityWeights['low']);
    }

    private function createMockPage(int $statusCode, string $contentType, ?string $title = null): Page
    {
        $page = $this->createMock(Page::class);
        $page->method('getStatusCode')->willReturn($statusCode);
        $page->method('getContentType')->willReturn($contentType);
        $page->method('getTitle')->willReturn($title);
        $page->method('getUrl')->willReturn('https://example.com');
        $page->method('isSuccessful')->willReturn($statusCode >= 200 && $statusCode < 300);
        $page->method('isHtml')->willReturn(str_contains($contentType, 'text/html'));
        $page->method('getRobotsDirectives')->willReturn([]);
        $page->method('getMetaDescription')->willReturn(null);
        
        return $page;
    }
}
