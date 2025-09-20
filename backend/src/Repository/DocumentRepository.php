<?php

namespace App\Repository;

use App\Entity\Document;
use App\Entity\Client;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Document>
 */
class DocumentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Document::class);
    }

    /**
     * Find documents by filters
     */
    public function findByFilters(array $filters = []): array
    {
        $qb = $this->createQueryBuilder('d')
            ->leftJoin('d.client', 'c')
            ->leftJoin('d.createdBy', 'u')
            ->addSelect('c', 'u');

        if (!empty($filters['status'])) {
            $qb->andWhere('d.status = :status')
               ->setParameter('status', $filters['status']);
        }

        if (!empty($filters['type'])) {
            $qb->andWhere('d.type = :type')
               ->setParameter('type', $filters['type']);
        }

        if (!empty($filters['client_id'])) {
            $qb->andWhere('d.client = :client_id')
               ->setParameter('client_id', $filters['client_id']);
        }

        if (!empty($filters['search'])) {
            $qb->andWhere('(d.title LIKE :search OR d.description LIKE :search)')
               ->setParameter('search', '%' . $filters['search'] . '%');
        }

        $qb->orderBy('d.createdAt', 'DESC');

        return $qb->getQuery()->getResult();
    }

    /**
     * Find documents by client
     */
    public function findByClient(Client $client, array $filters = []): array
    {
        $qb = $this->createQueryBuilder('d')
            ->where('d.client = :client')
            ->setParameter('client', $client)
            ->leftJoin('d.createdBy', 'u')
            ->addSelect('u');

        if (!empty($filters['status'])) {
            $qb->andWhere('d.status = :status')
               ->setParameter('status', $filters['status']);
        }

        if (!empty($filters['type'])) {
            $qb->andWhere('d.type = :type')
               ->setParameter('type', $filters['type']);
        }

        if (!empty($filters['requires_signature'])) {
            $qb->andWhere('d.requiresSignature = :requires_signature')
               ->setParameter('requires_signature', $filters['requires_signature']);
        }

        $qb->orderBy('d.createdAt', 'DESC');

        return $qb->getQuery()->getResult();
    }

    /**
     * Find documents ready for signature
     */
    public function findReadyForSignature(Client $client = null): array
    {
        $qb = $this->createQueryBuilder('d')
            ->where('d.status = :status')
            ->setParameter('status', 'ready_for_signature')
            ->leftJoin('d.client', 'c')
            ->leftJoin('d.createdBy', 'u')
            ->addSelect('c', 'u');

        if ($client) {
            $qb->andWhere('d.client = :client')
               ->setParameter('client', $client);
        }

        $qb->orderBy('d.sentForSignatureAt', 'ASC');

        return $qb->getQuery()->getResult();
    }

    /**
     * Find documents by status
     */
    public function findByStatus(string $status): array
    {
        return $this->createQueryBuilder('d')
            ->where('d.status = :status')
            ->setParameter('status', $status)
            ->leftJoin('d.client', 'c')
            ->leftJoin('d.createdBy', 'u')
            ->addSelect('c', 'u')
            ->orderBy('d.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find documents by type
     */
    public function findByType(string $type): array
    {
        return $this->createQueryBuilder('d')
            ->where('d.type = :type')
            ->setParameter('type', $type)
            ->leftJoin('d.client', 'c')
            ->leftJoin('d.createdBy', 'u')
            ->addSelect('c', 'u')
            ->orderBy('d.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find documents that are templates
     */
    public function findTemplates(): array
    {
        return $this->createQueryBuilder('d')
            ->where('d.isTemplate = :isTemplate')
            ->setParameter('isTemplate', true)
            ->leftJoin('d.createdBy', 'u')
            ->addSelect('u')
            ->orderBy('d.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find expired documents
     */
    public function findExpired(): array
    {
        return $this->createQueryBuilder('d')
            ->where('d.expiresAt < :now')
            ->setParameter('now', new \DateTimeImmutable())
            ->leftJoin('d.client', 'c')
            ->leftJoin('d.createdBy', 'u')
            ->addSelect('c', 'u')
            ->orderBy('d.expiresAt', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Count documents by status
     */
    public function countByStatus(): array
    {
        $qb = $this->createQueryBuilder('d')
            ->select('d.status, COUNT(d.id) as count')
            ->groupBy('d.status');

        $results = $qb->getQuery()->getResult();
        
        $counts = [];
        foreach ($results as $result) {
            $counts[$result['status']] = (int) $result['count'];
        }

        return $counts;
    }

    /**
     * Count documents by type
     */
    public function countByType(): array
    {
        $qb = $this->createQueryBuilder('d')
            ->select('d.type, COUNT(d.id) as count')
            ->groupBy('d.type');

        $results = $qb->getQuery()->getResult();
        
        $counts = [];
        foreach ($results as $result) {
            $counts[$result['type']] = (int) $result['count'];
        }

        return $counts;
    }
}
