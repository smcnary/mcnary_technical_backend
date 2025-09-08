<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Page;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Page>
 */
class PageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Page::class);
    }

    public function save(Page $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Page $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return Page[] Returns an array of Page objects
     */
    public function findByAuditRun(string $auditRunId): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.auditRun = :auditRunId')
            ->setParameter('auditRunId', $auditRunId)
            ->orderBy('p.createdAt', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Page[] Returns an array of Page objects
     */
    public function findSuccessfulByAuditRun(string $auditRunId): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.auditRun = :auditRunId')
            ->andWhere('p.statusCode >= 200')
            ->andWhere('p.statusCode < 300')
            ->setParameter('auditRunId', $auditRunId)
            ->orderBy('p.createdAt', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Page[] Returns an array of Page objects
     */
    public function findIndexableByAuditRun(string $auditRunId): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.auditRun = :auditRunId')
            ->andWhere('p.statusCode >= 200')
            ->andWhere('p.statusCode < 300')
            ->andWhere('p.robotsDirectives NOT LIKE :noindex')
            ->setParameter('auditRunId', $auditRunId)
            ->setParameter('noindex', '%noindex%')
            ->orderBy('p.createdAt', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
