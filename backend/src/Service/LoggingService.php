<?php

namespace App\Service;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class LoggingService
{
    private LoggerInterface $logger;
    private LoggerInterface $securityLogger;
    private LoggerInterface $apiLogger;
    private LoggerInterface $performanceLogger;
    private LoggerInterface $auditLogger;

    public function __construct(
        LoggerInterface $logger,
        LoggerInterface $securityLogger,
        LoggerInterface $apiLogger,
        LoggerInterface $performanceLogger,
        LoggerInterface $auditLogger
    ) {
        $this->logger = $logger;
        $this->securityLogger = $securityLogger;
        $this->apiLogger = $apiLogger;
        $this->performanceLogger = $performanceLogger;
        $this->auditLogger = $auditLogger;
    }

    /**
     * Log API requests and responses
     */
    public function logApiRequest(Request $request, Response $response, float $executionTime): void
    {
        $this->apiLogger->info('API Request', [
            'method' => $request->getMethod(),
            'uri' => $request->getUri(),
            'user_agent' => $request->headers->get('User-Agent'),
            'ip' => $request->getClientIp(),
            'status_code' => $response->getStatusCode(),
            'execution_time' => $executionTime,
            'memory_usage' => memory_get_usage(true),
            'timestamp' => new \DateTime(),
        ]);
    }

    /**
     * Log security events
     */
    public function logSecurityEvent(string $event, array $context = []): void
    {
        $this->securityLogger->warning('Security Event', array_merge([
            'event' => $event,
            'timestamp' => new \DateTime(),
        ], $context));
    }

    /**
     * Log authentication events
     */
    public function logAuthentication(string $action, string $userIdentifier, bool $success, array $context = []): void
    {
        $this->securityLogger->info('Authentication Event', array_merge([
            'action' => $action,
            'user_identifier' => $userIdentifier,
            'success' => $success,
            'timestamp' => new \DateTime(),
        ], $context));
    }

    /**
     * Log performance metrics
     */
    public function logPerformance(string $operation, float $executionTime, array $context = []): void
    {
        $this->performanceLogger->info('Performance Metric', array_merge([
            'operation' => $operation,
            'execution_time' => $executionTime,
            'memory_usage' => memory_get_usage(true),
            'peak_memory' => memory_get_peak_usage(true),
            'timestamp' => new \DateTime(),
        ], $context));
    }

    /**
     * Log audit trail events
     */
    public function logAuditEvent(string $action, string $resource, array $context = []): void
    {
        $this->auditLogger->info('Audit Event', array_merge([
            'action' => $action,
            'resource' => $resource,
            'timestamp' => new \DateTime(),
        ], $context));
    }

    /**
     * Log database operations
     */
    public function logDatabaseOperation(string $operation, string $table, array $context = []): void
    {
        $this->auditLogger->info('Database Operation', array_merge([
            'operation' => $operation,
            'table' => $table,
            'timestamp' => new \DateTime(),
        ], $context));
    }

    /**
     * Log business events
     */
    public function logBusinessEvent(string $event, array $context = []): void
    {
        $this->auditLogger->info('Business Event', array_merge([
            'event' => $event,
            'timestamp' => new \DateTime(),
        ], $context));
    }

    /**
     * Log errors with context
     */
    public function logError(\Throwable $exception, array $context = []): void
    {
        $this->logger->error('Application Error', array_merge([
            'message' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTraceAsString(),
            'timestamp' => new \DateTime(),
        ], $context));
    }
}
