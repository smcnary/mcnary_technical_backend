<?php

namespace App\Repository;

use App\Entity\Package;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Package>
 *
 * @method Package|null find($id, $lockMode = null, $lockVersion = null)
 * @method Package|null findOneBy(array $criteria, array $orderBy = null)
 * @method Package[]    findAll()
 * @method Package[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PackageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Package::class);
    }

    public function save(Package $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Package $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findByCriteria(array $criteria, array $sortFields = [], int $limit = null, int $offset = null): array
    {
        $qb = $this->createQueryBuilder('p');

        // Apply filters
        if (isset($criteria['is_active'])) {
            $qb->andWhere('p.isActive = :is_active')
               ->setParameter('is_active', $criteria['is_active']);
        }

        if (isset($criteria['is_popular'])) {
            $qb->andWhere('p.isPopular = :is_popular')
               ->setParameter('is_popular', $criteria['is_popular']);
        }

        if (isset($criteria['billing_cycle']) && $criteria['billing_cycle']) {
            $qb->andWhere('p.billingCycle = :billing_cycle')
               ->setParameter('billing_cycle', $criteria['billing_cycle']);
        }

        if (isset($criteria['client_id']) && $criteria['client_id']) {
            $qb->andWhere('p.clientId = :client_id')
               ->setParameter('client_id', $criteria['client_id']);
        }

        if (isset($criteria['tenant_id']) && $criteria['tenant_id']) {
            $qb->andWhere('p.tenantId = :tenant_id')
               ->setParameter('tenant_id', $criteria['tenant_id']);
        }

        // Apply sorting
        foreach ($sortFields as $field => $direction) {
            if (property_exists(Package::class, $field)) {
                $qb->addOrderBy('p.' . $field, $direction);
            }
        }

        // Default sorting if none specified
        if (empty($sortFields)) {
            $qb->orderBy('p.sortOrder', 'ASC');
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
        $qb = $this->createQueryBuilder('p')
                   ->select('COUNT(p.id)');

        // Apply filters
        if (isset($criteria['is_active'])) {
            $qb->andWhere('p.isActive = :is_active')
               ->setParameter('is_active', $criteria['is_active']);
        }

        if (isset($criteria['is_popular'])) {
            $qb->andWhere('p.isPopular = :is_popular')
               ->setParameter('is_popular', $criteria['is_popular']);
        }

        if (isset($criteria['billing_cycle']) && $criteria['billing_cycle']) {
            $qb->andWhere('p.billingCycle = :billing_cycle')
               ->setParameter('billing_cycle', $criteria['billing_cycle']);
        }

        if (isset($criteria['client_id']) && $criteria['client_id']) {
            $qb->andWhere('p.clientId = :client_id')
               ->setParameter('client_id', $criteria['client_id']);
        }

        if (isset($criteria['tenant_id']) && $criteria['tenant_id']) {
            $qb->andWhere('p.tenantId = :tenant_id')
               ->setParameter('tenant_id', $criteria['tenant_id']);
        }

        return $qb->getQuery()->getSingleScalarResult();
    }

    public function findBySlug(string $slug): ?Package
    {
        return $this->findOneBy(['slug' => $slug, 'isActive' => true]);
    }

    public function findActivePackages(): array
    {
        return $this->findBy(['isActive' => true], ['sortOrder' => 'ASC']);
    }

    public function findPopularPackages(): array
    {
        return $this->findBy(['isActive' => true, 'isPopular' => true], ['sortOrder' => 'ASC']);
    }

    public function findByBillingCycle(string $billingCycle): array
    {
        return $this->findBy(['billingCycle' => $billingCycle, 'isActive' => true], ['sortOrder' => 'ASC']);
    }

    public function findByTenantId(string $tenantId): array
    {
        return $this->findBy(['tenantId' => $tenantId, 'isActive' => true], ['sortOrder' => 'ASC']);
    }

    public function findByClientId(string $clientId): array
    {
        return $this->findBy(['clientId' => $clientId, 'isActive' => true], ['sortOrder' => 'ASC']);
    }
}
