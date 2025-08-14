<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\SystemUser;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Bundle\SecurityBundle\Security;

class SystemAccountService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private Security $security
    ) {}

    /**
     * Get the current system user for operations
     */
    public function getCurrentSystemUser(): ?SystemUser
    {
        $user = $this->security->getUser();
        
        if (!$user instanceof SystemUser) {
            return null;
        }

        if (!$user->isActive()) {
            throw new AccessDeniedException('System user account is inactive');
        }

        return $user;
    }

    /**
     * Check if the current user has a specific permission
     */
    public function hasPermission(string $permission): bool
    {
        $systemUser = $this->getCurrentSystemUser();
        return $systemUser && $systemUser->hasPermission($permission);
    }

    /**
     * Check if the current user can perform a specific operation
     */
    public function canPerformOperation(string $operation): bool
    {
        $systemUser = $this->getCurrentSystemUser();
        
        if (!$systemUser) {
            return false;
        }

        // Check for specific operation permissions
        $operationPermissions = [
            'create' => ['create', 'write', 'admin'],
            'read' => ['read', 'write', 'admin'],
            'update' => ['update', 'write', 'admin'],
            'delete' => ['delete', 'admin'],
            'admin' => ['admin']
        ];

        if (isset($operationPermissions[$operation])) {
            foreach ($operationPermissions[$operation] as $permission) {
                if ($systemUser->hasPermission($permission)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Create a new system user
     */
    public function createSystemUser(string $username, string $displayName, array $permissions = []): SystemUser
    {
        if (!$this->canPerformOperation('admin')) {
            throw new AccessDeniedException('Insufficient permissions to create system users');
        }

        $systemUser = new SystemUser();
        $systemUser->setUsername($username);
        $systemUser->setDisplayName($displayName);
        $systemUser->setPermissions($permissions);

        $this->entityManager->persist($systemUser);
        $this->entityManager->flush();

        return $systemUser;
    }

    /**
     * Update system user permissions
     */
    public function updateSystemUserPermissions(SystemUser $systemUser, array $permissions): void
    {
        if (!$this->canPerformOperation('admin')) {
            throw new AccessDeniedException('Insufficient permissions to update system users');
        }

        $systemUser->setPermissions($permissions);
        $this->entityManager->flush();
    }

    /**
     * Deactivate a system user
     */
    public function deactivateSystemUser(SystemUser $systemUser): void
    {
        if (!$this->canPerformOperation('admin')) {
            throw new AccessDeniedException('Insufficient permissions to deactivate system users');
        }

        $systemUser->setIsActive(false);
        $this->entityManager->flush();
    }

    /**
     * Get all active system users
     */
    public function getActiveSystemUsers(): array
    {
        if (!$this->canPerformOperation('read')) {
            throw new AccessDeniedException('Insufficient permissions to view system users');
        }

        return $this->entityManager->getRepository(SystemUser::class)
            ->findBy(['isActive' => true]);
    }

    /**
     * Log system operation
     */
    public function logOperation(string $operation, string $entity, string $entityId, array $details = []): void
    {
        $systemUser = $this->getCurrentSystemUser();
        
        if (!$systemUser) {
            return; // Skip logging if no system user context
        }

        // Here you could implement actual logging to a database or file
        // For now, we'll just ensure the operation is permitted
        if (!$this->canPerformOperation($operation)) {
            throw new AccessDeniedException("Cannot perform operation: {$operation}");
        }
    }
}
