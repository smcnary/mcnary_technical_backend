<?php

namespace App\Repository;

use App\Entity\AuditRun;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AuditRun>
 *
 * @method AuditRun|null find($id, $lockMode = null, $lockVersion = null)
 * @method AuditRun|null findOneBy(array $criteria, array $orderBy = null)
 * @method AuditRun[]    findAll()
 * @method AuditRun[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AuditRunRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AuditRun::class);
    }

    public function save(AuditRun $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(AuditRun $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
