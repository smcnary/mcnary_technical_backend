<?php

namespace App\Repository;

use App\Entity\LeadSource;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<LeadSource>
 *
 * @method LeadSource|null find($id, $lockMode = null, $lockVersion = null)
 * @method LeadSource|null findOneBy(array $criteria, array $orderBy = null)
 * @method LeadSource[]    findAll()
 * @method LeadSource[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LeadSourceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LeadSource::class);
    }

    public function save(LeadSource $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(LeadSource $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Find active lead sources ordered by sort order
     */
    public function findActiveOrdered(): array
    {
        return $this->createQueryBuilder('ls')
            ->where('ls.status = :status')
            ->setParameter('status', 'active')
            ->orderBy('ls.sortOrder', 'ASC')
            ->addOrderBy('ls.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find lead source by name
     */
    public function findByName(string $name): ?LeadSource
    {
        return $this->createQueryBuilder('ls')
            ->where('ls.name = :name')
            ->setParameter('name', $name)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
