<?php

namespace App\Command;

use App\Entity\Package;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;


#[AsCommand(
    name: 'app:create-package',
    description: 'Create a new pricing package',
)]
class CreatePackageCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('name', InputArgument::REQUIRED, 'Package name')
            ->addOption('slug', 's', InputOption::VALUE_OPTIONAL, 'URL slug (auto-generated if not provided)')
            ->addOption('description', 'd', InputOption::VALUE_OPTIONAL, 'Package description')
            ->addOption('price', 'p', InputOption::VALUE_OPTIONAL, 'Package price')
            ->addOption('billing-cycle', 'b', InputOption::VALUE_OPTIONAL, 'Billing cycle (monthly, quarterly, annually)')
            ->addOption('features', 'f', InputOption::VALUE_OPTIONAL, 'Comma-separated list of features')
            ->addOption('services', 'sv', InputOption::VALUE_OPTIONAL, 'Comma-separated list of included services')
            ->addOption('popular', null, InputOption::VALUE_NONE, 'Mark as popular package')
            ->addOption('sort-order', 'o', InputOption::VALUE_OPTIONAL, 'Sort order (default: 0)', '0')
            ->addOption('client-id', 'c', InputOption::VALUE_OPTIONAL, 'Client ID (optional)')
            ->addOption('tenant-id', 't', InputOption::VALUE_OPTIONAL, 'Tenant ID (optional)')
            ->setHelp(<<<'HELP'
The <info>%command.name%</info> command creates a new pricing package:

  <info>php %command.full_name%</info> "Starter Plan" --price="99" --billing-cycle="monthly" --features="SEO,Content,Analytics" --popular

Examples:
  # Create basic package
  php %command.full_name% "Basic SEO Package" --price="199" --billing-cycle="monthly"

  # Create popular package with features
  php %command.full_name% "Premium Package" --price="499" --billing-cycle="monthly" --features="SEO,Content,PPC,Analytics" --popular

  # Create annual package
  php %command.full_name% "Annual Plan" --price="4999" --billing-cycle="annually" --features="Full Service,Priority Support"
HELP
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        
        $name = $input->getArgument('name');
        $slug = $input->getOption('slug');
        $description = $input->getOption('description');
        $price = $input->getOption('price');
        $billingCycle = $input->getOption('billing-cycle');
        $features = $input->getOption('features');
        $services = $input->getOption('services');
        $isPopular = $input->getOption('popular');
        $sortOrder = (int) $input->getOption('sort-order');
        $clientId = $input->getOption('client-id');
        $tenantId = $input->getOption('tenant-id');

        // Validate required fields
        if (!$description) {
            $io->error('Description is required. Use --description option.');
            return Command::FAILURE;
        }

        if (!$price) {
            $io->error('Price is required. Use --price option.');
            return Command::FAILURE;
        }

        if (!$billingCycle) {
            $io->error('Billing cycle is required. Use --billing-cycle option.');
            return Command::FAILURE;
        }

        if (!$features) {
            $io->error('Features are required. Use --features option.');
            return Command::FAILURE;
        }

        if (!$services) {
            $io->error('Services are required. Use --services option.');
            return Command::FAILURE;
        }

        // Auto-generate slug if not provided
        if (!$slug) {
            $slug = $this->generateSlug($name);
        }

        // Check if package with same slug already exists
        $existingPackage = $this->entityManager->getRepository(Package::class)->findOneBy(['slug' => $slug]);
        if ($existingPackage) {
            $io->error(sprintf('Package with slug %s already exists', $slug));
            return Command::FAILURE;
        }

        // Parse features and services
        $featuresArray = array_map('trim', explode(',', $features));
        $servicesArray = array_map('trim', explode(',', $services));

        // Create new package
        $package = new Package();
        $package->setName($name);
        $package->setSlug($slug);
        $package->setDescription($description);
        $package->setPrice((float) $price);
        $package->setBillingCycle($billingCycle);
        $package->setFeatures($featuresArray);
        $package->setIncludedServices($servicesArray);
        $package->setIsPopular($isPopular);
        $package->setSortOrder($sortOrder);

        if ($clientId) {
            $package->setClientId($clientId);
        }

        if ($tenantId) {
            $package->setTenantId($tenantId);
        }

        // Persist package
        $this->entityManager->persist($package);
        $this->entityManager->flush();

        $io->success(sprintf(
            'Package created successfully with ID: %s, Name: %s, Slug: %s',
            $package->getId(),
            $package->getName(),
            $package->getSlug()
        ));

        $io->info('Package details:');
        $io->info(sprintf('  Price: $%s/%s', $price, $billingCycle));
        $io->info(sprintf('  Features: %s', implode(', ', $featuresArray)));
        $io->info(sprintf('  Services: %s', implode(', ', $servicesArray)));
        
        if ($isPopular) {
            $io->info('  Status: Popular package');
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
