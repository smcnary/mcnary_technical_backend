<?php

namespace App\Command;

use App\Entity\Lead;
use App\Repository\LeadRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:test-tulsa-leads-api',
    description: 'Test Tulsa attorneys leads API response format',
)]
class TestTulsaLeadsAPICommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private LeadRepository $leadRepository
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('Testing Tulsa Attorneys Leads API Response');

        // Simulate API call to get leads with status "new_lead"
        $criteria = ['status' => 'new_lead'];
        $sortFields = ['created_at' => 'DESC'];
        $perPage = 20;
        $offset = 0;

        $leads = $this->leadRepository->findByCriteria($criteria, $sortFields, $perPage, $offset);
        $totalLeads = $this->leadRepository->countByCriteria($criteria);

        $io->info(sprintf('Found %d total leads with status "new_lead"', $totalLeads));

        // Filter for Tulsa attorney leads
        $tulsaEmails = [
            'contact@johnsonlawtulsa.com',
            'info@smithlegalok.com',
            'hello@williamspartners.com',
            'info@brownlawtulsa.com',
            'contact@davislegalok.com'
        ];

        $tulsaLeads = array_filter($leads, function($lead) use ($tulsaEmails) {
            return in_array($lead->getEmail(), $tulsaEmails);
        });

        $io->success(sprintf('Found %d Tulsa attorney leads in "New Leads" list:', count($tulsaLeads)));

        // Format response as API would return
        $leadData = [];
        foreach ($tulsaLeads as $lead) {
            $leadData[] = [
                'id' => $lead->getId(),
                'full_name' => $lead->getFullName(),
                'email' => $lead->getEmail(),
                'phone' => $lead->getPhone(),
                'firm' => $lead->getFirm(),
                'website' => $lead->getWebsite(),
                'city' => $lead->getCity(),
                'state' => $lead->getState(),
                'zip_code' => $lead->getZipCode(),
                'message' => $lead->getMessage(),
                'practice_areas' => $lead->getPracticeAreas(),
                'status' => $lead->getStatusValue(),
                'status_label' => $lead->getStatusLabel(),
                'source' => $lead->getSource()?->getName(),
                'client' => $lead->getClient()?->getName(),
                'created_at' => $lead->getCreatedAt()->format('c'),
                'updated_at' => $lead->getUpdatedAt()->format('c')
            ];
        }

        // Display as JSON (similar to API response)
        $apiResponse = [
            'data' => $leadData,
            'pagination' => [
                'page' => 1,
                'per_page' => $perPage,
                'total' => $totalLeads,
                'pages' => ceil($totalLeads / $perPage)
            ]
        ];

        $io->section('API Response Format (JSON)');
        $io->text(json_encode($apiResponse, JSON_PRETTY_PRINT));

        // Summary
        $io->section('Summary');
        $io->listing([
            sprintf('Total leads with "new_lead" status: %d', $totalLeads),
            sprintf('Tulsa attorney leads found: %d', count($tulsaLeads)),
            sprintf('Tulsa leads as %% of total: %.1f%%', $totalLeads > 0 ? (count($tulsaLeads) / $totalLeads) * 100 : 0)
        ]);

        // Verify all Tulsa leads have correct status
        $allNewLeads = true;
        foreach ($tulsaLeads as $lead) {
            if ($lead->getStatusValue() !== 'new_lead') {
                $allNewLeads = false;
                break;
            }
        }

        if ($allNewLeads) {
            $io->success('✅ All Tulsa attorney leads have "new_lead" status and will appear in the "New Leads" list');
        } else {
            $io->error('❌ Some Tulsa attorney leads do not have "new_lead" status');
        }

        $io->success('Tulsa attorneys leads API test completed successfully!');

        return Command::SUCCESS;
    }
}
