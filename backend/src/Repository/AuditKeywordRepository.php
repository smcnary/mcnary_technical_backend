<?php

namespace App\Repository;

use App\Entity\AuditKeyword;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AuditKeyword>
 *
 * @method AuditKeyword|null find($id, $lockMode = null, $lockVersion = null)
 * @method AuditKeyword|null findOneBy(array $criteria, array $orderBy = null)
 * @method AuditKeyword[]    findAll()
 * @method AuditKeyword[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AuditKeywordRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AuditKeyword::class);
    }

    public function save(AuditKeyword $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(AuditKeyword $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
