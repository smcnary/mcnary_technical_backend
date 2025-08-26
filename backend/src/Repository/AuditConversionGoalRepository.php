<?php

namespace App\Repository;

use App\Entity\AuditConversionGoal;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AuditConversionGoal>
 *
 * @method AuditConversionGoal|null find($id, $lockMode = null, $lockVersion = null)
 * @method AuditConversionGoal|null findOneBy(array $criteria, array $orderBy = null)
 * @method AuditConversionGoal[]    findAll()
 * @method AuditConversionGoal[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AuditConversionGoalRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AuditConversionGoal::class);
    }

    public function save(AuditConversionGoal $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(AuditConversionGoal $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
