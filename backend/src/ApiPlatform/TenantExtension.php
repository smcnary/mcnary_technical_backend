<?php

namespace App\ApiPlatform;

use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bundle\SecurityBundle\Security;

class TenantExtension implements QueryCollectionExtensionInterface, QueryItemExtensionInterface
{
    public function __construct(
        private Security $security
    ) {}

    public function applyToCollection(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, ?Operation $operation = null, array $context = []): void
    {
        $this->addTenantFilter($queryBuilder, $resourceClass);
    }

    public function applyToItem(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, array $identifiers, ?Operation $operation = null, array $context = []): void
    {
        $this->addTenantFilter($queryBuilder, $resourceClass);
    }

    private function addTenantFilter(QueryBuilder $queryBuilder, string $resourceClass): void
    {
        // Skip if no user is authenticated
        if (!$this->security->getUser()) {
            return;
        }

        // Get the user's tenant ID
        $user = $this->security->getUser();
        /** @var \App\Entity\User|null $user */
        $tenantId = method_exists($user, 'getTenantId') ? $user->getTenantId() : null;

        if (!$tenantId) {
            return;
        }

        // Add tenant filter to the query
        $rootAlias = $queryBuilder->getRootAliases()[0];
        $queryBuilder->andWhere(sprintf('%s.tenantId = :tenantId', $rootAlias));
        $queryBuilder->setParameter('tenantId', $tenantId);
    }
}
