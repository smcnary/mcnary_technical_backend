<?php

namespace App\EventListener;

use App\Service\LoggingService;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

#[AsEventListener(event: KernelEvents::REQUEST, priority: 1000)]
#[AsEventListener(event: KernelEvents::RESPONSE, priority: -1000)]
class ApiLoggingListener
{
    private LoggingService $loggingService;
    private array $requestStartTimes = [];

    public function __construct(LoggingService $loggingService)
    {
        $this->loggingService = $loggingService;
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();
        
        // Only log API requests
        if (str_starts_with($request->getPathInfo(), '/api/')) {
            $this->requestStartTimes[$request->getRequestUri()] = microtime(true);
        }
    }

    public function onKernelResponse(ResponseEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();
        $response = $event->getResponse();
        
        // Only log API requests
        if (str_starts_with($request->getPathInfo(), '/api/')) {
            $requestUri = $request->getRequestUri();
            $startTime = $this->requestStartTimes[$requestUri] ?? microtime(true);
            $executionTime = microtime(true) - $startTime;
            
            $this->loggingService->logApiRequest($request, $response, $executionTime);
            
            // Clean up
            unset($this->requestStartTimes[$requestUri]);
        }
    }
}
