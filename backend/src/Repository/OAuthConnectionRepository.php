<?php

namespace App\Repository;

use App\Entity\OAuthConnection;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<OAuthConnection>
 *
 * @method OAuthConnection|null find($id, $lockMode = null, $lockVersion = null)
 * @method OAuthConnection|null findOneBy(array $criteria, array $orderBy = null)
 * @method OAuthConnection[]    findAll()
 * @method OAuthConnection[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OAuthConnectionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OAuthConnection::class);
    }

    public function save(OAuthConnection $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(OAuthConnection $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Find OAuth connection by client and provider
     */
    public function findByClientAndProvider($client, string $provider): ?OAuthConnection
    {
        return $this->findOneBy([
            'client' => $client,
            'provider' => $provider
        ]);
    }

    /**
     * Find active OAuth connections for a client
     */
    public function findActiveByClient($client): array
    {
        return $this->findBy([
            'client' => $client,
            'status' => 'active'
        ]);
    }
}
