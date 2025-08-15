<?php

namespace App\Controller\Api\V1;

use App\Entity\Client;
use App\Repository\ClientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[Route('/api/v1/clients')]
class ClientController extends AbstractController
{
    public function __construct(
        private ClientRepository $clientRepository,
        private EntityManagerInterface $entityManager,
        private ValidatorInterface $validator
    ) {}

    #[Route('', name: 'api_v1_clients_list', methods: ['GET'])]
    #[IsGranted('ROLE_AGENCY_ADMIN')]
    public function listClients(Request $request): JsonResponse
    {
        $page = max(1, (int) $request->query->get('page', 1));
        $perPage = min(100, max(1, (int) $request->query->get('per_page', 20)));
        $sort = $request->query->get('sort', 'name');
        $search = $request->query->get('search', '');
        $status = $request->query->get('status', '');

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

        // Get clients with pagination and filtering
        $criteria = [];
        if ($search) {
            $criteria['search'] = $search;
        }
        if ($status) {
            $criteria['status'] = $status;
        }

        $clients = $this->clientRepository->findByCriteria($criteria, $sortFields, $perPage, ($page - 1) * $perPage);
        $totalClients = $this->clientRepository->countByCriteria($criteria);

        $clientData = [];
        foreach ($clients as $client) {
            $clientData[] = [
                'id' => $client->getId(),
                'name' => $client->getName(),
                'slug' => $client->getSlug(),
                'description' => $client->getDescription(),
                'website' => $client->getWebsite(),
                'phone' => $client->getPhone(),
                'address' => $client->getAddress(),
                'city' => $client->getCity(),
                'state' => $client->getState(),
                'zip_code' => $client->getZipCode(),
                'status' => $client->getStatus(),
                'created_at' => $client->getCreatedAt()->format('c'),
                'updated_at' => $client->getUpdatedAt()->format('c')
            ];
        }

        return $this->json([
            'data' => $clientData,
            'pagination' => [
                'page' => $page,
                'per_page' => $perPage,
                'total' => $totalClients,
                'pages' => ceil($totalClients / $perPage)
            ]
        ]);
    }

    #[Route('', name: 'api_v1_clients_create', methods: ['POST'])]
    #[IsGranted('ROLE_AGENCY_ADMIN')]
    public function createClient(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            
            if (!$data) {
                return $this->json(['error' => 'Invalid JSON'], Response::HTTP_BAD_REQUEST);
            }

            // Validate input
            $constraints = new Assert\Collection([
                'name' => [new Assert\NotBlank()],
                'slug' => [new Assert\Optional([new Assert\NotBlank()])],
                'description' => [new Assert\Optional([new Assert\NotBlank()])],
                'website' => [new Assert\Optional([new Assert\Url()])],
                'phone' => [new Assert\Optional([new Assert\NotBlank()])],
                'address' => [new Assert\Optional([new Assert\NotBlank()])],
                'city' => [new Assert\Optional([new Assert\NotBlank()])],
                'state' => [new Assert\Optional([new Assert\NotBlank()])],
                'zip_code' => [new Assert\Optional([new Assert\NotBlank()])],
                'tenant_id' => [new Assert\Optional([new Assert\Uuid()])]
            ]);

            $violations = $this->validator->validate($data, $constraints);
            if (count($violations) > 0) {
                $errors = [];
                foreach ($violations as $violation) {
                    $errors[$violation->getPropertyPath()] = $violation->getMessage();
                }
                return $this->json(['error' => 'Validation failed', 'details' => $errors], Response::HTTP_BAD_REQUEST);
            }

            // Check if client with same slug already exists
            $slug = $data['slug'] ?? $this->generateSlug($data['name']);
            $existingClient = $this->clientRepository->findBySlug($slug);
            if ($existingClient) {
                return $this->json(['error' => 'Client with this slug already exists'], Response::HTTP_CONFLICT);
            }

            // Create client
            $client = new Client();
            $client->setName($data['name']);
            $client->setSlug($slug);

            if (isset($data['description'])) {
                $client->setDescription($data['description']);
            }

            if (isset($data['website'])) {
                $client->setWebsite($data['website']);
            }

            if (isset($data['phone'])) {
                $client->setPhone($data['phone']);
            }

            if (isset($data['address'])) {
                $client->setAddress($data['address']);
            }

            if (isset($data['city'])) {
                $client->setCity($data['city']);
            }

            if (isset($data['state'])) {
                $client->setState($data['state']);
            }

            if (isset($data['zip_code'])) {
                $client->setZipCode($data['zip_code']);
            }

            if (isset($data['tenant_id'])) {
                $client->setTenantId($data['tenant_id']);
            }

            $this->entityManager->persist($client);
            $this->entityManager->flush();

            $clientData = [
                'id' => $client->getId(),
                'name' => $client->getName(),
                'slug' => $client->getSlug(),
                'description' => $client->getDescription(),
                'website' => $client->getWebsite(),
                'phone' => $client->getPhone(),
                'address' => $client->getAddress(),
                'city' => $client->getCity(),
                'state' => $client->getState(),
                'zip_code' => $client->getZipCode(),
                'status' => $client->getStatus(),
                'created_at' => $client->getCreatedAt()->format('c'),
                'updated_at' => $client->getUpdatedAt()->format('c')
            ];

            return $this->json([
                'message' => 'Client created successfully',
                'client' => $clientData
            ], Response::HTTP_CREATED);

        } catch (\Exception $e) {
            return $this->json(['error' => 'Internal server error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/{id}', name: 'api_v1_clients_get', methods: ['GET'])]
    #[IsGranted('ROLE_AGENCY_ADMIN')]
    public function getClient(string $id): JsonResponse
    {
        if (!Uuid::isValid($id)) {
            return $this->json(['error' => 'Invalid UUID'], Response::HTTP_BAD_REQUEST);
        }

        $client = $this->clientRepository->find($id);
        if (!$client) {
            return $this->json(['error' => 'Client not found'], Response::HTTP_NOT_FOUND);
        }

        $clientData = [
            'id' => $client->getId(),
            'name' => $client->getName(),
            'slug' => $client->getSlug(),
            'description' => $client->getDescription(),
            'website' => $client->getWebsite(),
            'phone' => $client->getPhone(),
            'address' => $client->getAddress(),
            'city' => $client->getCity(),
            'state' => $client->getState(),
            'zip_code' => $client->getZipCode(),
            'status' => $client->getStatus(),
            'metadata' => $client->getMetadata(),
            'google_business_profile' => $client->getGoogleBusinessProfile(),
            'google_search_console' => $client->getGoogleSearchConsole(),
            'google_analytics' => $client->getGoogleAnalytics(),
            'created_at' => $client->getCreatedAt()->format('c'),
            'updated_at' => $client->getUpdatedAt()->format('c')
        ];

        return $this->json($clientData);
    }

    #[Route('/{id}', name: 'api_v1_clients_update', methods: ['PATCH'])]
    #[IsGranted('ROLE_AGENCY_ADMIN')]
    public function updateClient(string $id, Request $request): JsonResponse
    {
        try {
            if (!Uuid::isValid($id)) {
                return $this->json(['error' => 'Invalid UUID'], Response::HTTP_BAD_REQUEST);
            }

            $client = $this->clientRepository->find($id);
            if (!$client) {
                return $this->json(['error' => 'Client not found'], Response::HTTP_NOT_FOUND);
            }

            $data = json_decode($request->getContent(), true);
            
            if (!$data) {
                return $this->json(['error' => 'Invalid JSON'], Response::HTTP_BAD_REQUEST);
            }

            // Validate input
            $constraints = new Assert\Collection([
                'name' => [new Assert\Optional([new Assert\NotBlank()])],
                'slug' => [new Assert\Optional([new Assert\NotBlank()])],
                'description' => [new Assert\Optional([new Assert\NotBlank()])],
                'website' => [new Assert\Optional([new Assert\Url()])],
                'phone' => [new Assert\Optional([new Assert\NotBlank()])],
                'address' => [new Assert\Optional([new Assert\NotBlank()])],
                'city' => [new Assert\Optional([new Assert\NotBlank()])],
                'state' => [new Assert\Optional([new Assert\NotBlank()])],
                'zip_code' => [new Assert\Optional([new Assert\NotBlank()])],
                'status' => [new Assert\Optional([new Assert\Choice(['active', 'inactive', 'archived'])])],
                'metadata' => [new Assert\Optional([new Assert\Type('array')])],
                'google_business_profile' => [new Assert\Optional([new Assert\Type('array')])],
                'google_search_console' => [new Assert\Optional([new Assert\Type('array')])],
                'google_analytics' => [new Assert\Optional([new Assert\Type('array')])]
            ]);

            $violations = $this->validator->validate($data, $constraints);
            if (count($violations) > 0) {
                $errors = [];
                foreach ($violations as $violation) {
                    $errors[$violation->getPropertyPath()] = $violation->getMessage();
                }
                return $this->json(['error' => 'Validation failed', 'details' => $errors], Response::HTTP_BAD_REQUEST);
            }

            // Update fields
            if (isset($data['name'])) {
                $client->setName($data['name']);
            }

            if (isset($data['slug'])) {
                // Check if slug is unique
                $existingClient = $this->clientRepository->findBySlug($data['slug']);
                if ($existingClient && $existingClient->getId() !== $client->getId()) {
                    return $this->json(['error' => 'Client with this slug already exists'], Response::HTTP_CONFLICT);
                }
                $client->setSlug($data['slug']);
            }

            if (isset($data['description'])) {
                $client->setDescription($data['description']);
            }

            if (isset($data['website'])) {
                $client->setWebsite($data['website']);
            }

            if (isset($data['phone'])) {
                $client->setPhone($data['phone']);
            }

            if (isset($data['address'])) {
                $client->setAddress($data['address']);
            }

            if (isset($data['city'])) {
                $client->setCity($data['city']);
            }

            if (isset($data['state'])) {
                $client->setState($data['state']);
            }

            if (isset($data['zip_code'])) {
                $client->setZipCode($data['zip_code']);
            }

            if (isset($data['status'])) {
                $client->setStatus($data['status']);
            }

            if (isset($data['metadata'])) {
                $client->setMetadata($data['metadata']);
            }

            if (isset($data['google_business_profile'])) {
                $client->setGoogleBusinessProfile($data['google_business_profile']);
            }

            if (isset($data['google_search_console'])) {
                $client->setGoogleSearchConsole($data['google_search_console']);
            }

            if (isset($data['google_analytics'])) {
                $client->setGoogleAnalytics($data['google_analytics']);
            }

            $this->entityManager->flush();

            $clientData = [
                'id' => $client->getId(),
                'name' => $client->getName(),
                'slug' => $client->getSlug(),
                'description' => $client->getDescription(),
                'website' => $client->getWebsite(),
                'phone' => $client->getPhone(),
                'address' => $client->getAddress(),
                'city' => $client->getCity(),
                'state' => $client->getState(),
                'zip_code' => $client->getZipCode(),
                'status' => $client->getStatus(),
                'metadata' => $client->getMetadata(),
                'google_business_profile' => $client->getGoogleBusinessProfile(),
                'google_search_console' => $client->getGoogleSearchConsole(),
                'google_analytics' => $client->getGoogleAnalytics(),
                'created_at' => $client->getCreatedAt()->format('c'),
                'updated_at' => $client->getUpdatedAt()->format('c')
            ];

            return $this->json([
                'message' => 'Client updated successfully',
                'client' => $clientData
            ]);

        } catch (\Exception $e) {
            return $this->json(['error' => 'Internal server error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/{id}/locations', name: 'api_v1_clients_locations_get', methods: ['GET'])]
    #[IsGranted('ROLE_AGENCY_ADMIN')]
    public function getClientLocations(string $id): JsonResponse
    {
        if (!Uuid::isValid($id)) {
            return $this->json(['error' => 'Invalid UUID'], Response::HTTP_BAD_REQUEST);
        }

        $client = $this->clientRepository->find($id);
        if (!$client) {
            return $this->json(['error' => 'Client not found'], Response::HTTP_NOT_FOUND);
        }

        // For now, return the main location. In the future, this could be expanded to multiple locations
        $location = [
            'id' => $client->getId(),
            'name' => $client->getName(),
            'address' => $client->getAddress(),
            'city' => $client->getCity(),
            'state' => $client->getState(),
            'zip_code' => $client->getZipCode(),
            'phone' => $client->getPhone(),
            'website' => $client->getWebsite()
        ];

        return $this->json([
            'data' => [$location]
        ]);
    }

    #[Route('/{id}/locations', name: 'api_v1_clients_locations_create', methods: ['POST'])]
    #[IsGranted('ROLE_AGENCY_ADMIN')]
    public function createClientLocation(string $id, Request $request): JsonResponse
    {
        // This endpoint would be used to create additional locations for a client
        // For now, return not implemented
        return $this->json(['error' => 'Multiple locations not implemented yet'], Response::HTTP_NOT_IMPLEMENTED);
    }

    private function generateSlug(string $name): string
    {
        // Convert to lowercase and replace spaces/special chars with hyphens
        $slug = strtolower(trim($name));
        $slug = preg_replace('/[^a-z0-9\s-]/', '', $slug);
        $slug = preg_replace('/[\s-]+/', '-', $slug);
        $slug = trim($slug, '-');
        
        return $slug;
    }
}
