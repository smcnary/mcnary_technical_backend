<?php

namespace App\Repository;

use App\Entity\Page;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Page>
 *
 * @method Page|null find($id, $lockMode = null, $lockVersion = null)
 * @method Page|null findOneBy(array $criteria, array $orderBy = null)
 * @method Page[]    findAll()
 * @method Page[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
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

    public function findByCriteria(array $criteria, array $sortFields = [], ?int $limit = null, ?int $offset = null): array
    {
        $qb = $this->createQueryBuilder('p');

        // Apply filters
        if (isset($criteria['status'])) {
            $qb->andWhere('p.status = :status')
               ->setParameter('status', $criteria['status']);
        }

        if (isset($criteria['type']) && $criteria['type']) {
            $qb->andWhere('p.type = :type')
               ->setParameter('type', $criteria['type']);
        }

        if (isset($criteria['slug']) && $criteria['slug']) {
            $qb->andWhere('p.slug = :slug')
               ->setParameter('slug', $criteria['slug']);
        }

        if (isset($criteria['tenant_id']) && $criteria['tenant_id']) {
            $qb->andWhere('p.tenantId = :tenant_id')
               ->setParameter('tenant_id', $criteria['tenant_id']);
        }

        if (isset($criteria['client_id']) && $criteria['client_id']) {
            $qb->andWhere('p.clientId = :client_id')
               ->setParameter('client_id', $criteria['client_id']);
        }

        // Apply sorting
        foreach ($sortFields as $field => $direction) {
            if (property_exists(Page::class, $field)) {
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
        if (isset($criteria['status'])) {
            $qb->andWhere('p.status = :status')
               ->setParameter('status', $criteria['status']);
        }

        if (isset($criteria['type']) && $criteria['type']) {
            $qb->andWhere('p.type = :type')
               ->setParameter('type', $criteria['type']);
        }

        if (isset($criteria['slug']) && $criteria['slug']) {
            $qb->andWhere('p.slug = :slug')
               ->setParameter('slug', $criteria['slug']);
        }

        if (isset($criteria['tenant_id']) && $criteria['tenant_id']) {
            $qb->andWhere('p.tenantId = :tenant_id')
               ->setParameter('tenant_id', $criteria['tenant_id']);
        }

        if (isset($criteria['client_id']) && $criteria['client_id']) {
            $qb->andWhere('p.clientId = :client_id')
               ->setParameter('client_id', $criteria['client_id']);
        }

        return $qb->getQuery()->getSingleScalarResult();
    }

    public function findBySlug(string $slug): ?Page
    {
        return $this->findOneBy(['slug' => $slug, 'status' => 'published']);
    }

    public function findPublishedPages(): array
    {
        return $this->findBy(['status' => 'published'], ['sortOrder' => 'ASC']);
    }

    public function findByType(string $type): array
    {
        return $this->findBy(['type' => $type, 'status' => 'published'], ['sortOrder' => 'ASC']);
    }

    public function findByTenantId(string $tenantId): array
    {
        return $this->findBy(['tenantId' => $tenantId, 'status' => 'published'], ['sortOrder' => 'ASC']);
    }

    public function findByClientId(string $clientId): array
    {
        return $this->findBy(['clientId' => $clientId, 'status' => 'published'], ['sortOrder' => 'ASC']);
    }
}
