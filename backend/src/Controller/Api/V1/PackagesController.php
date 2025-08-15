<?php

namespace App\Controller\Api\V1;

use App\Entity\Package;
use App\Repository\PackageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/v1/packages')]
class PackagesController extends AbstractController
{
    public function __construct(
        private PackageRepository $packageRepository
    ) {}

    #[Route('', name: 'api_v1_packages_list', methods: ['GET'])]
    public function listPackages(Request $request): JsonResponse
    {
        $page = max(1, (int) $request->query->get('page', 1));
        $perPage = min(100, max(1, (int) $request->query->get('per_page', 20)));
        $sort = $request->query->get('sort', 'sort_order');
        $clientId = $request->query->get('client_id', '');
        $isPopular = $request->query->get('popular', '');
        $billingCycle = $request->query->get('billing_cycle', '');

        // Parse sort parameter
        $sortFields = [];
        foreach (explode(',', $sort) as $field) {
            $direction = 'ASC';
            if (str_starts_with($field, '-')) {
                $direction = 'DESC';
                $field = substr($field, 1);
            }
            $sortFields[$field] = $direction;
        }

        // Build criteria
        $criteria = ['is_active' => true];
        if ($clientId) {
            $criteria['client_id'] = $clientId;
        }
        if ($isPopular !== '') {
            $criteria['is_popular'] = $isPopular === 'true';
        }
        if ($billingCycle) {
            $criteria['billing_cycle'] = $billingCycle;
        }

        // Get packages with pagination and filtering
        $packages = $this->packageRepository->findByCriteria($criteria, $sortFields, $perPage, ($page - 1) * $perPage);
        $totalPackages = $this->packageRepository->countByCriteria($criteria);

        $packageData = [];
        foreach ($packages as $package) {
            $packageData[] = [
                'id' => $package->getId(),
                'name' => $package->getName(),
                'slug' => $package->getSlug(),
                'description' => $package->getDescription(),
                'price' => $package->getPrice(),
                'billing_cycle' => $package->getBillingCycle(),
                'features' => $package->getFeatures(),
                'included_services' => $package->getIncludedServices(),
                'is_popular' => $package->isPopular(),
                'is_active' => $package->isActive(),
                'sort_order' => $package->getSortOrder(),
                'created_at' => $package->getCreatedAt()->format('c'),
                'updated_at' => $package->getUpdatedAt()->format('c')
            ];
        }

        return $this->json([
            'data' => $packageData,
            'pagination' => [
                'page' => $page,
                'per_page' => $perPage,
                'total' => $totalPackages,
                'pages' => ceil($totalPackages / $perPage)
            ]
        ]);
    }

    #[Route('/{id}', name: 'api_v1_packages_get', methods: ['GET'])]
    public function getPackage(string $id): JsonResponse
    {
        $package = $this->packageRepository->find($id);
        if (!$package) {
            return $this->json(['error' => 'Package not found'], Response::HTTP_NOT_FOUND);
        }

        // Check if package is active
        if (!$package->isActive()) {
            return $this->json(['error' => 'Package not found'], Response::HTTP_NOT_FOUND);
        }

        $packageData = [
            'id' => $package->getId(),
            'name' => $package->getName(),
            'slug' => $package->getSlug(),
            'description' => $package->getDescription(),
            'price' => $package->getPrice(),
            'billing_cycle' => $package->getBillingCycle(),
            'features' => $package->getFeatures(),
            'included_services' => $package->getIncludedServices(),
            'is_popular' => $package->isPopular(),
            'is_active' => $package->isActive(),
            'sort_order' => $package->getSortOrder(),
            'metadata' => $package->getMetadata(),
            'created_at' => $package->getCreatedAt()->format('c'),
            'updated_at' => $package->getUpdatedAt()->format('c')
        ];

        return $this->json($packageData);
    }
}
