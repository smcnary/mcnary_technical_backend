<?php

namespace App\Service;

use Doctrine\DBAL\Connection;
use Psr\Log\LoggerInterface;
use Psr\Cache\CacheItemPoolInterface;

class MonitoringService
{
    private LoggerInterface $logger;
    private Connection $connection;
    private CacheItemPoolInterface $cache;

    public function __construct(
        LoggerInterface $logger,
        Connection $connection,
        CacheItemPoolInterface $cache
    ) {
        $this->logger = $logger;
        $this->connection = $connection;
        $this->cache = $cache;
    }

    /**
     * Get application health status
     */
    public function getHealthStatus(): array
    {
        $status = [
            'status' => 'healthy',
            'timestamp' => new \DateTime(),
            'checks' => []
        ];

        // Database connectivity check
        try {
            $this->connection->executeQuery('SELECT 1');
            $status['checks']['database'] = [
                'status' => 'healthy',
                'response_time' => $this->measureDatabaseResponseTime()
            ];
        } catch (\Exception $e) {
            $status['checks']['database'] = [
                'status' => 'unhealthy',
                'error' => $e->getMessage()
            ];
            $status['status'] = 'unhealthy';
        }

        // Cache connectivity check
        try {
            $testKey = 'health_check_' . uniqid();
            $item = $this->cache->getItem($testKey);
            $item->set('test');
            $item->expiresAfter(10);
            $this->cache->save($item);
            $this->cache->deleteItem($testKey);
            $status['checks']['cache'] = [
                'status' => 'healthy',
                'response_time' => $this->measureCacheResponseTime()
            ];
        } catch (\Exception $e) {
            $status['checks']['cache'] = [
                'status' => 'unhealthy',
                'error' => $e->getMessage()
            ];
            $status['status'] = 'unhealthy';
        }

        // Memory usage check
        $memoryUsage = memory_get_usage(true);
        $memoryLimit = ini_get('memory_limit');
        $memoryLimitBytes = $this->convertToBytes($memoryLimit);
        $memoryUsagePercent = ($memoryUsage / $memoryLimitBytes) * 100;

        $status['checks']['memory'] = [
            'status' => $memoryUsagePercent > 90 ? 'warning' : 'healthy',
            'usage_bytes' => $memoryUsage,
            'usage_percent' => round($memoryUsagePercent, 2),
            'limit' => $memoryLimit
        ];

        if ($memoryUsagePercent > 95) {
            $status['status'] = 'unhealthy';
        }

        // Disk space check
        $diskFreeBytes = disk_free_space('.');
        $diskTotalBytes = disk_total_space('.');
        $diskUsagePercent = (($diskTotalBytes - $diskFreeBytes) / $diskTotalBytes) * 100;

        $status['checks']['disk'] = [
            'status' => $diskUsagePercent > 90 ? 'warning' : 'healthy',
            'free_bytes' => $diskFreeBytes,
            'total_bytes' => $diskTotalBytes,
            'usage_percent' => round($diskUsagePercent, 2)
        ];

        if ($diskUsagePercent > 95) {
            $status['status'] = 'unhealthy';
        }

        return $status;
    }

    /**
     * Get performance metrics
     */
    public function getPerformanceMetrics(): array
    {
        return [
            'timestamp' => new \DateTime(),
            'memory' => [
                'current' => memory_get_usage(true),
                'peak' => memory_get_peak_usage(true),
                'limit' => ini_get('memory_limit')
            ],
            'opcache' => [
                'enabled' => function_exists('opcache_get_status'),
                'status' => function_exists('opcache_get_status') ? opcache_get_status() : null
            ],
            'php' => [
                'version' => PHP_VERSION,
                'sapi' => PHP_SAPI,
                'extensions' => get_loaded_extensions()
            ],
            'system' => [
                'load_average' => function_exists('sys_getloadavg') ? sys_getloadavg() : null,
                'uptime' => $this->getSystemUptime()
            ]
        ];
    }

    /**
     * Log performance metrics
     */
    public function logPerformanceMetrics(): void
    {
        $metrics = $this->getPerformanceMetrics();
        $this->logger->info('Performance Metrics', $metrics);
    }

    /**
     * Measure database response time
     */
    private function measureDatabaseResponseTime(): float
    {
        $start = microtime(true);
        $this->connection->executeQuery('SELECT 1');
        return microtime(true) - $start;
    }

    /**
     * Measure cache response time
     */
    private function measureCacheResponseTime(): float
    {
        $testKey = 'performance_test_' . uniqid();
        $start = microtime(true);
        $item = $this->cache->getItem($testKey);
        $item->set('test');
        $item->expiresAfter(10);
        $this->cache->save($item);
        $this->cache->deleteItem($testKey);
        return microtime(true) - $start;
    }

    /**
     * Convert memory limit string to bytes
     */
    private function convertToBytes(string $memoryLimit): int
    {
        $memoryLimit = trim($memoryLimit);
        $last = strtolower($memoryLimit[strlen($memoryLimit) - 1]);
        $memoryLimit = (int) $memoryLimit;

        switch ($last) {
            case 'g':
                $memoryLimit *= 1024;
            case 'm':
                $memoryLimit *= 1024;
            case 'k':
                $memoryLimit *= 1024;
        }

        return $memoryLimit;
    }

    /**
     * Get system uptime
     */
    private function getSystemUptime(): ?int
    {
        if (PHP_OS_FAMILY === 'Linux') {
            $uptime = @file_get_contents('/proc/uptime');
            if ($uptime !== false) {
                return (int) explode(' ', $uptime)[0];
            }
        }
        return null;
    }
}
