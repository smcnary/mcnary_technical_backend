<?php

namespace App\Repository;

use App\Entity\DocumentTemplate;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DocumentTemplate>
 */
class DocumentTemplateRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DocumentTemplate::class);
    }

    /**
     * Find active templates
     */
    public function findActive(): array
    {
        return $this->createQueryBuilder('dt')
            ->where('dt.isActive = :isActive')
            ->setParameter('isActive', true)
            ->orderBy('dt.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find templates by type
     */
    public function findByType(string $type): array
    {
        return $this->createQueryBuilder('dt')
            ->where('dt.type = :type')
            ->andWhere('dt.isActive = :isActive')
            ->setParameter('type', $type)
            ->setParameter('isActive', true)
            ->orderBy('dt.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find templates by search term
     */
    public function findBySearch(string $search): array
    {
        return $this->createQueryBuilder('dt')
            ->where('dt.isActive = :isActive')
            ->andWhere('(dt.name LIKE :search OR dt.description LIKE :search)')
            ->setParameter('isActive', true)
            ->setParameter('search', '%' . $search . '%')
            ->orderBy('dt.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find most used templates
     */
    public function findMostUsed(int $limit = 10): array
    {
        return $this->createQueryBuilder('dt')
            ->where('dt.isActive = :isActive')
            ->setParameter('isActive', true)
            ->orderBy('dt.usageCount', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * Find templates that require signature
     */
    public function findRequiringSignature(): array
    {
        return $this->createQueryBuilder('dt')
            ->where('dt.requiresSignature = :requiresSignature')
            ->andWhere('dt.isActive = :isActive')
            ->setParameter('requiresSignature', true)
            ->setParameter('isActive', true)
            ->orderBy('dt.name', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
