<?php

namespace App\Repository;

use App\Entity\DocumentSignature;
use App\Entity\Document;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DocumentSignature>
 */
class DocumentSignatureRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DocumentSignature::class);
    }

    /**
     * Find signatures by document
     */
    public function findByDocument(Document $document): array
    {
        return $this->createQueryBuilder('ds')
            ->where('ds.document = :document')
            ->setParameter('document', $document)
            ->leftJoin('ds.signedBy', 'u')
            ->addSelect('u')
            ->orderBy('ds.signedAt', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find signatures by user
     */
    public function findByUser(User $user): array
    {
        return $this->createQueryBuilder('ds')
            ->where('ds.signedBy = :user')
            ->setParameter('user', $user)
            ->leftJoin('ds.document', 'd')
            ->leftJoin('d.client', 'c')
            ->addSelect('d', 'c')
            ->orderBy('ds.signedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find pending signatures for a user
     */
    public function findPendingForUser(User $user): array
    {
        return $this->createQueryBuilder('ds')
            ->where('ds.signedBy = :user')
            ->andWhere('ds.status = :status')
            ->setParameter('user', $user)
            ->setParameter('status', 'pending')
            ->leftJoin('ds.document', 'd')
            ->leftJoin('d.client', 'c')
            ->addSelect('d', 'c')
            ->orderBy('ds.createdAt', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find signed documents for a user
     */
    public function findSignedByUser(User $user): array
    {
        return $this->createQueryBuilder('ds')
            ->where('ds.signedBy = :user')
            ->andWhere('ds.status = :status')
            ->setParameter('user', $user)
            ->setParameter('status', 'signed')
            ->leftJoin('ds.document', 'd')
            ->leftJoin('d.client', 'c')
            ->addSelect('d', 'c')
            ->orderBy('ds.signedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Count signatures by status
     */
    public function countByStatus(): array
    {
        $qb = $this->createQueryBuilder('ds')
            ->select('ds.status, COUNT(ds.id) as count')
            ->groupBy('ds.status');

        $results = $qb->getQuery()->getResult();
        
        $counts = [];
        foreach ($results as $result) {
            $counts[$result['status']] = (int) $result['count'];
        }

        return $counts;
    }

    /**
     * Find signatures in date range
     */
    public function findByDateRange(\DateTimeImmutable $startDate, \DateTimeImmutable $endDate): array
    {
        return $this->createQueryBuilder('ds')
            ->where('ds.signedAt >= :startDate')
            ->andWhere('ds.signedAt <= :endDate')
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate)
            ->leftJoin('ds.document', 'd')
            ->leftJoin('ds.signedBy', 'u')
            ->addSelect('d', 'u')
            ->orderBy('ds.signedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
