<?php

namespace App\Command;

use App\Entity\Agency;
use App\Entity\Campaign;
use App\Entity\CaseStudy;
use App\Entity\Client;
use App\Entity\Lead;
use App\Entity\LeadSource;
use App\Entity\Organization;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Uid\Uuid;

#[AsCommand(
    name: 'app:generate-sample-data',
    description: 'Generate sample data for the client dashboard (leads, campaigns, case studies)',
)]
class GenerateSampleDataCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('client-id', 'c', InputOption::VALUE_OPTIONAL, 'Client ID to generate data for (if not provided, will use first available client)')
            ->addOption('leads-count', 'l', InputOption::VALUE_OPTIONAL, 'Number of leads to generate', 15)
            ->addOption('campaigns-count', 'm', InputOption::VALUE_OPTIONAL, 'Number of campaigns to generate', 8)
            ->addOption('case-studies-count', 's', InputOption::VALUE_OPTIONAL, 'Number of case studies to generate', 6)
            ->setHelp(<<<'HELP'
The <info>%command.name%</info> command generates sample data for testing the client dashboard:

  <info>php %command.full_name%</info> --client-id=uuid-here --leads-count=20 --campaigns-count=10

This will create realistic sample data including:
  - Leads with various statuses and practice areas
  - Campaigns with different types and statuses
  - Case studies with different practice areas

Examples:
  # Generate default amounts for first available client
  php %command.full_name%

  # Generate specific amounts for a client
  php %command.full_name% --client-id=uuid-here --leads-count=25 --campaigns-count=12
HELP
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        
        $clientId = $input->getOption('client-id');
        $leadsCount = (int) $input->getOption('leads-count');
        $campaignsCount = (int) $input->getOption('campaigns-count');
        $caseStudiesCount = (int) $input->getOption('case-studies-count');

        // Find or create client
        $client = $this->findOrCreateClient($clientId, $io);
        if (!$client) {
            return Command::FAILURE;
        }

        $io->info(sprintf('Generating sample data for client: %s (%s)', $client->getName(), $client->getId()));

        // Generate leads
        $this->generateLeads($client, $leadsCount, $io);
        
        // Generate campaigns
        $this->generateCampaigns($client, $campaignsCount, $io);
        
        // Generate case studies
        $this->generateCaseStudies($caseStudiesCount, $io);

        $io->success('Sample data generated successfully!');
        $io->table(
            ['Entity', 'Count'],
            [
                ['Leads', $leadsCount],
                ['Campaigns', $campaignsCount],
                ['Case Studies', $caseStudiesCount],
            ]
        );

        return Command::SUCCESS;
    }

    private function findOrCreateClient(?string $clientId, SymfonyStyle $io): ?Client
    {
        if ($clientId) {
            $client = $this->entityManager->getRepository(Client::class)->find($clientId);
            if (!$client) {
                $io->error(sprintf('Client with ID %s not found', $clientId));
                return null;
            }
            return $client;
        }

        // Find first available client
        $client = $this->entityManager->getRepository(Client::class)->findOneBy([]);
        if (!$client) {
            $io->error('No clients found. Please create a client first.');
            return null;
        }

        return $client;
    }

    private function generateLeads(Client $client, int $count, SymfonyStyle $io): void
    {
        $practiceAreas = [
            'Personal Injury',
            'Criminal Defense',
            'Family Law',
            'Business Law',
            'Real Estate',
            'Estate Planning',
            'Employment Law',
            'Immigration Law',
            'Bankruptcy',
            'Medical Malpractice'
        ];

        $statuses = ['new', 'contacted', 'qualified', 'proposal', 'closed_won', 'closed_lost'];
        $cities = ['Tulsa', 'Oklahoma City', 'Broken Arrow', 'Norman', 'Edmond', 'Lawton', 'Moore', 'Midwest City'];
        $states = ['OK', 'TX', 'AR', 'KS', 'MO'];
        $firms = [
            'Smith & Associates',
            'Johnson Law Group',
            'Brown Legal Services',
            'Davis & Partners',
            'Wilson Law Firm',
            'Miller Legal Group',
            'Taylor & Associates',
            'Anderson Law Office'
        ];

        $firstNames = ['John', 'Jane', 'Michael', 'Sarah', 'David', 'Lisa', 'Robert', 'Jennifer', 'William', 'Ashley', 'James', 'Jessica', 'Christopher', 'Amanda', 'Daniel', 'Stephanie'];
        $lastNames = ['Smith', 'Johnson', 'Williams', 'Brown', 'Jones', 'Garcia', 'Miller', 'Davis', 'Rodriguez', 'Martinez', 'Hernandez', 'Lopez', 'Gonzalez', 'Wilson', 'Anderson', 'Thomas'];

        for ($i = 0; $i < $count; $i++) {
            $lead = new Lead();
            $lead->setClient($client);
            
            $firstName = $firstNames[array_rand($firstNames)];
            $lastName = $lastNames[array_rand($lastNames)];
            $lead->setFullName($firstName . ' ' . $lastName);
            $lead->setEmail(strtolower($firstName . '.' . $lastName . '@example.com'));
            $lead->setPhone('555-' . rand(100, 999) . '-' . rand(1000, 9999));
            $lead->setFirm($firms[array_rand($firms)]);
            $lead->setWebsite('https://www.' . strtolower(str_replace([' ', '&'], ['', ''], $lead->getFirm())) . '.com');
            
            $practiceAreaCount = rand(1, 3);
            $selectedAreas = array_rand($practiceAreas, $practiceAreaCount);
            if ($practiceAreaCount === 1) {
                $selectedAreas = [$selectedAreas];
            }
            $lead->setPracticeAreas(array_map(fn($index) => $practiceAreas[$index], $selectedAreas));
            
            $lead->setCity($cities[array_rand($cities)]);
            $lead->setState($states[array_rand($states)]);
            $lead->setStatus($statuses[array_rand($statuses)]);
            $lead->setMessage('Sample lead generated for testing purposes. This is a ' . $lead->getPracticeAreas()[0] . ' case.');
            
            // Note: Timestamps are handled automatically by the Timestamps trait

            $this->entityManager->persist($lead);
        }

        $this->entityManager->flush();
        $io->info(sprintf('Generated %d leads', $count));
    }

    private function generateCampaigns(Client $client, int $count, SymfonyStyle $io): void
    {
        $campaignTypes = ['SEO', 'PPC', 'Social Media', 'Content Marketing', 'Email Marketing', 'Local SEO'];
        $statuses = ['active', 'paused', 'completed', 'draft'];
        $campaignNames = [
            'Tulsa Personal Injury SEO',
            'Oklahoma City Criminal Defense PPC',
            'Family Law Social Media',
            'Business Law Content Strategy',
            'Real Estate Local SEO',
            'Estate Planning Email Campaign',
            'Employment Law PPC',
            'Immigration Law SEO',
            'Bankruptcy Content Marketing',
            'Medical Malpractice Local SEO'
        ];

        for ($i = 0; $i < $count; $i++) {
            $campaignName = $campaignNames[array_rand($campaignNames)] . ' ' . ($i + 1);
            $campaignType = strtolower($campaignTypes[array_rand($campaignTypes)]);
            
            $campaign = new Campaign($client, $campaignName, $campaignType);
            $campaign->setStatus($statuses[array_rand($statuses)]);
            $campaign->setBudget((string) rand(1000, 10000));
            
            // Set start date
            $startDate = new \DateTimeImmutable();
            $startDate = $startDate->modify('-' . rand(30, 180) . ' days');
            $campaign->setStartDate($startDate);
            
            // Set end date (some campaigns ongoing)
            if ($campaign->getStatus() === 'completed') {
                $endDate = $startDate->modify('+' . rand(30, 90) . ' days');
                $campaign->setEndDate($endDate);
            }
            
            $campaign->setDescription('Sample campaign for ' . $campaign->getType() . ' marketing. This campaign targets ' . $campaign->getType() . ' keywords and audiences.');
            
            // Note: Timestamps are handled automatically by the Timestamps trait

            $this->entityManager->persist($campaign);
        }

        $this->entityManager->flush();
        $io->info(sprintf('Generated %d campaigns', $count));
    }

    private function generateCaseStudies(int $count, SymfonyStyle $io): void
    {
        // Find or create a tenant
        $tenant = $this->entityManager->getRepository(\App\Entity\Tenant::class)->findOneBy([]);
        if (!$tenant) {
            $io->warning('No tenant found. Skipping case studies generation.');
            return;
        }

        $titles = [
            'Personal Injury Law Firm Increases Leads by 300%',
            'Criminal Defense Attorney Dominates Local Search',
            'Family Law Practice Grows Revenue 150%',
            'Business Law Firm Expands Client Base',
            'Real Estate Attorney Boosts Online Presence',
            'Estate Planning Lawyer Attracts High-Value Clients',
            'Employment Law Firm Increases Case Intake',
            'Immigration Attorney Reaches New Communities',
            'Bankruptcy Lawyer Helps More Families',
            'Medical Malpractice Firm Builds Trust Online'
        ];

        $practiceAreas = [
            'Personal Injury',
            'Criminal Defense',
            'Family Law',
            'Business Law',
            'Real Estate',
            'Estate Planning',
            'Employment Law',
            'Immigration Law',
            'Bankruptcy',
            'Medical Malpractice'
        ];

        $summaries = [
            'This case study demonstrates how strategic SEO and content marketing helped a law firm dramatically increase their client base and revenue.',
            'Learn how this attorney used local SEO and Google My Business optimization to dominate their market.',
            'Discover the marketing strategies that helped this law firm grow their practice and serve more clients.',
            'See how targeted PPC campaigns and content marketing transformed this law firm\'s online presence.',
            'This case study shows the power of local SEO and reputation management for legal professionals.'
        ];

        for ($i = 0; $i < $count; $i++) {
            $title = $titles[array_rand($titles)];
            $slug = strtolower(str_replace([' ', '%', '&'], ['-', '', ''], $title)) . '-' . ($i + 1);
            
            $caseStudy = new CaseStudy($tenant);
            $caseStudy->setTitle($title . ' ' . ($i + 1));
            $caseStudy->setPracticeArea($practiceAreas[array_rand($practiceAreas)]);
            $caseStudy->setSummary($summaries[array_rand($summaries)]);
            $caseStudy->setSlug($slug);
            $caseStudy->setIsActive(true);
            $caseStudy->setSort($i + 1);
            
            // Set metrics JSON
            $caseStudy->setMetricsJson([
                'leads_increase' => rand(150, 400) . '%',
                'traffic_growth' => rand(100, 300) . '%',
                'phone_calls' => rand(150, 250) . '%',
                'revenue_growth' => rand(120, 200) . '%'
            ]);
            
            // Set random creation date
            $createdAt = new \DateTimeImmutable();
            $createdAt = $createdAt->modify('-' . rand(0, 365) . ' days');
            $caseStudy->setCreatedAt($createdAt);
            $caseStudy->setUpdatedAt($createdAt);

            $this->entityManager->persist($caseStudy);
        }

        $this->entityManager->flush();
        $io->info(sprintf('Generated %d case studies', $count));
    }
}
