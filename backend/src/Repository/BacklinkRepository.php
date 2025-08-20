<?php

namespace App\Repository;

use App\Entity\Backlink;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class BacklinkRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Backlink::class);
    }

    /**
     * @param array<string, mixed> $criteria
     * @param array<string, string> $sortFields
     * @return array<int, Backlink>
     */
    public function findByCriteria(array $criteria, array $sortFields = [], int $limit = 20, int $offset = 0): array
    {
        $qb = $this->createQueryBuilder('e');

        $normalizedCriteria = [];
        foreach ($criteria as $key => $value) {
            if ($value === '' || $value === null) {
                continue;
            }
            $normalizedKey = $this->snakeToCamel((string) $key);
            // Map legacy/api names to entity properties
            switch ($normalizedKey) {
                case 'qualityScore':
                    $normalizedKey = 'domainAuthority';
                    break;
                default:
                    // keep as is
            }
            $normalizedCriteria[$normalizedKey] = $value;
        }

        foreach ($normalizedCriteria as $field => $value) {
            $qb->andWhere("e.$field = :$field")
               ->setParameter($field, $value);
        }

        foreach ($sortFields as $field => $direction) {
            $field = $this->snakeToCamel($field);
            if ($field === 'qualityScore') {
                $field = 'domainAuthority';
            }
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

        $normalizedCriteria = [];
        foreach ($criteria as $key => $value) {
            if ($value === '' || $value === null) {
                continue;
            }
            $normalizedKey = $this->snakeToCamel((string) $key);
            if ($normalizedKey === 'qualityScore') {
                $normalizedKey = 'domainAuthority';
            }
            $normalizedCriteria[$normalizedKey] = $value;
        }

        foreach ($normalizedCriteria as $field => $value) {
            $qb->andWhere("e.$field = :$field")
               ->setParameter($field, $value);
        }

        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    public function findBySourceUrl(string $sourceUrl): ?Backlink
    {
        return $this->findOneBy(['sourceUrl' => $sourceUrl]);
    }

    private function snakeToCamel(string $value): string
    {
        return lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $value))));
    }
}


