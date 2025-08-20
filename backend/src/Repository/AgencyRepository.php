<?php

namespace App\Repository;

use App\Entity\Agency;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Agency>
 *
 * @method Agency|null find($id, $lockMode = null, $lockVersion = null)
 * @method Agency|null findOneBy(array $criteria, array $orderBy = null)
 * @method Agency[]    findAll()
 * @method Agency[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AgencyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Agency::class);
    }

    public function save(Agency $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Agency $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Find agencies by criteria with pagination and sorting
     */
    public function findByCriteria(array $criteria, array $sortFields = [], int $limit = 20, int $offset = 0): array
    {
        $qb = $this->createQueryBuilder('a');

        // Apply search criteria
        if (isset($criteria['search']) && $criteria['search']) {
            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->like('a.name', ':search'),
                    $qb->expr()->like('a.domain', ':search'),
                    $qb->expr()->like('a.description', ':search')
                )
            )
            ->setParameter('search', '%' . $criteria['search'] . '%');
        }

        if (isset($criteria['status']) && $criteria['status']) {
            $qb->andWhere('a.status = :status')
               ->setParameter('status', $criteria['status']);
        }

        // Apply sorting
        foreach ($sortFields as $field => $direction) {
            if (property_exists(Agency::class, $field)) {
                $qb->addOrderBy('a.' . $field, $direction);
            }
        }

        // Default sorting
        if (empty($sortFields)) {
            $qb->orderBy('a.createdAt', 'DESC');
        }

        $qb->setMaxResults($limit)
           ->setFirstResult($offset);

        return $qb->getQuery()->getResult();
    }

    /**
     * Count agencies by criteria
     */
    public function countByCriteria(array $criteria): int
    {
        $qb = $this->createQueryBuilder('a')
                   ->select('COUNT(a.id)');

        // Apply search criteria
        if (isset($criteria['search']) && $criteria['search']) {
            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->like('a.name', ':search'),
                    $qb->expr()->like('a.domain', ':search'),
                    $qb->expr()->like('a.description', ':search')
                )
            )
            ->setParameter('search', '%' . $criteria['search'] . '%');
        }

        if (isset($criteria['status']) && $criteria['status']) {
            $qb->andWhere('a.status = :status')
               ->setParameter('status', $criteria['status']);
        }

        return $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * Find agency by domain
     */
    public function findByDomain(string $domain): ?Agency
    {
        return $this->findOneBy(['domain' => $domain]);
    }

    /**
     * Find active agencies
     */
    public function findActive(): array
    {
        return $this->findBy(['status' => 'active']);
    }
}
