<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\AuditRun;
use App\Entity\AuditRunState;
use App\Entity\Tenant;
use App\Entity\User;
use App\Message\AnalyzeAuditRunMessage;
use App\Service\SeoAnalyzer;
use App\Service\SeoScorer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsCommand(
    name: 'audit:analyze',
    description: 'Run SEO analysis on an audit run'
)]
class AnalyzeAuditRunCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private MessageBusInterface $messageBus,
        private SeoAnalyzer $seoAnalyzer,
        private SeoScorer $seoScorer
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('run-id', InputArgument::REQUIRED, 'Audit run ID to analyze')
            ->addOption('sync', 's', InputOption::VALUE_NONE, 'Run synchronously instead of queuing')
            ->addOption('tenant', 't', InputOption::VALUE_OPTIONAL, 'Tenant ID')
            ->addOption('user', 'u', InputOption::VALUE_OPTIONAL, 'User ID')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        
        $runId = $input->getArgument('run-id');
        $sync = $input->getOption('sync');
        $tenantId = $input->getOption('tenant');
        $userId = $input->getOption('user');

        // Get audit run
        $auditRun = $this->entityManager->find(AuditRun::class, $runId);
        if (!$auditRun) {
            $io->error('Audit run not found');
            return Command::FAILURE;
        }

        // Check if audit run has pages to analyze
        if ($auditRun->getPages()->count() === 0) {
            $io->error('No pages found in audit run. Run crawl first.');
            return Command::FAILURE;
        }

        $io->info(sprintf('Found audit run with %d pages', $auditRun->getPages()->count()));

        if ($sync) {
            $io->note('Running analysis synchronously...');
            
            try {
                // Run analysis
                $findings = $this->seoAnalyzer->analyzeAuditRun($runId);
                
                // Calculate scores
                $scorecard = $this->seoScorer->score($runId);
                
                $io->success('Analysis completed successfully');
                $io->table(['Metric', 'Value'], [
                    ['Overall Score', $scorecard->overallScore . '%'],
                    ['Technical Score', $scorecard->getCategoryScore('technical') . '%'],
                    ['On-Page Score', $scorecard->getCategoryScore('onpage') . '%'],
                    ['Local Score', $scorecard->getCategoryScore('local') . '%'],
                    ['Total Findings', $scorecard->totalFindings],
                    ['Critical Issues', $scorecard->criticalFindings],
                    ['High Issues', $scorecard->highFindings],
                    ['Medium Issues', $scorecard->mediumFindings],
                    ['Low Issues', $scorecard->lowFindings],
                ]);

                if (!empty($scorecard->quickWins)) {
                    $io->section('Quick Wins');
                    $quickWinsData = array_map(fn($win) => [
                        $win['title'],
                        $win['impact_score'],
                        $win['effort']
                    ], $scorecard->quickWins);
                    $io->table(['Title', 'Impact Score', 'Effort'], $quickWinsData);
                }

            } catch (\Exception $e) {
                $io->error('Analysis failed: ' . $e->getMessage());
                return Command::FAILURE;
            }
        } else {
            $io->note('Queuing analysis job...');
            
            // Queue the job
            $this->messageBus->dispatch(new AnalyzeAuditRunMessage($runId));
            
            $io->success('Analysis job queued successfully');
        }

        return Command::SUCCESS;
    }
}
