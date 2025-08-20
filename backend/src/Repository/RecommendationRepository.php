<?php

namespace App\Repository;

use App\Entity\Recommendation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class RecommendationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Recommendation::class);
    }

    /**
     * @param array<string, mixed> $criteria
     * @param array<string, string> $sortFields
     * @return array<int, Recommendation>
     */
    public function findByCriteria(array $criteria, array $sortFields = [], int $limit = 20, int $offset = 0): array
    {
        $qb = $this->createQueryBuilder('e');

        foreach ($criteria as $key => $value) {
            if ($value === '' || $value === null) {
                continue;
            }
            $field = $this->snakeToCamel((string) $key);
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


