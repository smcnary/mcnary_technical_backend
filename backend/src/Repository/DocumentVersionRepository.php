<?php

namespace App\Repository;

use App\Entity\DocumentVersion;
use App\Entity\Document;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DocumentVersion>
 */
class DocumentVersionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DocumentVersion::class);
    }

    /**
     * Find versions by document
     */
    public function findByDocument(Document $document): array
    {
        return $this->createQueryBuilder('dv')
            ->where('dv.document = :document')
            ->setParameter('document', $document)
            ->leftJoin('dv.createdBy', 'u')
            ->addSelect('u')
            ->orderBy('dv.versionNumber', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find current version of a document
     */
    public function findCurrentByDocument(Document $document): ?DocumentVersion
    {
        return $this->createQueryBuilder('dv')
            ->where('dv.document = :document')
            ->andWhere('dv.isCurrent = :isCurrent')
            ->setParameter('document', $document)
            ->setParameter('isCurrent', true)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Find versions by user
     */
    public function findByUser(User $user): array
    {
        return $this->createQueryBuilder('dv')
            ->where('dv.createdBy = :user')
            ->setParameter('user', $user)
            ->leftJoin('dv.document', 'd')
            ->leftJoin('d.client', 'c')
            ->addSelect('d', 'c')
            ->orderBy('dv.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Count versions by document
     */
    public function countByDocument(Document $document): int
    {
        return $this->createQueryBuilder('dv')
            ->select('COUNT(dv.id)')
            ->where('dv.document = :document')
            ->setParameter('document', $document)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Find versions in date range
     */
    public function findByDateRange(\DateTimeImmutable $startDate, \DateTimeImmutable $endDate): array
    {
        return $this->createQueryBuilder('dv')
            ->where('dv.createdAt >= :startDate')
            ->andWhere('dv.createdAt <= :endDate')
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate)
            ->leftJoin('dv.document', 'd')
            ->leftJoin('dv.createdBy', 'u')
            ->addSelect('d', 'u')
            ->orderBy('dv.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
