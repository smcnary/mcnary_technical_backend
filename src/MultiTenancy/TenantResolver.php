<?php

namespace App\MultiTenancy;

use App\Entity\Tenant;
use App\Repository\TenantRepository;
use Symfony\Component\HttpFoundation\RequestStack;

final class TenantResolver
{
    public function __construct(
        private RequestStack $requestStack,
        private TenantRepository $tenantRepository
    ) {}

    public function getCurrentTenant(): ?Tenant
    {
        $request = $this->requestStack->getCurrentRequest();
        if (!$request) {
            return null;
        }

        $slug = $request->headers->get('X-Tenant-Slug');
        if (!$slug) {
            $host = $request->getHost();
            $slug = explode('.', $host)[0] ?? null; // naive; adjust for prod
        }
        
        return $slug ? $this->tenantRepository->findOneBy(['slug' => $slug]) : null;
    }
}
