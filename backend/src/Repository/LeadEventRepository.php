<?php

namespace App\Repository;

use App\Entity\LeadEvent;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<LeadEvent>
 */
class LeadEventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LeadEvent::class);
    }

    /**
     * Find events for a specific lead
     */
    public function findByLead(string $leadId): array
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.lead = :leadId')
            ->setParameter('leadId', $leadId)
            ->orderBy('e.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Get event statistics for a lead
     */
    public function getLeadStatistics(string $leadId): array
    {
        $qb = $this->createQueryBuilder('e')
            ->select([
                'COUNT(e.id) as total_events',
                'SUM(CASE WHEN e.type = :phone_call THEN 1 ELSE 0 END) as phone_calls',
                'SUM(CASE WHEN e.type = :email THEN 1 ELSE 0 END) as emails',
                'SUM(CASE WHEN e.type = :meeting THEN 1 ELSE 0 END) as meetings',
                'SUM(CASE WHEN e.type = :application THEN 1 ELSE 0 END) as applications',
                'SUM(e.duration) as total_duration',
                'MAX(e.createdAt) as last_contact'
            ])
            ->andWhere('e.lead = :leadId')
            ->setParameter('leadId', $leadId)
            ->setParameter('phone_call', 'phone_call')
            ->setParameter('email', 'email')
            ->setParameter('meeting', 'meeting')
            ->setParameter('application', 'application');

        $result = $qb->getQuery()->getSingleResult();

        return [
            'total_events' => (int) $result['total_events'],
            'phone_calls' => (int) $result['phone_calls'],
            'emails' => (int) $result['emails'],
            'meetings' => (int) $result['meetings'],
            'applications' => (int) $result['applications'],
            'total_duration' => (int) $result['total_duration'],
            'last_contact' => $result['last_contact']
        ];
    }

    /**
     * Get events by type for a lead
     */
    public function findByLeadAndType(string $leadId, string $type): array
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.lead = :leadId')
            ->andWhere('e.type = :type')
            ->setParameter('leadId', $leadId)
            ->setParameter('type', $type)
            ->orderBy('e.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Get recent events across all leads
     */
    public function findRecentEvents(int $limit = 10): array
    {
        return $this->createQueryBuilder('e')
            ->orderBy('e.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}
