<?php

namespace App\Repository;

use App\Entity\Lead;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Lead>
 *
 * @method Lead|null find($id, $lockMode = null, $lockVersion = null)
 * @method Lead|null findOneBy(array $criteria, array $orderBy = null)
 * @method Lead[]    findAll()
 * @method Lead[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LeadRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Lead::class);
    }

    public function save(Lead $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Lead $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @param array<string, mixed> $criteria
     * @param array<string, string> $sortFields
     * @return array<int, Lead>
     */
    public function findByCriteria(array $criteria, array $sortFields = [], int $limit = 20, int $offset = 0): array
    {
        $qb = $this->createQueryBuilder('e');

        foreach ($criteria as $key => $value) {
            if ($value === '' || $value === null) {
                continue;
            }
            $field = $this->snakeToCamel((string) $key);
            if ($field === 'clientId') {
                // Entity stores relation; use join to filter by client id
                $qb->join('e.client', 'c')
                   ->andWhere('c.id = :client_id')
                   ->setParameter('client_id', $value);
                continue;
            }
            if ($field === 'dateFrom') {
                $qb->andWhere('e.createdAt >= :date_from')
                   ->setParameter('date_from', $value);
                continue;
            }
            if ($field === 'dateTo') {
                $qb->andWhere('e.createdAt <= :date_to')
                   ->setParameter('date_to', $value);
                continue;
            }
            $qb->andWhere("e.$field = :$field")
               ->setParameter($field, $value);
        }

        foreach ($sortFields as $field => $direction) {
            $field = $this->snakeToCamel($field);
            $qb->addOrderBy("e.$field", strtoupper($direction) === 'DESC' ? 'DESC' : 'ASC');
        }

        return $qb->setMaxResults($limit)
                  ->setFirstResult($offset)
                  ->getQuery()
                  ->getResult();
    }

    /**
     * @param array<string, mixed> $criteria
     */
    public function countByCriteria(array $criteria): int
    {
        $qb = $this->createQueryBuilder('e')
                   ->select('COUNT(e.id)');

        foreach ($criteria as $key => $value) {
            if ($value === '' || $value === null) {
                continue;
            }
            $field = $this->snakeToCamel((string) $key);
            if ($field === 'clientId') {
                $qb->join('e.client', 'c')
                   ->andWhere('c.id = :client_id')
                   ->setParameter('client_id', $value);
                continue;
            }
            if ($field === 'dateFrom') {
                $qb->andWhere('e.createdAt >= :date_from')
                   ->setParameter('date_from', $value);
                continue;
            }
            if ($field === 'dateTo') {
                $qb->andWhere('e.createdAt <= :date_to')
                   ->setParameter('date_to', $value);
                continue;
            }
            $qb->andWhere("e.$field = :$field")
               ->setParameter($field, $value);
        }

        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    private function snakeToCamel(string $value): string
    {
        return lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $value))));
    }
}
