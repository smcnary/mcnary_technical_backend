<?php

namespace App\Command;

use App\Service\GoogleBusinessProfileSyncService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:sync-gbp-data',
    description: 'Sync Google Business Profile data for all connected clients',
)]
class SyncGoogleBusinessProfileDataCommand extends Command
{
    public function __construct(
        private GoogleBusinessProfileSyncService $gbpSyncService
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('client-id', null, InputOption::VALUE_OPTIONAL, 'Sync data for a specific client ID only')
            ->addOption('force', null, InputOption::VALUE_NONE, 'Force sync even if not needed')
            ->setHelp('This command synchronizes Google Business Profile data for connected clients.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        
        $clientId = $input->getOption('client-id');
        $force = $input->getOption('force');

        $io->title('Google Business Profile Data Sync');

        try {
            if ($clientId) {
                // Sync specific client
                $io->section("Syncing data for client: {$clientId}");
                $result = $this->syncSpecificClient($clientId, $force, $io);
                return $result ? Command::SUCCESS : Command::FAILURE;
            } else {
                // Sync all clients
                $io->section('Syncing data for all connected clients');
                $results = $this->gbpSyncService->syncAllClients();
                
                $this->displayResults($results, $io);
                return Command::SUCCESS;
            }
        } catch (\Exception $e) {
            $io->error("Sync failed: {$e->getMessage()}");
            return Command::FAILURE;
        }
    }

    private function syncSpecificClient(string $clientId, bool $force, SymfonyStyle $io): bool
    {
        try {
            // Find client
            $client = $this->gbpSyncService->getClientById($clientId);
            if (!$client) {
                $io->error("Client with ID {$clientId} not found");
                return false;
            }

            // Check if sync is needed
            if (!$force && !$this->gbpSyncService->needsSync($client)) {
                $io->info("Client {$clientId} does not need sync at this time");
                return true;
            }

            // Perform sync
            $io->text("Syncing data for client: {$client->getName()}");
            $result = $this->gbpSyncService->syncClientData($client);
            
            if ($result['success']) {
                $io->success([
                    "Successfully synced data for client: {$client->getName()}",
                    "Data points: {$result['data_points']}",
                    "Last sync: {$result['last_sync']}"
                ]);
                return true;
            } else {
                $io->error("Failed to sync client {$clientId}: {$result['error']}");
                return false;
            }
        } catch (\Exception $e) {
            $io->error("Failed to sync client {$clientId}: {$e->getMessage()}");
            return false;
        }
    }

    private function displayResults(array $results, SymfonyStyle $io): void
    {
        $successCount = 0;
        $failureCount = 0;

        foreach ($results as $clientId => $result) {
            if ($result['success']) {
                $successCount++;
                $io->text("✓ Client {$clientId}: {$result['data_points']} data points synced");
            } else {
                $failureCount++;
                $io->text("✗ Client {$clientId}: {$result['error']}");
            }
        }

        $io->newLine();
        
        if ($successCount > 0) {
            $io->success("Successfully synced {$successCount} client(s)");
        }
        
        if ($failureCount > 0) {
            $io->warning("Failed to sync {$failureCount} client(s)");
        }

        if ($successCount === 0 && $failureCount === 0) {
            $io->info('No clients with Google Business Profile connections found');
        }
    }
}
