<?php

namespace App\Repository;

use App\Entity\Client;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;

/**
 * @extends ServiceEntityRepository<Client>
 *
 * @method Client|null find($id, $lockMode = null, $lockVersion = null)
 * @method Client|null findOneBy(array $criteria, array $orderBy = null)
 * @method Client[]    findAll()
 * @method Client[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ClientRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Client::class);
    }

    public function save(Client $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Client $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findByCriteria(array $criteria, array $sortFields = [], ?int $limit = null, ?int $offset = null): array
    {
        $qb = $this->createQueryBuilder('c');

        // Apply filters
        if (isset($criteria['search']) && $criteria['search']) {
            $qb->andWhere('c.name LIKE :search OR c.description LIKE :search OR c.website LIKE :search')
               ->setParameter('search', '%' . $criteria['search'] . '%');
        }

        if (isset($criteria['status']) && $criteria['status']) {
            $qb->andWhere('c.status = :status')
               ->setParameter('status', $criteria['status']);
        }

        if (isset($criteria['tenant_id']) && $criteria['tenant_id']) {
            $qb->andWhere('c.tenantId = :tenant_id')
               ->setParameter('tenant_id', $criteria['tenant_id']);
        }

        // Apply sorting
        foreach ($sortFields as $field => $direction) {
            if (property_exists(Client::class, $field)) {
                $qb->addOrderBy('c.' . $field, $direction);
            }
        }

        // Default sorting if none specified
        if (empty($sortFields)) {
            $qb->orderBy('c.name', 'ASC');
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
        $qb = $this->createQueryBuilder('c')
                   ->select('COUNT(c.id)');

        // Apply filters
        if (isset($criteria['search']) && $criteria['search']) {
            $qb->andWhere('c.name LIKE :search OR c.description LIKE :search OR c.website LIKE :search')
               ->setParameter('search', '%' . $criteria['search'] . '%');
        }

        if (isset($criteria['status']) && $criteria['status']) {
            $qb->andWhere('c.status = :status')
               ->setParameter('status', $criteria['status']);
        }

        if (isset($criteria['tenant_id']) && $criteria['tenant_id']) {
            $qb->andWhere('c.tenantId = :tenant_id')
               ->setParameter('tenant_id', $criteria['tenant_id']);
        }

        return $qb->getQuery()->getSingleScalarResult();
    }

    public function findBySlug(string $slug): ?Client
    {
        return $this->findOneBy(['slug' => $slug]);
    }

    public function findActiveClients(): array
    {
        return $this->findBy(['status' => 'active'], ['name' => 'ASC']);
    }

    public function findByTenantId(string $tenantId): array
    {
        return $this->findBy(['tenantId' => $tenantId], ['name' => 'ASC']);
    }
}
