<?php

namespace App\Repository;

use App\Entity\AuditFinding;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class AuditFindingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AuditFinding::class);
    }

    /**
     * @param array<string, mixed> $criteria
     * @param array<string, string> $sortFields
     * @return array<int, AuditFinding>
     */
    public function findByCriteria(array $criteria, array $sortFields = [], int $limit = 20, int $offset = 0): array
    {
        $qb = $this->createQueryBuilder('af');

        // Normalize snake_case keys from controllers to entity property names
        $normalizedCriteria = [];
        foreach ($criteria as $key => $value) {
            if ($value === '' || $value === null) {
                continue;
            }
            switch ($key) {
                case 'audit_run_id':
                    $normalizedCriteria['auditRunId'] = $value;
                    break;
                default:
                    $normalizedCriteria[$key] = $value;
            }
        }

        foreach ($normalizedCriteria as $field => $value) {
            $qb->andWhere("af.$field = :$field")
               ->setParameter($field, $value);
        }

        foreach ($sortFields as $field => $direction) {
            // Map snake_case to camelCase for known fields
            if ($field === 'audit_run_id') {
                $field = 'auditRunId';
            }
            $qb->addOrderBy("af.$field", strtoupper($direction) === 'DESC' ? 'DESC' : 'ASC');
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
        $qb = $this->createQueryBuilder('af')
                   ->select('COUNT(af.id)');

        $normalizedCriteria = [];
        foreach ($criteria as $key => $value) {
            if ($value === '' || $value === null) {
                continue;
            }
            switch ($key) {
                case 'audit_run_id':
                    $normalizedCriteria['auditRunId'] = $value;
                    break;
                default:
                    $normalizedCriteria[$key] = $value;
            }
        }

        foreach ($normalizedCriteria as $field => $value) {
            $qb->andWhere("af.$field = :$field")
               ->setParameter($field, $value);
        }

        return (int) $qb->getQuery()->getSingleScalarResult();
    }
}


