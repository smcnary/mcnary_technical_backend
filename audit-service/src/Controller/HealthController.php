<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\DBAL\Connection;
use Redis;

#[Route('/api')]
class HealthController extends AbstractController
{
    #[Route('/health', name: 'api_health', methods: ['GET'])]
    public function health(): JsonResponse
    {
        return new JsonResponse([
            'status' => 'healthy',
            'timestamp' => (new \DateTime())->format('c'),
            'service' => 'audit-service',
            'version' => '1.0.0'
        ]);
    }

    #[Route('/health/detailed', name: 'api_health_detailed', methods: ['GET'])]
    public function detailedHealth(Connection $connection): JsonResponse
    {
        $health = [
            'status' => 'healthy',
            'timestamp' => (new \DateTime())->format('c'),
            'service' => 'audit-service',
            'version' => '1.0.0',
            'checks' => []
        ];

        // Database health check
        try {
            $connection->executeQuery('SELECT 1');
            $health['checks']['database'] = 'healthy';
        } catch (\Exception $e) {
            $health['checks']['database'] = 'unhealthy';
            $health['status'] = 'degraded';
        }

        // Redis health check (if available)
        try {
            $redis = new Redis();
            $redis->connect('redis', 6379);
            $redis->ping();
            $health['checks']['redis'] = 'healthy';
        } catch (\Exception $e) {
            $health['checks']['redis'] = 'unhealthy';
            $health['status'] = 'degraded';
        }

        // File system health check
        try {
            $cacheDir = $this->getParameter('kernel.cache_dir');
            if (is_writable($cacheDir)) {
                $health['checks']['filesystem'] = 'healthy';
            } else {
                $health['checks']['filesystem'] = 'unhealthy';
                $health['status'] = 'degraded';
            }
        } catch (\Exception $e) {
            $health['checks']['filesystem'] = 'unhealthy';
            $health['status'] = 'degraded';
        }

        $statusCode = $health['status'] === 'healthy' ? Response::HTTP_OK : Response::HTTP_SERVICE_UNAVAILABLE;

        return new JsonResponse($health, $statusCode);
    }

    #[Route('/metrics', name: 'api_metrics', methods: ['GET'])]
    public function metrics(): Response
    {
        $metrics = [
            '# HELP audit_service_requests_total Total number of requests',
            '# TYPE audit_service_requests_total counter',
            'audit_service_requests_total{method="GET",endpoint="/health"} 1',
            '',
            '# HELP audit_service_uptime_seconds Service uptime in seconds',
            '# TYPE audit_service_uptime_seconds gauge',
            'audit_service_uptime_seconds ' . time(),
            '',
            '# HELP audit_service_memory_bytes Current memory usage in bytes',
            '# TYPE audit_service_memory_bytes gauge',
            'audit_service_memory_bytes ' . memory_get_usage(true),
            '',
            '# HELP audit_service_memory_peak_bytes Peak memory usage in bytes',
            '# TYPE audit_service_memory_peak_bytes gauge',
            'audit_service_memory_peak_bytes ' . memory_get_peak_usage(true)
        ];

        return new Response(implode("\n", $metrics), Response::HTTP_OK, [
            'Content-Type' => 'text/plain; version=0.0.4; charset=utf-8'
        ]);
    }
}
