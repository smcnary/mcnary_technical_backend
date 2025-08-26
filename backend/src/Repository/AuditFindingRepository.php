<?php

namespace App\Repository;

use App\Entity\AuditFinding;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AuditFinding>
 *
 * @method AuditFinding|null find($id, $lockMode = null, $lockVersion = null)
 * @method AuditFinding|null findOneBy(array $criteria, array $orderBy = null)
 * @method AuditFinding[]    findAll()
 * @method AuditFinding[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AuditFindingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AuditFinding::class);
    }

    public function save(AuditFinding $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(AuditFinding $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}


