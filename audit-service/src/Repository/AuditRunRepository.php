<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\AuditRun;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AuditRun>
 */
class AuditRunRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AuditRun::class);
    }

    public function save(AuditRun $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(AuditRun $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return AuditRun[] Returns an array of AuditRun objects
     */
    public function findByTenant(string $tenantId): array
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.tenant = :tenantId')
            ->setParameter('tenantId', $tenantId)
            ->orderBy('a.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return AuditRun[] Returns an array of AuditRun objects
     */
    public function findByProject(string $projectId): array
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.project = :projectId')
            ->setParameter('projectId', $projectId)
            ->orderBy('a.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
