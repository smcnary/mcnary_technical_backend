<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\Entity\AuditRun;
use App\Message\CrawlAuditRunMessage;
use App\Service\CrawlerService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class CrawlAuditRunMessageHandler
{
    public function __construct(
        private CrawlerService $crawlerService,
        private EntityManagerInterface $entityManager,
        private LoggerInterface $logger
    ) {}

    public function __invoke(CrawlAuditRunMessage $message): void
    {
        $auditRunId = $message->getAuditRunId();
        
        $this->logger->info('Processing crawl message', [
            'audit_run_id' => $auditRunId
        ]);

        try {
            $auditRun = $this->entityManager->find(AuditRun::class, $auditRunId);
            
            if (!$auditRun) {
                $this->logger->error('Audit run not found', [
                    'audit_run_id' => $auditRunId
                ]);
                return;
            }

            $this->crawlerService->crawlAuditRun($auditRun);

        } catch (\Exception $e) {
            $this->logger->error('Failed to process crawl message', [
                'audit_run_id' => $auditRunId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Update audit run to failed state
            try {
                $auditRun = $this->entityManager->find(AuditRun::class, $auditRunId);
                if ($auditRun) {
                    $auditRun->setState(\App\Entity\AuditRunState::FAILED);
                    $auditRun->setError($e->getMessage());
                    $auditRun->setFinishedAt(new \DateTimeImmutable());
                    $this->entityManager->flush();
                }
            } catch (\Exception $updateException) {
                $this->logger->error('Failed to update audit run state', [
                    'audit_run_id' => $auditRunId,
                    'error' => $updateException->getMessage()
                ]);
            }
        }
    }
}
