<?php

namespace App\Repository;

use App\Entity\AuditCompetitor;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AuditCompetitor>
 *
 * @method AuditCompetitor|null find($id, $lockMode = null, $lockVersion = null)
 * @method AuditCompetitor|null findOneBy(array $criteria, array $orderBy = null)
 * @method AuditCompetitor[]    findAll()
 * @method AuditCompetitor[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AuditCompetitorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AuditCompetitor::class);
    }

    public function save(AuditCompetitor $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(AuditCompetitor $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
