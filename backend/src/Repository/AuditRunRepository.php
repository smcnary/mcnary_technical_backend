<?php

namespace App\Repository;

use App\Entity\AuditRun;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class AuditRunRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AuditRun::class);
    }

    public function findByCriteria(array $criteria, array $sortFields, int $limit, int $offset): array
    {
        $qb = $this->createQueryBuilder('ar');
        
        foreach ($criteria as $field => $value) {
            if ($value !== '') {
                $qb->andWhere("ar.$field = :$field")
                   ->setParameter($field, $value);
            }
        }
        
        foreach ($sortFields as $field => $direction) {
            $qb->addOrderBy("ar.$field", $direction);
        }
        
        return $qb->setMaxResults($limit)
                 ->setFirstResult($offset)
                 ->getQuery()
                 ->getResult();
    }

    public function countByCriteria(array $criteria): int
    {
        $qb = $this->createQueryBuilder('ar')
                   ->select('COUNT(ar.id)');
        
        foreach ($criteria as $field => $value) {
            if ($value !== '') {
                $qb->andWhere("ar.$field = :$field")
                   ->setParameter($field, $value);
            }
        }
        
        return $qb->getQuery()->getSingleScalarResult();
    }
}
