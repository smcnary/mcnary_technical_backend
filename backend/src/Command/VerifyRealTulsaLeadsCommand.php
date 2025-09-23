<?php

namespace App\Command;

use App\Entity\Lead;
use App\ValueObject\LeadStatus;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:verify-real-tulsa-leads',
    description: 'Verifies that real Tulsa attorneys leads from Google Places API are in the database with "new_lead" status.',
)]
class VerifyRealTulsaLeadsCommand extends Command
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $realTulsaAttorneysLeads = $this->entityManager->getRepository(Lead::class)->createQueryBuilder('l')
            ->leftJoin('l.source', 's')
            ->where('l.city = :city')
            ->andWhere('l.state = :state')
            ->andWhere('l.status = :status')
            ->andWhere('s.name = :sourceName')
            ->setParameter('city', 'Tulsa')
            ->setParameter('state', 'OK')
            ->setParameter('status', LeadStatus::NEW_LEAD)
            ->setParameter('sourceName', 'Leadgen: Tulsa Attorneys Real API')
            ->getQuery()
            ->getResult();

        if (empty($realTulsaAttorneysLeads)) {
            $io->warning('No real Tulsa attorney leads found with "new_lead" status.');
            return Command::FAILURE;
        }

        $io->success(sprintf('Found %d real Tulsa attorney leads:', count($realTulsaAttorneysLeads)));

        foreach ($realTulsaAttorneysLeads as $lead) {
            $io->section($lead->getFullName());
            $io->definitionList(
                ['Email' => $lead->getEmail()],
                ['Phone' => $lead->getPhone()],
                ['Firm' => $lead->getFirm()],
                ['Website' => $lead->getWebsite()],
                ['City' => $lead->getCity()],
                ['State' => $lead->getState()],
                ['Status' => $lead->getStatusValue()],
                ['Status Label' => $lead->getStatusLabel()],
                ['Source' => $lead->getSource()?->getName()],
                ['Practice Areas' => implode(', ', $lead->getPracticeAreas())],
                ['Message' => $lead->getMessage()],
                ['Created' => $lead->getCreatedAt()->format('Y-m-d H:i:s')]
            );

            $utmData = $lead->getUtmJson();
            if (isset($utmData['leadgen_data'])) {
                $io->comment('Leadgen Data:');
                $io->definitionList(
                    ['Lead Score' => $utmData['leadgen_data']['lead_score'] ?? 'N/A'],
                    ['Vertical' => $utmData['leadgen_data']['vertical'] ?? 'N/A'],
                    ['Rating' => $utmData['leadgen_data']['rating'] ?? 'N/A'],
                    ['Review Count' => $utmData['leadgen_data']['review_count'] ?? 'N/A'],
                    ['Campaign' => $utmData['leadgen_campaign']['name'] ?? 'N/A'],
                    ['Source' => $utmData['source'] ?? 'N/A']
                );
            }
        }

        // Get status summary for all leads
        $statusCounts = $this->entityManager->getRepository(Lead::class)->createQueryBuilder('l')
            ->select('l.status, COUNT(l.id) as count')
            ->groupBy('l.status')
            ->getQuery()
            ->getArrayResult();

        $io->section('Status Summary');
        foreach ($statusCounts as $statusCount) {
            $statusValue = $statusCount['status'] instanceof LeadStatus ? $statusCount['status']->value : $statusCount['status'];
            $io->writeln(sprintf(' %s: %d leads', ucfirst($statusValue), $statusCount['count']));
        }

        $io->success('Real Tulsa attorneys leads verification completed successfully!');

        return Command::SUCCESS;
    }
}
