<?php

namespace App\Repository;

use App\Entity\Ranking;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class KeywordRankingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ranking::class);
    }

    /**
     * @param array<string, mixed> $criteria
     * @param array<string, string> $sortFields
     * @return array<int, Ranking>
     */
    public function findByCriteria(array $criteria, array $sortFields = [], int $limit = 20, int $offset = 0): array
    {
        $qb = $this->createQueryBuilder('e');

        foreach ($criteria as $key => $value) {
            if ($value === '' || $value === null) {
                continue;
            }
            $field = $this->snakeToCamel((string) $key);
            if (in_array($field, ['from', 'to'], true)) {
                continue; // handled below
            }
            $qb->andWhere("e.$field = :$field")
               ->setParameter($field, $value);
        }

        if (!empty($criteria['from'])) {
            $qb->andWhere('e.date >= :from')
               ->setParameter('from', new \DateTimeImmutable((string) $criteria['from']));
        }
        if (!empty($criteria['to'])) {
            $qb->andWhere('e.date <= :to')
               ->setParameter('to', new \DateTimeImmutable((string) $criteria['to']));
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
            if (in_array($field, ['from', 'to'], true)) {
                continue;
            }
            $qb->andWhere("e.$field = :$field")
               ->setParameter($field, $value);
        }

        if (!empty($criteria['from'])) {
            $qb->andWhere('e.date >= :from')
               ->setParameter('from', new \DateTimeImmutable((string) $criteria['from']));
        }
        if (!empty($criteria['to'])) {
            $qb->andWhere('e.date <= :to')
               ->setParameter('to', new \DateTimeImmutable((string) $criteria['to']));
        }

        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * @param array<string, mixed> $criteria
     * @return array<string, mixed>
     */
    public function getSummary(array $criteria): array
    {
        $qb = $this->createQueryBuilder('e')
                   ->select('COUNT(e.id) as total_keywords, SUM(e.position) as total_position, SUM(COALESCE(e.searchVolume, 0)) as total_search_volume, AVG(COALESCE(e.ctr, 0)) as avg_ctr');

        if (!empty($criteria['client_id'])) {
            $qb->andWhere('e.clientId = :client_id')->setParameter('client_id', $criteria['client_id']);
        }
        if (!empty($criteria['from'])) {
            $qb->andWhere('e.date >= :from')
               ->setParameter('from', new \DateTimeImmutable((string) $criteria['from']));
        }
        if (!empty($criteria['to'])) {
            $qb->andWhere('e.date <= :to')
               ->setParameter('to', new \DateTimeImmutable((string) $criteria['to']));
        }

        return (array) $qb->getQuery()->getSingleResult();
    }

    /**
     * @param array<string, mixed> $criteria
     * @return array<int, array<string, mixed>>
     */
    public function getTopMovers(array $criteria, int $limit = 10): array
    {
        // Simplified: order by change desc when change is available
        $qb = $this->createQueryBuilder('e')
                   ->select('e.keyword as keyword, (e.previousPosition - e.position) as position_change, e.position as current_position, e.previousPosition as previous_position')
                   ->orderBy('position_change', 'DESC')
                   ->setMaxResults($limit);

        if (!empty($criteria['client_id'])) {
            $qb->andWhere('e.clientId = :client_id')->setParameter('client_id', $criteria['client_id']);
        }
        if (!empty($criteria['from'])) {
            $qb->andWhere('e.date >= :from')
               ->setParameter('from', new \DateTimeImmutable((string) $criteria['from']));
        }
        if (!empty($criteria['to'])) {
            $qb->andWhere('e.date <= :to')
               ->setParameter('to', new \DateTimeImmutable((string) $criteria['to']));
        }

        return $qb->getQuery()->getArrayResult();
    }

    private function snakeToCamel(string $value): string
    {
        return lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $value))));
    }
}


