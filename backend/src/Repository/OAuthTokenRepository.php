<?php

namespace App\Repository;

use App\Entity\OAuthToken;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<OAuthToken>
 *
 * @method OAuthToken|null find($id, $lockMode = null, $lockVersion = null)
 * @method OAuthToken|null findOneBy(array $criteria, array $orderBy = null)
 * @method OAuthToken[]    findAll()
 * @method OAuthToken[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OAuthTokenRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OAuthToken::class);
    }

    public function save(OAuthToken $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(OAuthToken $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Find active token by connection
     */
    public function findActiveByConnection($connection): ?OAuthToken
    {
        return $this->findOneBy([
            'connection' => $connection,
            'status' => 'active'
        ]);
    }

    /**
     * Find expired tokens
     */
    public function findExpired(): array
    {
        $qb = $this->createQueryBuilder('t');
        $qb->where('t.expiresAt < :now')
           ->andWhere('t.status = :status')
           ->setParameter('now', new \DateTimeImmutable())
           ->setParameter('status', 'active');

        return $qb->getQuery()->getResult();
    }

    /**
     * Find tokens that need refresh
     */
    public function findNeedingRefresh(): array
    {
        $qb = $this->createQueryBuilder('t');
        $qb->where('t.expiresAt < :threshold')
           ->andWhere('t.status = :status')
           ->setParameter('threshold', new \DateTimeImmutable('+5 minutes'))
           ->setParameter('status', 'active');

        return $qb->getQuery()->getResult();
    }
}
