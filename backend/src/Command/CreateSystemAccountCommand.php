<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\SystemAccountService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:create-system-account',
    description: 'Create a new system account for backend operations'
)]
class CreateSystemAccountCommand extends Command
{
    public function __construct(
        private SystemAccountService $systemAccountService
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('username', 'u', InputOption::VALUE_REQUIRED, 'Username for the system account')
            ->addOption('display-name', 'd', InputOption::VALUE_REQUIRED, 'Display name for the system account')
            ->addOption('permissions', 'p', InputOption::VALUE_OPTIONAL, 'Comma-separated list of permissions', 'read,write,admin')
            ->setHelp('This command creates a new system account that can be used for backend operations.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $username = $input->getOption('username');
        $displayName = $input->getOption('display-name');
        $permissionsString = $input->getOption('permissions');

        if (!$username || !$displayName) {
            $io->error('Username and display name are required. Use --help for usage information.');
            return Command::FAILURE;
        }

        $permissions = array_map('trim', explode(',', $permissionsString));

        try {
            $systemUser = $this->systemAccountService->createSystemUser($username, $displayName, $permissions);

            $io->success(sprintf(
                'System account "%s" created successfully with ID: %s',
                $username,
                $systemUser->getId()
            ));

            $io->table(
                ['Property', 'Value'],
                [
                    ['Username', $systemUser->getUsername()],
                    ['Display Name', $systemUser->getDisplayName()],
                    ['Permissions', implode(', ', $systemUser->getPermissions())],
                    ['Active', $systemUser->isActive() ? 'Yes' : 'No'],
                    ['Created At', $systemUser->getCreatedAt()->format('Y-m-d H:i:s')],
                ]
            );

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $io->error(sprintf('Failed to create system account: %s', $e->getMessage()));
            return Command::FAILURE;
        }
    }
}
