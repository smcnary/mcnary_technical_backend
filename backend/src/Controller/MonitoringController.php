<?php

namespace App\Controller;

use App\Service\MonitoringService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/v1/monitoring', name: 'api_monitoring_')]
class MonitoringController extends AbstractController
{
    private MonitoringService $monitoringService;

    public function __construct(MonitoringService $monitoringService)
    {
        $this->monitoringService = $monitoringService;
    }

    #[Route('/health', name: 'health', methods: ['GET'])]
    #[IsGranted('PUBLIC_ACCESS')]
    public function health(): JsonResponse
    {
        $healthStatus = $this->monitoringService->getHealthStatus();
        
        $statusCode = $healthStatus['status'] === 'healthy' ? 200 : 503;
        
        return new JsonResponse($healthStatus, $statusCode);
    }

    #[Route('/metrics', name: 'metrics', methods: ['GET'])]
    public function metrics(): JsonResponse
    {
        $metrics = $this->monitoringService->getPerformanceMetrics();
        
        return new JsonResponse($metrics);
    }

    #[Route('/ready', name: 'ready', methods: ['GET'])]
    #[IsGranted('PUBLIC_ACCESS')]
    public function ready(): JsonResponse
    {
        $healthStatus = $this->monitoringService->getHealthStatus();
        
        // For readiness check, we only care about critical services
        $isReady = $healthStatus['checks']['database']['status'] === 'healthy';
        
        return new JsonResponse([
            'ready' => $isReady,
            'timestamp' => new \DateTime()
        ], $isReady ? 200 : 503);
    }

    #[Route('/live', name: 'live', methods: ['GET'])]
    #[IsGranted('PUBLIC_ACCESS')]
    public function live(): JsonResponse
    {
        // Liveness check - just verify the application is responding
        return new JsonResponse([
            'alive' => true,
            'timestamp' => new \DateTime()
        ]);
    }
}
