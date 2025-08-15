<?php

namespace App\Repository;

use App\Entity\MediaAsset;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MediaAsset>
 *
 * @method MediaAsset|null find($id, $lockMode = null, $lockVersion = null)
 * @method MediaAsset|null findOneBy(array $criteria, array $orderBy = null)
 * @method MediaAsset[]    findAll()
 * @method MediaAsset[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MediaAssetRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MediaAsset::class);
    }

    public function save(MediaAsset $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(MediaAsset $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findByCriteria(array $criteria, array $sortFields = [], int $limit = null, int $offset = null): array
    {
        $qb = $this->createQueryBuilder('m');

        // Apply filters
        if (isset($criteria['status'])) {
            $qb->andWhere('m.status = :status')
               ->setParameter('status', $criteria['status']);
        }

        if (isset($criteria['type']) && $criteria['type']) {
            $qb->andWhere('m.type = :type')
               ->setParameter('type', $criteria['type']);
        }

        if (isset($criteria['client_id']) && $criteria['client_id']) {
            $qb->andWhere('m.clientId = :client_id')
               ->setParameter('client_id', $criteria['client_id']);
        }

        if (isset($criteria['tenant_id']) && $criteria['tenant_id']) {
            $qb->andWhere('m.tenantId = :tenant_id')
               ->setParameter('tenant_id', $criteria['tenant_id']);
        }

        if (isset($criteria['mime_type']) && $criteria['mime_type']) {
            $qb->andWhere('m.mimeType = :mime_type')
               ->setParameter('mime_type', $criteria['mime_type']);
        }

        // Apply sorting
        foreach ($sortFields as $field => $direction) {
            if (property_exists(MediaAsset::class, $field)) {
                $qb->addOrderBy('m.' . $field, $direction);
            }
        }

        // Default sorting if none specified
        if (empty($sortFields)) {
            $qb->orderBy('m.createdAt', 'DESC');
        }

        // Apply pagination
        if ($limit) {
            $qb->setMaxResults($limit);
        }

        if ($offset) {
            $qb->setFirstResult($offset);
        }

        return $qb->getQuery()->getResult();
    }

    public function countByCriteria(array $criteria): int
    {
        $qb = $this->createQueryBuilder('m')
                   ->select('COUNT(m.id)');

        // Apply filters
        if (isset($criteria['status'])) {
            $qb->andWhere('m.status = :status')
               ->setParameter('status', $criteria['status']);
        }

        if (isset($criteria['type']) && $criteria['type']) {
            $qb->andWhere('m.type = :type')
               ->setParameter('type', $criteria['type']);
        }

        if (isset($criteria['client_id']) && $criteria['client_id']) {
            $qb->andWhere('m.clientId = :client_id')
               ->setParameter('client_id', $criteria['client_id']);
        }

        if (isset($criteria['tenant_id']) && $criteria['tenant_id']) {
            $qb->andWhere('m.tenantId = :tenant_id')
               ->setParameter('tenant_id', $criteria['tenant_id']);
        }

        if (isset($criteria['mime_type']) && $criteria['mime_type']) {
            $qb->andWhere('m.mimeType = :mime_type')
               ->setParameter('mime_type', $criteria['mime_type']);
        }

        return $qb->getQuery()->getSingleScalarResult();
    }

    public function findByType(string $type): array
    {
        return $this->findBy(['type' => $type, 'status' => 'active'], ['createdAt' => 'DESC']);
    }

    public function findByClientId(string $clientId): array
    {
        return $this->findBy(['clientId' => $clientId, 'status' => 'active'], ['createdAt' => 'DESC']);
    }

    public function findByTenantId(string $tenantId): array
    {
        return $this->findBy(['tenantId' => $tenantId, 'status' => 'active'], ['createdAt' => 'DESC']);
    }

    public function findImages(): array
    {
        return $this->findBy(['type' => 'image', 'status' => 'active'], ['createdAt' => 'DESC']);
    }

    public function findVideos(): array
    {
        return $this->findBy(['type' => 'video', 'status' => 'active'], ['createdAt' => 'DESC']);
    }

    public function findDocuments(): array
    {
        return $this->findBy(['type' => 'document', 'status' => 'active'], ['createdAt' => 'DESC']);
    }

    public function findAudio(): array
    {
        return $this->findBy(['type' => 'audio', 'status' => 'active'], ['createdAt' => 'DESC']);
    }
}
