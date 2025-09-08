<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\Entity\AuditRun;
use App\Message\AnalyzeAuditRunMessage;
use App\Service\SeoAnalyzer;
use App\Service\SeoScorer;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class AnalyzeAuditRunMessageHandler
{
    public function __construct(
        private SeoAnalyzer $seoAnalyzer,
        private SeoScorer $seoScorer,
        private EntityManagerInterface $entityManager,
        private LoggerInterface $logger
    ) {}

    public function __invoke(AnalyzeAuditRunMessage $message): void
    {
        $auditRunId = $message->getAuditRunId();
        
        $this->logger->info('Processing SEO analysis message', [
            'audit_run_id' => $auditRunId
        ]);

        try {
            // Run SEO analysis on all pages
            $findings = $this->seoAnalyzer->analyzeAuditRun($auditRunId);
            
            // Calculate scores
            $scorecard = $this->seoScorer->score($auditRunId);
            
            // Update audit run with final scores
            $auditRun = $this->entityManager->find(AuditRun::class, $auditRunId);
            if ($auditRun) {
                $totals = $auditRun->getTotals();
                $totals['scores'] = [
                    'overall' => $scorecard->overallScore,
                    'categories' => $scorecard->categoryScores,
                    'metrics' => $scorecard->metrics,
                ];
                $totals['quick_wins'] = $scorecard->quickWins;
                $auditRun->setTotals($totals);
                $this->entityManager->flush();
            }

            $this->logger->info('Completed SEO analysis', [
                'audit_run_id' => $auditRunId,
                'findings_count' => count($findings),
                'overall_score' => $scorecard->overallScore
            ]);

        } catch (\Exception $e) {
            $this->logger->error('SEO analysis failed', [
                'audit_run_id' => $auditRunId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Update audit run to failed state
            try {
                $auditRun = $this->entityManager->find(AuditRun::class, $auditRunId);
                if ($auditRun) {
                    $auditRun->setState(\App\Entity\AuditRunState::FAILED);
                    $auditRun->setError('SEO analysis failed: ' . $e->getMessage());
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
