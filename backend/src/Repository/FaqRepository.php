<?php

namespace App\Repository;

use App\Entity\Faq;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Faq>
 *
 * @method Faq|null find($id, $lockMode = null, $lockVersion = null)
 * @method Faq|null findOneBy(array $criteria, array $orderBy = null)
 * @method Faq[]    findAll()
 * @method Faq[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FaqRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Faq::class);
    }

    public function save(Faq $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Faq $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findByCriteria(array $criteria, array $sortFields = [], int $limit = null, int $offset = null): array
    {
        $qb = $this->createQueryBuilder('f');

        // Apply filters
        if (isset($criteria['isActive'])) {
            $qb->andWhere('f.isActive = :is_active')
               ->setParameter('is_active', $criteria['isActive']);
        }

        if (isset($criteria['search']) && $criteria['search']) {
            $qb->andWhere('f.question LIKE :search OR f.answer LIKE :search')
               ->setParameter('search', '%' . $criteria['search'] . '%');
        }

        if (isset($criteria['client_id']) && $criteria['client_id']) {
            // Note: FAQ entity doesn't have client_id, only tenant_id
            // This would need to be implemented if client scoping is needed
        }

        // Apply sorting
        foreach ($sortFields as $field => $direction) {
            if (property_exists(Faq::class, $field)) {
                $qb->addOrderBy('f.' . $field, $direction);
            }
        }

        // Default sorting if none specified
        if (empty($sortFields)) {
            $qb->orderBy('f.sort', 'ASC');
        }

        // Apply pagination
        if ($limit) {
            $qb->setMaxResults($limit);
        }

        if ($offset) {
            $qb->setFirstResult($offset);
        }

        return $qb->getQuery()->getResult();
    }

    public function countByCriteria(array $criteria): int
    {
        $qb = $this->createQueryBuilder('f')
                   ->select('COUNT(f.id)');

        // Apply filters
        if (isset($criteria['isActive'])) {
            $qb->andWhere('f.isActive = :is_active')
               ->setParameter('is_active', $criteria['isActive']);
        }

        if (isset($criteria['search']) && $criteria['search']) {
            $qb->andWhere('f.question LIKE :search OR f.answer LIKE :search')
               ->setParameter('search', '%' . $criteria['search'] . '%');
        }

        if (isset($criteria['client_id']) && $criteria['client_id']) {
            // Note: FAQ entity doesn't have client_id, only tenant_id
            // This would need to be implemented if client scoping is needed
        }

        return $qb->getQuery()->getSingleScalarResult();
    }

    public function findActiveFaqs(): array
    {
        return $this->findBy(['isActive' => true], ['sort' => 'ASC']);
    }

    public function findByTenantId(string $tenantId): array
    {
        return $this->createQueryBuilder('f')
                   ->join('f.tenant', 't')
                   ->andWhere('t.id = :tenant_id')
                   ->andWhere('f.isActive = :is_active')
                   ->setParameter('tenant_id', $tenantId)
                   ->setParameter('is_active', true)
                   ->orderBy('f.sort', 'ASC')
                   ->getQuery()
                   ->getResult();
    }

    public function searchFaqs(string $searchTerm): array
    {
        return $this->createQueryBuilder('f')
                   ->andWhere('f.question LIKE :search OR f.answer LIKE :search')
                   ->andWhere('f.isActive = :is_active')
                   ->setParameter('search', '%' . $searchTerm . '%')
                   ->setParameter('is_active', true)
                   ->orderBy('f.sort', 'ASC')
                   ->getQuery()
                   ->getResult();
    }
}
