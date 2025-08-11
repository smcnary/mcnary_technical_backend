<?php

namespace App\EventSubscriber;

use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Events;
use Doctrine\Common\EventSubscriber;

class TenantFilterSubscriber implements EventSubscriber
{
    public function getSubscribedEvents(): array
    {
        return [
            Events::loadClassMetadata,
        ];
    }

    public function loadClassMetadata(LoadClassMetadataEventArgs $eventArgs): void
    {
        $metadata = $eventArgs->getClassMetadata();
        
        // Skip if the entity doesn't have a tenant_id field
        if (!$metadata->hasField('tenantId')) {
            return;
        }

        // Add tenant filter to the entity
        $metadata->addFilter('tenant_filter', 'App\Filter\TenantFilter');
    }
}
