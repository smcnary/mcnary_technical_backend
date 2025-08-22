<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\AuditRun;
use App\Entity\AuditRunState;
use App\Message\AggregateAuditMessage;
use App\Message\CrawlPageMessage;
use App\Repository\AuditRunRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsCommand(
    name: 'audit:run',
    description: 'Run an SEO audit',
)]
class AuditRunCommand extends Command
{
    public function __construct(
        private readonly AuditRunRepository $auditRunRepository,
        private readonly MessageBusInterface $messageBus
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('auditId', InputArgument::REQUIRED, 'The audit ID to run')
            ->addOption('max-pages', 'm', InputOption::VALUE_OPTIONAL, 'Maximum pages to crawl', 1000)
            ->addOption('force', 'f', InputOption::VALUE_NONE, 'Force run even if already running')
            ->addOption('sample-lighthouse', 'l', InputOption::VALUE_OPTIONAL, 'Number of pages to run Lighthouse on', 30);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $auditId = $input->getArgument('auditId');
        $maxPages = (int) $input->getOption('max-pages');
        $force = $input->getOption('force');
        $lighthouseSample = (int) $input->getOption('sample-lighthouse');

        $io->title('Running SEO Audit');
        $io->text("Audit ID: {$auditId}");
        $io->text("Max Pages: {$maxPages}");
        $io->text("Lighthouse Sample: {$lighthouseSample}");

        // Find the audit run
        $auditRun = $this->auditRunRepository->find($auditId);
        if (!$auditRun) {
            $io->error("Audit run not found: {$auditId}");
            return Command::FAILURE;
        }

        if (!$force && $auditRun->isRunning()) {
            $io->error('Audit is already running. Use --force to override.');
            return Command::FAILURE;
        }

        try {
            // Update state to QUEUED
            $auditRun->setState(AuditRunState::QUEUED);
            $this->auditRunRepository->save($auditRun, true);

            $io->success('Audit queued successfully!');
            $io->text('Use "audit:status {$auditId}" to monitor progress.');

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $io->error('Failed to queue audit: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
