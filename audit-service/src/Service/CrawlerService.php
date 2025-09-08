<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\AuditRun;
use App\Entity\Page;
use App\ValueObject\FetchedResource;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class CrawlerService
{
    private const MAX_PAGES_PER_AUDIT = 200;
    private const MAX_CONCURRENT_REQUESTS = 4;
    private const CRAWL_DELAY_MS = 1000; // 1 second between requests

    public function __construct(
        private CrawlerInterface $crawler,
        private EntityManagerInterface $entityManager,
        private LoggerInterface $logger,
        private MessageBusInterface $messageBus
    ) {}

    public function crawlAuditRun(AuditRun $auditRun): void
    {
        $this->logger->info('Starting crawl for audit run', [
            'audit_run_id' => $auditRun->getId(),
            'seed_urls' => $auditRun->getSeedUrls()
        ]);

        try {
            $auditRun->setState(\App\Entity\AuditRunState::RUNNING);
            $auditRun->setStartedAt(new \DateTimeImmutable());
            $this->entityManager->flush();

            $crawledUrls = [];
            $urlsToCrawl = $auditRun->getSeedUrls();
            $pagesCrawled = 0;

            while (!empty($urlsToCrawl) && $pagesCrawled < self::MAX_PAGES_PER_AUDIT) {
                $batch = array_slice($urlsToCrawl, 0, self::MAX_CONCURRENT_REQUESTS);
                $urlsToCrawl = array_slice($urlsToCrawl, self::MAX_CONCURRENT_REQUESTS);

                $batchResults = $this->crawlBatch($batch, $auditRun->getConfig());
                
                foreach ($batchResults as $result) {
                    if ($result['success']) {
                        $page = $this->createPageEntity($result['resource'], $auditRun);
                        $this->entityManager->persist($page);
                        
                        $pagesCrawled++;
                        $crawledUrls[] = $result['resource']->url;

                        // Discover new URLs
                        if ($result['resource']->isHtml()) {
                            $newUrls = $this->crawler->discoverUrls($result['resource'], $auditRun->getConfig());
                            foreach ($newUrls as $newUrl) {
                                if (!in_array($newUrl, $crawledUrls) && !in_array($newUrl, $urlsToCrawl)) {
                                    $urlsToCrawl[] = $newUrl;
                                }
                            }
                        }
                    }

                    // Rate limiting
                    usleep(self::CRAWL_DELAY_MS * 1000);
                }

                $this->entityManager->flush();
            }

            // Update audit run totals
            $auditRun->setTotals([
                'pages_crawled' => $pagesCrawled,
                'urls_discovered' => count($crawledUrls),
                'crawl_duration' => $this->getCrawlDuration($auditRun)
            ]);

            $auditRun->setState(\App\Entity\AuditRunState::COMPLETED);
            $auditRun->setFinishedAt(new \DateTimeImmutable());
            $this->entityManager->flush();

            $this->logger->info('Completed crawl for audit run', [
                'audit_run_id' => $auditRun->getId(),
                'pages_crawled' => $pagesCrawled,
                'urls_discovered' => count($crawledUrls)
            ]);

        } catch (\Exception $e) {
            $this->logger->error('Crawl failed for audit run', [
                'audit_run_id' => $auditRun->getId(),
                'error' => $e->getMessage()
            ]);

            $auditRun->setState(\App\Entity\AuditRunState::FAILED);
            $auditRun->setError($e->getMessage());
            $auditRun->setFinishedAt(new \DateTimeImmutable());
            $this->entityManager->flush();
        }
    }

    private function crawlBatch(array $urls, array $config): array
    {
        $results = [];
        
        foreach ($urls as $url) {
            try {
                $resource = $this->crawler->fetch($url, $config);
                $results[] = [
                    'url' => $url,
                    'resource' => $resource,
                    'success' => !$resource->error
                ];
            } catch (\Exception $e) {
                $this->logger->warning('Failed to crawl URL', [
                    'url' => $url,
                    'error' => $e->getMessage()
                ]);
                
                $results[] = [
                    'url' => $url,
                    'resource' => null,
                    'success' => false,
                    'error' => $e->getMessage()
                ];
            }
        }

        return $results;
    }

    private function createPageEntity(FetchedResource $resource, AuditRun $auditRun): Page
    {
        $page = new Page();
        $page->setAuditRun($auditRun);
        $page->setUrl($resource->url);
        $page->setStatusCode($resource->statusCode);
        $page->setContentType($resource->contentType);
        $page->setContentLength($resource->contentLength);
        $page->setResponseTime($resource->responseTime);
        $page->setHeaders($resource->headers);
        $page->setCanonicalUrl($resource->canonicalUrl);
        $page->setRobotsDirectives($resource->robotsDirectives);
        $page->setBodyHash($resource->getBodyHash());
        $page->setHtmlPath($resource->htmlPath);
        $page->setScreenshotPath($resource->screenshotPath);

        // Parse HTML content for additional data
        if ($resource->isHtml()) {
            $this->parseHtmlContent($page, $resource->body);
        }

        // Determine if page is indexable
        $page->setIsIndexable($this->isPageIndexable($resource));

        return $page;
    }

    private function parseHtmlContent(Page $page, string $html): void
    {
        try {
            $crawler = new \Symfony\Component\DomCrawler\Crawler($html);

            // Extract title
            $titleElement = $crawler->filter('title');
            if ($titleElement->count() > 0) {
                $page->setTitle(trim($titleElement->text()));
            }

            // Extract meta description
            $metaDescElement = $crawler->filter('meta[name="description"]');
            if ($metaDescElement->count() > 0) {
                $page->setMetaDescription($metaDescElement->attr('content'));
            }

            // Count words in body text
            $bodyText = $crawler->filter('body')->text();
            $wordCount = str_word_count(strip_tags($bodyText));
            $page->setWordCount($wordCount);

        } catch (\Exception $e) {
            $this->logger->warning('Failed to parse HTML content', [
                'url' => $page->getUrl(),
                'error' => $e->getMessage()
            ]);
        }
    }

    private function isPageIndexable(FetchedResource $resource): bool
    {
        // Check status code
        if (!$resource->isSuccessful()) {
            return false;
        }

        // Check robots directives
        if ($resource->hasRobotsDirective('noindex')) {
            return false;
        }

        // Check content type
        if (!$resource->isHtml()) {
            return false;
        }

        return true;
    }

    private function getCrawlDuration(AuditRun $auditRun): float
    {
        if (!$auditRun->getStartedAt()) {
            return 0.0;
        }

        $endTime = $auditRun->getFinishedAt() ?? new \DateTimeImmutable();
        return $endTime->getTimestamp() - $auditRun->getStartedAt()->getTimestamp();
    }

    public function getCrawlStats(AuditRun $auditRun): array
    {
        $pages = $auditRun->getPages();
        
        $stats = [
            'total_pages' => $pages->count(),
            'successful_pages' => 0,
            'failed_pages' => 0,
            'redirect_pages' => 0,
            'client_error_pages' => 0,
            'server_error_pages' => 0,
            'indexable_pages' => 0,
            'non_indexable_pages' => 0,
            'total_response_time' => 0.0,
            'average_response_time' => 0.0,
            'total_content_length' => 0,
            'average_content_length' => 0,
        ];

        foreach ($pages as $page) {
            if ($page->isSuccessful()) {
                $stats['successful_pages']++;
            } elseif ($page->isRedirect()) {
                $stats['redirect_pages']++;
            } elseif ($page->isClientError()) {
                $stats['client_error_pages']++;
            } elseif ($page->isServerError()) {
                $stats['server_error_pages']++;
            } else {
                $stats['failed_pages']++;
            }

            if ($page->isIndexable()) {
                $stats['indexable_pages']++;
            } else {
                $stats['non_indexable_pages']++;
            }

            $stats['total_response_time'] += $page->getResponseTime();
            $stats['total_content_length'] += $page->getContentLength();
        }

        if ($stats['total_pages'] > 0) {
            $stats['average_response_time'] = $stats['total_response_time'] / $stats['total_pages'];
            $stats['average_content_length'] = $stats['total_content_length'] / $stats['total_pages'];
        }

        return $stats;
    }
}
