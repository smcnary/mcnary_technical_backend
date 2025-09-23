<?php

namespace App\Repository;

use App\Entity\Notification;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Notification>
 */
class NotificationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Notification::class);
    }

    /**
     * Find notifications for a specific user
     */
    public function findByUser(User $user, bool $unreadOnly = false, int $limit = 20, int $offset = 0): array
    {
        $qb = $this->createQueryBuilder('n')
            ->where('n.user = :user')
            ->setParameter('user', $user)
            ->orderBy('n.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->setFirstResult($offset);

        if ($unreadOnly) {
            $qb->andWhere('n.isRead = :isRead')
               ->setParameter('isRead', false);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * Count notifications for a specific user
     */
    public function countByUser(User $user, bool $unreadOnly = false): int
    {
        $qb = $this->createQueryBuilder('n')
            ->select('COUNT(n.id)')
            ->where('n.user = :user')
            ->setParameter('user', $user);

        if ($unreadOnly) {
            $qb->andWhere('n.isRead = :isRead')
               ->setParameter('isRead', false);
        }

        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * Count unread notifications for a specific user
     */
    public function countUnreadByUser(User $user): int
    {
        return $this->countByUser($user, true);
    }

    /**
     * Mark all notifications as read for a specific user
     */
    public function markAllAsReadForUser(User $user): void
    {
        $this->createQueryBuilder('n')
            ->update()
            ->set('n.isRead', ':isRead')
            ->set('n.readAt', ':readAt')
            ->where('n.user = :user')
            ->andWhere('n.isRead = :wasUnread')
            ->setParameter('isRead', true)
            ->setParameter('readAt', new \DateTimeImmutable())
            ->setParameter('user', $user)
            ->setParameter('wasUnread', false)
            ->getQuery()
            ->execute();
    }

    /**
     * Find recent notifications for a user (last 7 days)
     */
    public function findRecentByUser(User $user, int $days = 7): array
    {
        $since = new \DateTimeImmutable("-{$days} days");

        return $this->createQueryBuilder('n')
            ->where('n.user = :user')
            ->andWhere('n.createdAt >= :since')
            ->setParameter('user', $user)
            ->setParameter('since', $since)
            ->orderBy('n.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find notifications by type for a user
     */
    public function findByTypeAndUser(User $user, string $type): array
    {
        return $this->createQueryBuilder('n')
            ->where('n.user = :user')
            ->andWhere('n.type = :type')
            ->setParameter('user', $user)
            ->setParameter('type', $type)
            ->orderBy('n.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
