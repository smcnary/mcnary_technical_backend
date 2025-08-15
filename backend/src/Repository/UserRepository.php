<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @extends ServiceEntityRepository<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface, UserProviderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function save(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPasswordHash($newHashedPassword);
        $this->save($user, true);
    }

    /**
     * Loads the user for the given user identifier (email).
     */
    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        $user = $this->findOneBy(['email' => $identifier]);
        
        if (!$user) {
            throw new UnsupportedUserException(sprintf('User with email "%s" not found.', $identifier));
        }

        return $user;
    }

    /**
     * Refreshes the user.
     */
    public function refreshUser(UserInterface $user): UserInterface
    {
        return $this->loadUserByIdentifier($user->getUserIdentifier());
    }

    /**
     * Whether this provider supports the given user class.
     */
    public function supportsClass(string $class): bool
    {
        return User::class === $class || is_subclass_of($class, User::class);
    }

    public function findByCriteria(array $criteria, array $sortFields = [], int $limit = null, int $offset = null): array
    {
        $qb = $this->createQueryBuilder('u');

        // Apply filters
        if (isset($criteria['search']) && $criteria['search']) {
            $qb->andWhere('u.name LIKE :search OR u.email LIKE :search')
               ->setParameter('search', '%' . $criteria['search'] . '%');
        }

        if (isset($criteria['role']) && $criteria['role']) {
            $qb->andWhere('u.roles LIKE :role')
               ->setParameter('role', '%' . $criteria['role'] . '%');
        }

        if (isset($criteria['status']) && $criteria['status']) {
            $qb->andWhere('u.status = :status')
               ->setParameter('status', $criteria['status']);
        }

        if (isset($criteria['client_id']) && $criteria['client_id']) {
            $qb->andWhere('u.clientId = :client_id')
               ->setParameter('client_id', $criteria['client_id']);
        }

        if (isset($criteria['tenant_id']) && $criteria['tenant_id']) {
            $qb->andWhere('u.tenantId = :tenant_id')
               ->setParameter('tenant_id', $criteria['tenant_id']);
        }

        // Apply sorting
        foreach ($sortFields as $field => $direction) {
            if (property_exists(User::class, $field)) {
                $qb->addOrderBy('u.' . $field, $direction);
            }
        }

        // Default sorting if none specified
        if (empty($sortFields)) {
            $qb->orderBy('u.createdAt', 'DESC');
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
        $qb = $this->createQueryBuilder('u')
                   ->select('COUNT(u.id)');

        // Apply filters
        if (isset($criteria['search']) && $criteria['search']) {
            $qb->andWhere('u.name LIKE :search OR u.email LIKE :search')
               ->setParameter('search', '%' . $criteria['search'] . '%');
        }

        if (isset($criteria['role']) && $criteria['role']) {
            $qb->andWhere('u.roles LIKE :role')
               ->setParameter('role', '%' . $criteria['role'] . '%');
        }

        if (isset($criteria['status']) && $criteria['status']) {
            $qb->andWhere('u.status = :status')
               ->setParameter('status', $criteria['status']);
        }

        if (isset($criteria['client_id']) && $criteria['client_id']) {
            $qb->andWhere('u.clientId = :client_id')
               ->setParameter('client_id', $criteria['client_id']);
        }

        if (isset($criteria['tenant_id']) && $criteria['tenant_id']) {
            $qb->andWhere('u.tenantId = :tenant_id')
               ->setParameter('tenant_id', $criteria['tenant_id']);
        }

        return $qb->getQuery()->getSingleScalarResult();
    }
}
