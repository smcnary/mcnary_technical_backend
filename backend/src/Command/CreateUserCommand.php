<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Uid\Uuid;

#[AsCommand(
    name: 'app:create-user',
    description: 'Create a new user with role-based access control',
)]
class CreateUserCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('email', InputArgument::REQUIRED, 'User email')
            ->addArgument('password', InputArgument::REQUIRED, 'User password')
            ->addArgument('name', InputArgument::REQUIRED, 'User full name')
            ->addOption('role', 'r', InputOption::VALUE_REQUIRED, 'User role (AGENCY_ADMIN, AGENCY_STAFF, CLIENT_ADMIN, CLIENT_STAFF, SYSTEM_ADMIN, SALES_CONSULTANT)', 'CLIENT_STAFF')
            ->addOption('client-id', 'c', InputOption::VALUE_OPTIONAL, 'Client ID for client users')
            ->addOption('tenant-id', 't', InputOption::VALUE_OPTIONAL, 'Tenant ID')
            ->addOption('status', 's', InputOption::VALUE_OPTIONAL, 'User status (invited, active, inactive)', 'active')
            ->setHelp(<<<'HELP'
The <info>%command.name%</info> command creates a new user with role-based access control:

  <info>php %command.full_name%</info> user@example.com password123 "John Doe" --role=CLIENT_ADMIN --client-id=uuid-here

Available roles:
  - AGENCY_ADMIN: Full agency access to all clients
  - AGENCY_STAFF: Agency staff access to assigned clients
  - CLIENT_ADMIN: Client administrator access
  - CLIENT_STAFF: Client staff access
  - SYSTEM_ADMIN: System-level administrator
  - SALES_CONSULTANT: CRM access only, no core admin functionality

Examples:
  # Create agency admin
  php %command.full_name% admin@agency.com password123 "Agency Admin" --role=AGENCY_ADMIN

  # Create client admin
  php %command.full_name% admin@lawfirm.com password123 "Law Firm Admin" --role=CLIENT_ADMIN --client-id=uuid-here

  # Create client staff
  php %command.full_name% staff@lawfirm.com password123 "Law Firm Staff" --role=CLIENT_STAFF --client-id=uuid-here
HELP
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        
        $email = $input->getArgument('email');
        $password = $input->getArgument('password');
        $name = $input->getArgument('name');
        $role = $input->getOption('role');
        $clientId = $input->getOption('client-id');
        $tenantId = $input->getOption('tenant-id');
        $status = $input->getOption('status');

        // Validate role
        $validRoles = [
            User::ROLE_AGENCY_ADMIN,
            User::ROLE_AGENCY_STAFF,
            User::ROLE_CLIENT_ADMIN,
            User::ROLE_CLIENT_STAFF,
            User::ROLE_SYSTEM_ADMIN,
            User::ROLE_SALES_CONSULTANT
        ];

        if (!in_array($role, $validRoles)) {
            $io->error(sprintf('Invalid role. Valid roles are: %s', implode(', ', $validRoles)));
            return Command::FAILURE;
        }

        // Validate client ID for client users
        if (str_starts_with($role, 'CLIENT_') && !$clientId) {
            $io->error('Client ID is required for CLIENT_ADMIN and CLIENT_STAFF roles');
            return Command::FAILURE;
        }

        // Check if user already exists
        $existingUser = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
        if ($existingUser) {
            $io->error(sprintf('User with email %s already exists', $email));
            return Command::FAILURE;
        }

        // Get the default organization (assuming there's at least one)
        $organization = $this->entityManager->getRepository(\App\Entity\Organization::class)->findOneBy([]);
        if (!$organization) {
            $io->error('No organization found. Please create an organization first.');
            return Command::FAILURE;
        }

        // Hash password
        $hashedPassword = $this->passwordHasher->hashPassword(new User($organization, 'temp@temp.com', 'temp', 'CLIENT_STAFF'), $password);

        // Create new user with constructor
        $user = new User($organization, $email, $hashedPassword, $role);
        
        // Parse name into first and last name
        $nameParts = explode(' ', trim($name), 2);
        $user->setFirstName($nameParts[0]);
        $user->setLastName(isset($nameParts[1]) ? $nameParts[1] : null);
        
        $user->setStatus($status);

        if ($tenantId) {
            // Find the tenant entity
            $tenant = $this->entityManager->getRepository(\App\Entity\Tenant::class)->find($tenantId);
            if ($tenant) {
                $user->setTenant($tenant);
            }
        }

        if ($clientId) {
            $user->setClientId($clientId);
        }

        // Explicitly set metadata to null to avoid array to string conversion
        $user->setMetadata(null);

        // Persist user
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $io->success(sprintf(
            'User created successfully with ID: %s, Email: %s, Role: %s',
            $user->getId(),
            $user->getEmail(),
            $role
        ));

        if ($clientId) {
            $io->info(sprintf('User assigned to client: %s', $clientId));
        }

        return Command::SUCCESS;
    }
}
