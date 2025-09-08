<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\AuditRun;
use App\Entity\AuditRunState;
use App\Entity\Tenant;
use App\Entity\User;
use App\Message\CrawlAuditRunMessage;
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
    name: 'audit:crawl',
    description: 'Start crawling for an audit run'
)]
class CrawlAuditRunCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private MessageBusInterface $messageBus,
        private \App\Service\CrawlerService $crawlerService
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('url', InputArgument::REQUIRED, 'URL to crawl')
            ->addOption('tenant', 't', InputOption::VALUE_REQUIRED, 'Tenant ID')
            ->addOption('user', 'u', InputOption::VALUE_REQUIRED, 'User ID')
            ->addOption('max-pages', 'm', InputOption::VALUE_OPTIONAL, 'Maximum pages to crawl', 50)
            ->addOption('sync', 's', InputOption::VALUE_NONE, 'Run synchronously instead of queuing')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        
        $url = $input->getArgument('url');
        $tenantId = $input->getOption('tenant');
        $userId = $input->getOption('user');
        $maxPages = (int) $input->getOption('max-pages');
        $sync = $input->getOption('sync');

        // Validate URL
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            $io->error('Invalid URL provided');
            return Command::FAILURE;
        }

        // Get tenant
        $tenant = null;
        if ($tenantId) {
            $tenant = $this->entityManager->find(Tenant::class, $tenantId);
            if (!$tenant) {
                $io->error('Tenant not found');
                return Command::FAILURE;
            }
        } else {
            // Get first tenant for testing
            $tenant = $this->entityManager->getRepository(Tenant::class)->findOneBy([]);
            if (!$tenant) {
                $io->error('No tenant found. Please create a tenant first.');
                return Command::FAILURE;
            }
        }

        // Get user
        $user = null;
        if ($userId) {
            $user = $this->entityManager->find(User::class, $userId);
            if (!$user) {
                $io->error('User not found');
                return Command::FAILURE;
            }
        } else {
            // Get first user for testing
            $user = $this->entityManager->getRepository(User::class)->findOneBy(['tenant' => $tenant]);
            if (!$user) {
                $io->error('No user found for tenant. Please create a user first.');
                return Command::FAILURE;
            }
        }

        // Create audit
        $audit = new \App\Entity\Audit();
        $audit->setTenant($tenant);
        $audit->setLabel('Manual Crawl - ' . parse_url($url, PHP_URL_HOST));
        $this->entityManager->persist($audit);

        // Create audit run
        $auditRun = new AuditRun();
        $auditRun->setTenant($tenant);
        $auditRun->setAudit($audit);
        $auditRun->setState(AuditRunState::QUEUED);
        $auditRun->setRequestedBy($user);
        $auditRun->setSeedUrls([$url]);
        $auditRun->setConfig([
            'max_pages' => $maxPages,
            'store_html' => true,
            'take_screenshot' => false,
            'allowed_paths' => [],
            'blocked_paths' => []
        ]);
        
        $this->entityManager->persist($auditRun);
        $this->entityManager->flush();

        $io->success(sprintf('Created audit run with ID: %s', $auditRun->getId()));

        if ($sync) {
            $io->note('Running crawl synchronously...');
            
            // Run synchronously
            $this->crawlerService->crawlAuditRun($auditRun);
            
            $io->success('Crawl completed successfully');
        } else {
            $io->note('Queuing crawl job...');
            
            // Queue the job
            $this->messageBus->dispatch(new CrawlAuditRunMessage($auditRun->getId()));
            
            $io->success('Crawl job queued successfully');
        }

        return Command::SUCCESS;
    }
}
