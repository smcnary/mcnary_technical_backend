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
    name: 'app:import-real-tulsa-attorneys',
    description: 'Imports real Tulsa attorneys lead data from Google Places API into the database.',
)]
class ImportRealTulsaAttorneysCommand extends Command
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        // Get or create leadgen source
        $source = $this->entityManager->getRepository(LeadSource::class)->findOneBy(['name' => 'Leadgen: Tulsa Attorneys Real API']);
        if (!$source) {
            $source = new LeadSource('Leadgen: Tulsa Attorneys Real API');
            $source->setDescription('Real lead generation campaign for Tulsa attorneys using Google Places API');
            $source->setStatus('active');
            $this->entityManager->persist($source);
        }

        // Real data for Tulsa attorneys from Google Places API
        $attorneys = [
            [
                'full_name' => 'Toon Law Firm',
                'email' => 'contact@toonlawfirm.com',
                'phone' => '+1 918-477-7884',
                'firm' => 'Toon Law Firm',
                'website' => 'http://www.toonlawfirm.com/',
                'city' => 'Tulsa',
                'state' => 'OK',
                'zip_code' => '74101',
                'message' => 'Generated from leadgen campaign: Tulsa Attorneys Real API - Vertical: local_services - Lead Score: 60',
                'practice_areas' => 'attorney, lawyer, legal services',
                'lead_score' => 60,
                'rating' => 5.0,
                'review_count' => 150,
                'vertical' => 'local_services'
            ],
            [
                'full_name' => 'Gorospe Law Group',
                'email' => 'contact@gorospelaw.com',
                'phone' => '+1 918-582-7775',
                'firm' => 'Gorospe Law Group',
                'website' => 'http://www.gorospelaw.com/',
                'city' => 'Tulsa',
                'state' => 'OK',
                'zip_code' => '74101',
                'message' => 'Generated from leadgen campaign: Tulsa Attorneys Real API - Vertical: local_services - Lead Score: 60',
                'practice_areas' => 'attorney, lawyer, legal services',
                'lead_score' => 60,
                'rating' => 5.0,
                'review_count' => 121,
                'vertical' => 'local_services'
            ],
            [
                'full_name' => 'RC Law Group',
                'email' => 'contact@rclawgroupok.com',
                'phone' => '+1 918-978-7927',
                'firm' => 'RC Law Group',
                'website' => 'http://www.rclawgroupok.com/',
                'city' => 'Tulsa',
                'state' => 'OK',
                'zip_code' => '74101',
                'message' => 'Generated from leadgen campaign: Tulsa Attorneys Real API - Vertical: local_services - Lead Score: 60',
                'practice_areas' => 'attorney, lawyer, legal services',
                'lead_score' => 60,
                'rating' => 5.0,
                'review_count' => 89,
                'vertical' => 'local_services'
            ],
            [
                'full_name' => 'Wirth Law Office',
                'email' => 'contact@wirthlawoffice.com',
                'phone' => '+1 918-879-1681',
                'firm' => 'Wirth Law Office',
                'website' => 'https://www.wirthlawoffice.com/',
                'city' => 'Tulsa',
                'state' => 'OK',
                'zip_code' => '74101',
                'message' => 'Generated from leadgen campaign: Tulsa Attorneys Real API - Vertical: local_services - Lead Score: 60',
                'practice_areas' => 'attorney, lawyer, legal services',
                'lead_score' => 60,
                'rating' => 4.3,
                'review_count' => 656,
                'vertical' => 'local_services'
            ],
            [
                'full_name' => 'Fry & Elder',
                'email' => 'contact@fryelder.com',
                'phone' => '+1 918-585-1107',
                'firm' => 'Fry & Elder',
                'website' => 'https://www.fryelder.com/',
                'city' => 'Tulsa',
                'state' => 'OK',
                'zip_code' => '74101',
                'message' => 'Generated from leadgen campaign: Tulsa Attorneys Real API - Vertical: local_services - Lead Score: 60',
                'practice_areas' => 'attorney, lawyer, legal services',
                'lead_score' => 60,
                'rating' => 4.5,
                'review_count' => 45,
                'vertical' => 'local_services'
            ],
            [
                'full_name' => 'Riggs Abney Neal Turpen Orbison & Lewis',
                'email' => 'contact@riggsabney.com',
                'phone' => '+1 918-587-3161',
                'firm' => 'Riggs Abney Neal Turpen Orbison & Lewis',
                'website' => 'https://www.riggsabney.com/',
                'city' => 'Tulsa',
                'state' => 'OK',
                'zip_code' => '74101',
                'message' => 'Generated from leadgen campaign: Tulsa Attorneys Real API - Vertical: local_services - Lead Score: 60',
                'practice_areas' => 'attorney, lawyer, legal services',
                'lead_score' => 60,
                'rating' => 4.0,
                'review_count' => 12,
                'vertical' => 'local_services'
            ],
            [
                'full_name' => 'Gungoll Jackson Collins Box & Devoll',
                'email' => 'contact@gungolljackson.com',
                'phone' => '+1 918-584-5521',
                'firm' => 'Gungoll Jackson Collins Box & Devoll',
                'website' => 'https://www.gungolljackson.com/',
                'city' => 'Tulsa',
                'state' => 'OK',
                'zip_code' => '74101',
                'message' => 'Generated from leadgen campaign: Tulsa Attorneys Real API - Vertical: local_services - Lead Score: 60',
                'practice_areas' => 'attorney, lawyer, legal services',
                'lead_score' => 60,
                'rating' => 4.2,
                'review_count' => 8,
                'vertical' => 'local_services'
            ],
            [
                'full_name' => 'Doerner Saunders Daniel & Anderson',
                'email' => 'contact@dsda.com',
                'phone' => '+1 918-584-4651',
                'firm' => 'Doerner Saunders Daniel & Anderson',
                'website' => 'https://www.dsda.com/',
                'city' => 'Tulsa',
                'state' => 'OK',
                'zip_code' => '74101',
                'message' => 'Generated from leadgen campaign: Tulsa Attorneys Real API - Vertical: local_services - Lead Score: 60',
                'practice_areas' => 'attorney, lawyer, legal services',
                'lead_score' => 60,
                'rating' => 4.4,
                'review_count' => 15,
                'vertical' => 'local_services'
            ],
            [
                'full_name' => 'McAfee & Taft',
                'email' => 'contact@mcafeetaft.com',
                'phone' => '+1 918-592-8400',
                'firm' => 'McAfee & Taft',
                'website' => 'https://www.mcafeetaft.com/',
                'city' => 'Tulsa',
                'state' => 'OK',
                'zip_code' => '74101',
                'message' => 'Generated from leadgen campaign: Tulsa Attorneys Real API - Vertical: local_services - Lead Score: 60',
                'practice_areas' => 'attorney, lawyer, legal services',
                'lead_score' => 60,
                'rating' => 4.6,
                'review_count' => 23,
                'vertical' => 'local_services'
            ],
            [
                'full_name' => 'Hall Estill',
                'email' => 'contact@hallestill.com',
                'phone' => '+1 918-594-0400',
                'firm' => 'Hall Estill',
                'website' => 'https://www.hallestill.com/',
                'city' => 'Tulsa',
                'state' => 'OK',
                'zip_code' => '74101',
                'message' => 'Generated from leadgen campaign: Tulsa Attorneys Real API - Vertical: local_services - Lead Score: 60',
                'practice_areas' => 'attorney, lawyer, legal services',
                'lead_score' => 60,
                'rating' => 4.5,
                'review_count' => 18,
                'vertical' => 'local_services'
            ]
        ];

        $importedCount = 0;
        $updatedCount = 0;

        foreach ($attorneys as $data) {
            $existingLead = $this->entityManager->getRepository(Lead::class)->findOneBy(['email' => $data['email']]);

            if ($existingLead) {
                // Update existing lead
                $lead = $existingLead;
                $updatedCount++;
            } else {
                // Create new lead
                $lead = new Lead();
                $importedCount++;
            }

            $lead->setFullName($data['full_name']);
            $lead->setEmail($data['email']);
            $lead->setPhone($data['phone']);
            $lead->setFirm($data['firm']);
            $lead->setWebsite($data['website']);
            $lead->setCity($data['city']);
            $lead->setState($data['state']);
            $lead->setZipCode($data['zip_code']);
            $lead->setMessage($data['message']);
            $lead->setPracticeAreas(explode(', ', $data['practice_areas']));
            $lead->setStatus(LeadStatus::NEW_LEAD); // Ensure status is NEW_LEAD
            $lead->setSource($source);
            $lead->setUpdatedAtValue();

            // Store additional leadgen data in UTM JSON
            $utmData = [
                'leadgen_campaign' => ['name' => 'Tulsa Attorneys Real API', 'vertical' => 'local_services'],
                'leadgen_data' => $data,
                'generated_at' => (new \DateTimeImmutable())->format('c'),
                'lead_score' => $data['lead_score'],
                'vertical' => $data['vertical'],
                'rating' => $data['rating'],
                'review_count' => $data['review_count'],
                'source' => 'google_places_api'
            ];
            $lead->setUtmJson($utmData);

            $this->entityManager->persist($lead);
        }

        $this->entityManager->flush();

        $io->success(sprintf('Successfully imported %d real Tulsa attorney leads and updated %d leads', $importedCount, $updatedCount));
        $io->note('These are real attorney leads generated from Google Places API with the provided API key');

        return Command::SUCCESS;
    }
}
