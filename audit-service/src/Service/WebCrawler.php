<?php

declare(strict_types=1);

namespace App\Service;

use App\ValueObject\FetchedResource;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Symfony\Component\DomCrawler\Crawler as DomCrawler;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Uid\Uuid;

class WebCrawler implements CrawlerInterface
{
    private const DEFAULT_USER_AGENT = 'CounselRank-SEO-Audit/1.0 (+https://counselrank.legal/audit-service)';
    private const DEFAULT_TIMEOUT = 30;
    private const DEFAULT_MAX_REDIRECTS = 10;
    private const DEFAULT_MAX_SIZE = 10 * 1024 * 1024; // 10MB

    private HttpClientInterface $httpClient;
    private LoggerInterface $logger;
    private Filesystem $filesystem;
    private string $storagePath;
    private array $robotsCache = [];

    public function __construct(
        LoggerInterface $logger,
        string $storagePath = '/tmp/audit-storage'
    ) {
        $this->logger = $logger;
        $this->storagePath = $storagePath;
        $this->filesystem = new Filesystem();
        
        // Ensure storage directory exists
        if (!$this->filesystem->exists($this->storagePath)) {
            $this->filesystem->mkdir($this->storagePath);
        }

        $this->httpClient = HttpClient::create([
            'timeout' => self::DEFAULT_TIMEOUT,
            'max_redirects' => self::DEFAULT_MAX_REDIRECTS,
            'headers' => [
                'User-Agent' => self::DEFAULT_USER_AGENT,
                'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                'Accept-Language' => 'en-US,en;q=0.5',
                'Accept-Encoding' => 'gzip, deflate',
                'Connection' => 'keep-alive',
                'Upgrade-Insecure-Requests' => '1',
            ],
        ]);
    }

    public function fetch(string $url, array $options = []): FetchedResource
    {
        $startTime = microtime(true);
        
        try {
            // Normalize URL
            $normalizedUrl = $this->normalizeUrl($url);
            
            // Check if we should crawl this URL
            if (!$this->shouldCrawl($normalizedUrl, $options)) {
                return new FetchedResource(
                    url: $normalizedUrl,
                    statusCode: 403,
                    contentType: 'text/plain',
                    body: '',
                    headers: [],
                    responseTime: 0,
                    contentLength: 0,
                    error: 'URL blocked by robots.txt or crawl rules'
                );
            }

            $this->logger->info('Fetching URL', ['url' => $normalizedUrl]);

            // Prepare request options
            $requestOptions = $this->prepareRequestOptions($options);
            
            // Make HTTP request
            $response = $this->httpClient->request('GET', $normalizedUrl, $requestOptions);
            
            // Get response data
            $statusCode = $response->getStatusCode();
            $headers = $this->normalizeHeaders($response->getHeaders());
            $contentType = $headers['content-type'] ?? 'application/octet-stream';
            $body = $response->getContent();
            $contentLength = strlen($body);
            $responseTime = microtime(true) - $startTime;

            // Parse robots directives from meta tags
            $robotsDirectives = $this->parseRobotsDirectives($body, $headers);
            
            // Extract canonical URL
            $canonicalUrl = $this->extractCanonicalUrl($body, $normalizedUrl);

            // Store HTML if requested
            $htmlPath = null;
            if ($options['store_html'] ?? true) {
                $htmlPath = $this->storeHtml($body, $normalizedUrl);
            }

            // Take screenshot if requested
            $screenshotPath = null;
            if ($options['take_screenshot'] ?? false) {
                $screenshotPath = $this->takeScreenshot($normalizedUrl, $options);
            }

            $this->logger->info('Successfully fetched URL', [
                'url' => $normalizedUrl,
                'status_code' => $statusCode,
                'content_length' => $contentLength,
                'response_time' => $responseTime
            ]);

            return new FetchedResource(
                url: $normalizedUrl,
                statusCode: $statusCode,
                contentType: $contentType,
                body: $body,
                headers: $headers,
                responseTime: $responseTime,
                contentLength: $contentLength,
                canonicalUrl: $canonicalUrl,
                robotsDirectives: $robotsDirectives,
                screenshotPath: $screenshotPath,
                htmlPath: $htmlPath
            );

        } catch (\Exception $e) {
            $responseTime = microtime(true) - $startTime;
            
            $this->logger->error('Failed to fetch URL', [
                'url' => $url,
                'error' => $e->getMessage(),
                'response_time' => $responseTime
            ]);

            return new FetchedResource(
                url: $url,
                statusCode: 0,
                contentType: 'text/plain',
                body: '',
                headers: [],
                responseTime: $responseTime,
                contentLength: 0,
                error: $e->getMessage()
            );
        }
    }

    public function shouldCrawl(string $url, array $options = []): bool
    {
        try {
            $parsedUrl = parse_url($url);
            if (!$parsedUrl || !isset($parsedUrl['host'])) {
                return false;
            }

            $host = $parsedUrl['host'];
            $path = $parsedUrl['path'] ?? '/';

            // Check robots.txt
            if (!$this->isAllowedByRobots($host, $path)) {
                return false;
            }

            // Check custom crawl rules
            $allowedPaths = $options['allowed_paths'] ?? [];
            $blockedPaths = $options['blocked_paths'] ?? [];

            if (!empty($allowedPaths)) {
                $isAllowed = false;
                foreach ($allowedPaths as $allowedPath) {
                    if (str_starts_with($path, $allowedPath)) {
                        $isAllowed = true;
                        break;
                    }
                }
                if (!$isAllowed) {
                    return false;
                }
            }

            if (!empty($blockedPaths)) {
                foreach ($blockedPaths as $blockedPath) {
                    if (str_starts_with($path, $blockedPath)) {
                        return false;
                    }
                }
            }

            return true;

        } catch (\Exception $e) {
            $this->logger->warning('Error checking crawl rules', [
                'url' => $url,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    public function discoverUrls(FetchedResource $resource, array $options = []): array
    {
        if (!$resource->isHtml()) {
            return [];
        }

        try {
            $crawler = new DomCrawler($resource->body);
            $baseUrl = $resource->url;
            $discoveredUrls = [];

            // Extract links using DomCrawler extract method
            $hrefs = $crawler->filter('a[href]')->extract(['href']);
            foreach ($hrefs as $href) {
                if ($href) {
                    $absoluteUrl = $this->resolveUrl($href, $baseUrl);
                    if ($absoluteUrl && $this->isSameDomain($absoluteUrl, $baseUrl)) {
                        $discoveredUrls[] = $absoluteUrl;
                    }
                }
            }

            // Extract sitemap URLs from robots.txt
            $robotsUrls = $this->extractSitemapUrls($baseUrl);
            $discoveredUrls = array_merge($discoveredUrls, $robotsUrls);

            // Remove duplicates and normalize
            $discoveredUrls = array_unique($discoveredUrls);
            $discoveredUrls = array_map([$this, 'normalizeUrl'], $discoveredUrls);

            $this->logger->info('Discovered URLs', [
                'base_url' => $baseUrl,
                'count' => count($discoveredUrls)
            ]);

            return $discoveredUrls;

        } catch (\Exception $e) {
            $this->logger->error('Error discovering URLs', [
                'url' => $resource->url,
                'error' => $e->getMessage()
            ]);
            return [];
        }
    }

    public function takeScreenshot(string $url, array $options = []): ?string
    {
        // This would integrate with Chrome PHP or similar
        // For now, return null as it's optional
        return null;
    }

    private function normalizeUrl(string $url): string
    {
        $parsed = parse_url($url);
        if (!$parsed) {
            return $url;
        }

        // Remove fragment
        unset($parsed['fragment']);

        // Normalize path
        if (isset($parsed['path'])) {
            $parsed['path'] = rtrim($parsed['path'], '/') ?: '/';
        }

        return $this->buildUrl($parsed);
    }

    private function buildUrl(array $parsed): string
    {
        $url = '';
        
        if (isset($parsed['scheme'])) {
            $url .= $parsed['scheme'] . '://';
        }
        
        if (isset($parsed['host'])) {
            $url .= $parsed['host'];
        }
        
        if (isset($parsed['port'])) {
            $url .= ':' . $parsed['port'];
        }
        
        if (isset($parsed['path'])) {
            $url .= $parsed['path'];
        }
        
        if (isset($parsed['query'])) {
            $url .= '?' . $parsed['query'];
        }

        return $url;
    }

    private function prepareRequestOptions(array $options): array
    {
        $requestOptions = [];

        if (isset($options['timeout'])) {
            $requestOptions['timeout'] = $options['timeout'];
        }

        if (isset($options['headers'])) {
            $requestOptions['headers'] = array_merge(
                $requestOptions['headers'] ?? [],
                $options['headers']
            );
        }

        if (isset($options['max_redirects'])) {
            $requestOptions['max_redirects'] = $options['max_redirects'];
        }

        return $requestOptions;
    }

    private function normalizeHeaders(array $headers): array
    {
        $normalized = [];
        foreach ($headers as $name => $values) {
            $normalized[strtolower($name)] = is_array($values) ? $values[0] : $values;
        }
        return $normalized;
    }

    private function parseRobotsDirectives(string $body, array $headers): array
    {
        $directives = [];

        // Check X-Robots-Tag header
        if (isset($headers['x-robots-tag'])) {
            $directives = array_merge($directives, explode(',', $headers['x-robots-tag']));
        }

        // Check meta robots tag
        if (preg_match('/<meta[^>]+name=["\']robots["\'][^>]+content=["\']([^"\']+)["\'][^>]*>/i', $body, $matches)) {
            $directives = array_merge($directives, explode(',', $matches[1]));
        }

        return array_map('trim', $directives);
    }

    private function extractCanonicalUrl(string $body, string $baseUrl): ?string
    {
        if (preg_match('/<link[^>]+rel=["\']canonical["\'][^>]+href=["\']([^"\']+)["\'][^>]*>/i', $body, $matches)) {
            return $this->resolveUrl($matches[1], $baseUrl);
        }
        return null;
    }

    private function resolveUrl(string $url, string $baseUrl): ?string
    {
        if (empty($url)) {
            return null;
        }

        // Already absolute URL
        if (parse_url($url, PHP_URL_SCHEME)) {
            return $url;
        }

        $parsedBase = parse_url($baseUrl);
        if (!$parsedBase) {
            return null;
        }

        if (str_starts_with($url, '//')) {
            return $parsedBase['scheme'] . ':' . $url;
        }

        if (str_starts_with($url, '/')) {
            return $parsedBase['scheme'] . '://' . $parsedBase['host'] . 
                   (isset($parsedBase['port']) ? ':' . $parsedBase['port'] : '') . $url;
        }

        // Relative URL
        $basePath = dirname($parsedBase['path'] ?? '/');
        if ($basePath === '.') {
            $basePath = '/';
        }
        
        return $parsedBase['scheme'] . '://' . $parsedBase['host'] . 
               (isset($parsedBase['port']) ? ':' . $parsedBase['port'] : '') . 
               $basePath . '/' . $url;
    }

    private function isSameDomain(string $url1, string $url2): bool
    {
        $host1 = parse_url($url1, PHP_URL_HOST);
        $host2 = parse_url($url2, PHP_URL_HOST);
        
        return $host1 === $host2;
    }

    private function isAllowedByRobots(string $host, string $path): bool
    {
        $cacheKey = $host;
        
        if (!isset($this->robotsCache[$cacheKey])) {
            $this->robotsCache[$cacheKey] = $this->fetchRobotsTxt($host);
        }

        $robotsRules = $this->robotsCache[$cacheKey];
        
        // Check if path is disallowed
        foreach ($robotsRules['disallow'] as $disallowPath) {
            if (str_starts_with($path, $disallowPath)) {
                return false;
            }
        }

        return true;
    }

    private function fetchRobotsTxt(string $host): array
    {
        try {
            $robotsUrl = "https://{$host}/robots.txt";
            $response = $this->httpClient->request('GET', $robotsUrl, [
                'timeout' => 10,
                'max_redirects' => 3
            ]);

            if ($response->getStatusCode() !== 200) {
                return ['disallow' => [], 'sitemaps' => []];
            }

            $content = $response->getContent();
            return $this->parseRobotsTxt($content);

        } catch (\Exception $e) {
            $this->logger->warning('Failed to fetch robots.txt', [
                'host' => $host,
                'error' => $e->getMessage()
            ]);
            return ['disallow' => [], 'sitemaps' => []];
        }
    }

    private function parseRobotsTxt(string $content): array
    {
        $disallow = [];
        $sitemaps = [];
        $lines = explode("\n", $content);
        $inUserAgent = false;

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line) || str_starts_with($line, '#')) {
                continue;
            }

            if (preg_match('/^User-agent:\s*(.+)$/i', $line, $matches)) {
                $userAgent = trim($matches[1]);
                $inUserAgent = $userAgent === '*' || str_contains($userAgent, 'CounselRank');
            } elseif ($inUserAgent && preg_match('/^Disallow:\s*(.+)$/i', $line, $matches)) {
                $disallow[] = trim($matches[1]);
            } elseif (preg_match('/^Sitemap:\s*(.+)$/i', $line, $matches)) {
                $sitemaps[] = trim($matches[1]);
            }
        }

        return ['disallow' => $disallow, 'sitemaps' => $sitemaps];
    }

    private function extractSitemapUrls(string $baseUrl): array
    {
        $host = parse_url($baseUrl, PHP_URL_HOST);
        if (!$host) {
            return [];
        }

        $robotsRules = $this->robotsCache[$host] ?? $this->fetchRobotsTxt($host);
        return $robotsRules['sitemaps'] ?? [];
    }

    private function storeHtml(string $body, string $url): string
    {
        $filename = Uuid::v4()->toRfc4122() . '.html';
        $filePath = $this->storagePath . '/html/' . $filename;
        
        $this->filesystem->mkdir(dirname($filePath));
        $this->filesystem->dumpFile($filePath, $body);
        
        return $filePath;
    }
}
