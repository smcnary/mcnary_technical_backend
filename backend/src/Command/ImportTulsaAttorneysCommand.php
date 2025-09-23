<?php

namespace App\Command;

use App\Entity\Lead;
use App\Entity\LeadSource;
use App\ValueObject\LeadStatus;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:import-tulsa-attorneys',
    description: 'Import mock Tulsa attorneys data for testing',
)]
class ImportTulsaAttorneysCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        // Get or create leadgen source
        $source = $this->entityManager->getRepository(LeadSource::class)->findOneBy(['name' => 'Leadgen: Tulsa Attorneys Mock']);
        if (!$source) {
            $source = new LeadSource('Leadgen: Tulsa Attorneys Mock');
            $source->setDescription('Automated lead generation campaign for Tulsa attorneys');
            $source->setStatus('active');
            $this->entityManager->persist($source);
        }

        // Mock data for Tulsa attorneys
        $attorneys = [
            [
                'full_name' => 'Johnson & Associates Law Firm',
                'email' => 'contact@johnsonlawtulsa.com',
                'phone' => '+1-918-555-0101',
                'firm' => 'Johnson & Associates Law Firm',
                'website' => 'https://johnsonlawtulsa.com',
                'city' => 'Tulsa',
                'state' => 'OK',
                'zip_code' => '74101',
                'message' => 'Generated from leadgen campaign: Tulsa Attorneys - Vertical: local_services - Lead Score: 87',
                'practice_areas' => ['attorney', 'lawyer', 'legal services'],
                'utm_json' => [
                    'leadgen_campaign' => 'Tulsa Attorneys Mock',
                    'lead_score' => 87,
                    'vertical' => 'local_services',
                    'rating' => 4.5,
                    'review_count' => 127,
                    'generated_at' => (new \DateTimeImmutable())->format('c')
                ]
            ],
            [
                'full_name' => 'Smith Legal Group',
                'email' => 'info@smithlegalok.com',
                'phone' => '+1-918-555-0102',
                'firm' => 'Smith Legal Group',
                'website' => 'https://smithlegalok.com',
                'city' => 'Tulsa',
                'state' => 'OK',
                'zip_code' => '74102',
                'message' => 'Generated from leadgen campaign: Tulsa Attorneys - Vertical: local_services - Lead Score: 82',
                'practice_areas' => ['attorney', 'lawyer', 'legal services'],
                'utm_json' => [
                    'leadgen_campaign' => 'Tulsa Attorneys Mock',
                    'lead_score' => 82,
                    'vertical' => 'local_services',
                    'rating' => 4.3,
                    'review_count' => 89,
                    'generated_at' => (new \DateTimeImmutable())->format('c')
                ]
            ],
            [
                'full_name' => 'Williams & Partners',
                'email' => 'hello@williamspartners.com',
                'phone' => '+1-918-555-0103',
                'firm' => 'Williams & Partners',
                'website' => 'https://williamspartners.com',
                'city' => 'Tulsa',
                'state' => 'OK',
                'zip_code' => '74103',
                'message' => 'Generated from leadgen campaign: Tulsa Attorneys - Vertical: local_services - Lead Score: 91',
                'practice_areas' => ['attorney', 'lawyer', 'legal services'],
                'utm_json' => [
                    'leadgen_campaign' => 'Tulsa Attorneys Mock',
                    'lead_score' => 91,
                    'vertical' => 'local_services',
                    'rating' => 4.7,
                    'review_count' => 156,
                    'generated_at' => (new \DateTimeImmutable())->format('c')
                ]
            ],
            [
                'full_name' => 'Brown Law Office',
                'email' => 'info@brownlawtulsa.com',
                'phone' => '+1-918-555-0104',
                'firm' => 'Brown Law Office',
                'website' => 'https://brownlawtulsa.com',
                'city' => 'Tulsa',
                'state' => 'OK',
                'zip_code' => '74104',
                'message' => 'Generated from leadgen campaign: Tulsa Attorneys - Vertical: local_services - Lead Score: 75',
                'practice_areas' => ['attorney', 'lawyer', 'legal services'],
                'utm_json' => [
                    'leadgen_campaign' => 'Tulsa Attorneys Mock',
                    'lead_score' => 75,
                    'vertical' => 'local_services',
                    'rating' => 4.1,
                    'review_count' => 67,
                    'generated_at' => (new \DateTimeImmutable())->format('c')
                ]
            ],
            [
                'full_name' => 'Davis Legal Solutions',
                'email' => 'contact@davislegalok.com',
                'phone' => '+1-918-555-0105',
                'firm' => 'Davis Legal Solutions',
                'website' => 'https://davislegalok.com',
                'city' => 'Tulsa',
                'state' => 'OK',
                'zip_code' => '74105',
                'message' => 'Generated from leadgen campaign: Tulsa Attorneys - Vertical: local_services - Lead Score: 84',
                'practice_areas' => ['attorney', 'lawyer', 'legal services'],
                'utm_json' => [
                    'leadgen_campaign' => 'Tulsa Attorneys Mock',
                    'lead_score' => 84,
                    'vertical' => 'local_services',
                    'rating' => 4.4,
                    'review_count' => 98,
                    'generated_at' => (new \DateTimeImmutable())->format('c')
                ]
            ]
        ];

        $imported = 0;
        $updated = 0;

        foreach ($attorneys as $attorneyData) {
            // Check if lead already exists
            $existingLead = $this->entityManager->getRepository(Lead::class)->findOneBy(['email' => $attorneyData['email']]);
            
            if ($existingLead) {
                $lead = $existingLead;
                $updated++;
            } else {
                $lead = new Lead();
                $imported++;
            }

            // Set lead data
            $lead->setFullName($attorneyData['full_name']);
            $lead->setEmail($attorneyData['email']);
            $lead->setPhone($attorneyData['phone']);
            $lead->setFirm($attorneyData['firm']);
            $lead->setWebsite($attorneyData['website']);
            $lead->setCity($attorneyData['city']);
            $lead->setState($attorneyData['state']);
            $lead->setZipCode($attorneyData['zip_code']);
            $lead->setMessage($attorneyData['message']);
            $lead->setPracticeAreas($attorneyData['practice_areas']);
            $lead->setStatus(LeadStatus::NEW_LEAD);
            $lead->setSource($source);
            $lead->setUtmJson($attorneyData['utm_json']);

            if (!$existingLead) {
                $this->entityManager->persist($lead);
            }
        }

        $this->entityManager->flush();

        $io->success(sprintf('Successfully imported %d leads and updated %d leads', $imported, $updated));

        return Command::SUCCESS;
    }
}
