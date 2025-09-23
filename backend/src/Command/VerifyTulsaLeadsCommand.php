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
    name: 'app:verify-tulsa-leads',
    description: 'Verify Tulsa attorneys leads are properly imported',
)]
class VerifyTulsaLeadsCommand extends Command
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

        // Find leads with Tulsa attorney emails
        $tulsaEmails = [
            'contact@johnsonlawtulsa.com',
            'info@smithlegalok.com',
            'hello@williamspartners.com',
            'info@brownlawtulsa.com',
            'contact@davislegalok.com'
        ];

        $io->title('Verifying Tulsa Attorneys Leads');

        $leads = $this->leadRepository->findBy(['email' => $tulsaEmails]);
        
        if (empty($leads)) {
            $io->error('No Tulsa attorney leads found!');
            return Command::FAILURE;
        }

        $io->success(sprintf('Found %d Tulsa attorney leads:', count($leads)));

        foreach ($leads as $lead) {
            $io->section($lead->getFullName());
            $io->listing([
                'Email: ' . $lead->getEmail(),
                'Phone: ' . ($lead->getPhone() ?? 'N/A'),
                'Firm: ' . ($lead->getFirm() ?? 'N/A'),
                'Website: ' . ($lead->getWebsite() ?? 'N/A'),
                'City: ' . ($lead->getCity() ?? 'N/A'),
                'State: ' . ($lead->getState() ?? 'N/A'),
                'Status: ' . $lead->getStatusValue(),
                'Status Label: ' . $lead->getStatusLabel(),
                'Source: ' . ($lead->getSource()?->getName() ?? 'N/A'),
                'Practice Areas: ' . implode(', ', $lead->getPracticeAreas()),
                'Message: ' . ($lead->getMessage() ?? 'N/A'),
                'Created: ' . $lead->getCreatedAt()->format('Y-m-d H:i:s')
            ]);

            // Show UTM data if available
            $utmJson = $lead->getUtmJson();
            if ($utmJson && isset($utmJson['lead_score'])) {
                $io->text('Leadgen Data:');
                $io->listing([
                    'Lead Score: ' . ($utmJson['lead_score'] ?? 'N/A'),
                    'Vertical: ' . ($utmJson['vertical'] ?? 'N/A'),
                    'Rating: ' . ($utmJson['rating'] ?? 'N/A'),
                    'Review Count: ' . ($utmJson['review_count'] ?? 'N/A'),
                    'Campaign: ' . ($utmJson['leadgen_campaign'] ?? 'N/A')
                ]);
            }
        }

        // Count by status
        $statusCounts = [];
        foreach ($leads as $lead) {
            $status = $lead->getStatusValue();
            $statusCounts[$status] = ($statusCounts[$status] ?? 0) + 1;
        }

        $io->section('Status Summary');
        foreach ($statusCounts as $status => $count) {
            $io->text(sprintf('%s: %d leads', ucfirst($status), $count));
        }

        $io->success('Tulsa attorneys leads verification completed successfully!');

        return Command::SUCCESS;
    }
}
