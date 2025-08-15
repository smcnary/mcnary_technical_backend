<?php

namespace App\Command;

use App\Entity\Client;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Uid\Uuid;

#[AsCommand(
    name: 'app:create-client',
    description: 'Create a new client (law firm)',
)]
class CreateClientCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('name', InputArgument::REQUIRED, 'Client name (law firm name)')
            ->addOption('slug', 's', InputOption::VALUE_OPTIONAL, 'URL slug (auto-generated if not provided)')
            ->addOption('description', 'd', InputOption::VALUE_OPTIONAL, 'Client description')
            ->addOption('website', 'w', InputOption::VALUE_OPTIONAL, 'Client website URL')
            ->addOption('phone', 'p', InputOption::VALUE_OPTIONAL, 'Client phone number')
            ->addOption('address', 'a', InputOption::VALUE_OPTIONAL, 'Client address')
            ->addOption('city', 'c', InputOption::VALUE_OPTIONAL, 'Client city')
            ->addOption('state', 'st', InputOption::VALUE_OPTIONAL, 'Client state')
            ->addOption('zip-code', 'z', InputOption::VALUE_OPTIONAL, 'Client zip code')
            ->addOption('tenant-id', 't', InputOption::VALUE_OPTIONAL, 'Tenant ID')
            ->setHelp(<<<'HELP'
The <info>%command.name%</info> command creates a new client (law firm):

  <info>php %command.full_name%</info> "Smith & Associates Law Firm" --website="https://smithlaw.com" --city="New York" --state="NY"

Examples:
  # Create basic client
  php %command.full_name% "Johnson Legal Group"

  # Create client with full details
  php %command.full_name% "Wilson & Partners" --website="https://wilsonpartners.com" --phone="555-123-4567" --city="Los Angeles" --state="CA" --zip-code="90210"

  # Create client with custom slug
  php %command.full_name% "Brown Law Office" --slug="brown-law" --city="Chicago" --state="IL"
HELP
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        
        $name = $input->getArgument('name');
        $slug = $input->getOption('slug');
        $description = $input->getOption('description');
        $website = $input->getOption('website');
        $phone = $input->getOption('phone');
        $address = $input->getOption('address');
        $city = $input->getOption('city');
        $state = $input->getOption('state');
        $zipCode = $input->getOption('zip-code');
        $tenantId = $input->getOption('tenant-id');

        // Auto-generate slug if not provided
        if (!$slug) {
            $slug = $this->generateSlug($name);
        }

        // Check if client with same slug already exists
        $existingClient = $this->entityManager->getRepository(Client::class)->findOneBy(['slug' => $slug]);
        if ($existingClient) {
            $io->error(sprintf('Client with slug %s already exists', $slug));
            return Command::FAILURE;
        }

        // Create new client
        $client = new Client();
        $client->setName($name);
        $client->setSlug($slug);

        if ($description) {
            $client->setDescription($description);
        }

        if ($website) {
            $client->setWebsite($website);
        }

        if ($phone) {
            $client->setPhone($phone);
        }

        if ($address) {
            $client->setAddress($address);
        }

        if ($city) {
            $client->setCity($city);
        }

        if ($state) {
            $client->setState($state);
        }

        if ($zipCode) {
            $client->setZipCode($zipCode);
        }

        if ($tenantId) {
            $client->setTenantId($tenantId);
        }

        // Persist client
        $this->entityManager->persist($client);
        $this->entityManager->flush();

        $io->success(sprintf(
            'Client created successfully with ID: %s, Name: %s, Slug: %s',
            $client->getId(),
            $client->getName(),
            $client->getSlug()
        ));

        if ($website || $phone || $city) {
            $io->info('Client details:');
            if ($website) $io->info(sprintf('  Website: %s', $website));
            if ($phone) $io->info(sprintf('  Phone: %s', $phone));
            if ($city && $state) $io->info(sprintf('  Location: %s, %s', $city, $state));
        }

        return Command::SUCCESS;
    }

    private function generateSlug(string $name): string
    {
        // Convert to lowercase and replace spaces/special chars with hyphens
        $slug = strtolower(trim($name));
        $slug = preg_replace('/[^a-z0-9\s-]/', '', $slug);
        $slug = preg_replace('/[\s-]+/', '-', $slug);
        $slug = trim($slug, '-');
        
        return $slug;
    }
}
