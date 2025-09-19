<?php

namespace App\Controller\Api\V1;

use App\Entity\Agency;
use App\Entity\Client;
use App\Entity\Organization;
use App\Entity\Tenant;
use App\Entity\User;
use App\Repository\ClientRepository;
use App\Repository\OrganizationRepository;
use App\Repository\TenantRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;
use App\Validator\Constraints\DomainOrUrl;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\Exception\ORMException;

#[Route('/api/v1/clients')]
class ClientController extends AbstractController
{
    public function __construct(
        private ClientRepository $clientRepository,
        private OrganizationRepository $organizationRepository,
        private TenantRepository $tenantRepository,
        private UserRepository $userRepository,
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher,
        private JWTTokenManagerInterface $jwtManager,
        private ValidatorInterface $validator,
        private LoggerInterface $logger
    ) {}

    /**
     * Log error with context and return error response
     */
    private function logAndReturnError(
        string $message,
        int $statusCode,
        array $context = [],
        ?\Throwable $exception = null
    ): JsonResponse {
        $currentUser = $this->getUser();
        $userId = $currentUser instanceof User ? $currentUser->getId() : null;
        
        $logContext = array_merge($context, [
            'status_code' => $statusCode,
            'user_id' => $userId,
            'timestamp' => (new \DateTimeImmutable())->format('c')
        ]);

        if ($exception) {
            $logContext['exception'] = [
                'message' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => $exception->getTraceAsString()
            ];
            $this->logger->error($message, $logContext);
        } else {
            $this->logger->warning($message, $logContext);
        }

        return $this->json([
            'error' => $message,
            'status_code' => $statusCode,
            'timestamp' => $logContext['timestamp']
        ], $statusCode);
    }

    /**
     * Log successful operation
     */
    private function logSuccess(string $operation, array $context = []): void
    {
        $currentUser = $this->getUser();
        $userId = $currentUser instanceof User ? $currentUser->getId() : null;
        
        $this->logger->info($operation, array_merge($context, [
            'user_id' => $userId,
            'timestamp' => (new \DateTimeImmutable())->format('c')
        ]));
    }

    /**
     * Validate UUID and return client or throw exception
     */
    private function validateAndGetClient(string $id): Client
    {
        if (!Uuid::isValid($id)) {
            $this->logger->warning('Invalid UUID provided', ['uuid' => $id]);
            throw new BadRequestHttpException('Invalid UUID format');
        }

        $client = $this->clientRepository->find($id);
        if (!$client) {
            $this->logger->warning('Client not found', ['uuid' => $id]);
            throw new NotFoundHttpException('Client not found');
        }

        return $client;
    }

    /**
     * Validate JSON request data
     */
    private function validateJsonRequest(Request $request): array
    {
        $data = json_decode($request->getContent(), true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->logger->warning('Invalid JSON in request', [
                'json_error' => json_last_error_msg(),
                'content' => substr($request->getContent(), 0, 1000)
            ]);
            throw new BadRequestHttpException('Invalid JSON format');
        }

        if (!$data) {
            throw new BadRequestHttpException('Request body cannot be empty');
        }

        return $data;
    }

    /**
     * Handle validation violations
     */
    private function handleValidationViolations($violations): JsonResponse
    {
        $errors = [];
        foreach ($violations as $violation) {
            $errors[$violation->getPropertyPath()] = $violation->getMessage();
        }

        $this->logger->warning('Validation failed', ['errors' => $errors]);
        
        return $this->json([
            'error' => 'Validation failed',
            'details' => $errors,
            'status_code' => Response::HTTP_BAD_REQUEST
        ], Response::HTTP_BAD_REQUEST);
    }

    #[Route('', name: 'api_v1_clients_list', methods: ['GET'])]
    #[IsGranted('ROLE_AGENCY_ADMIN')]
    public function listClients(Request $request): JsonResponse
    {
        try {
            $this->logger->info('Client list requested', [
                'query_params' => $request->query->all(),
                'user_agent' => $request->headers->get('User-Agent')
            ]);

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

            $this->logSuccess('Clients retrieved successfully', [
                'total_clients' => $totalClients,
                'page' => $page,
                'per_page' => $perPage
            ]);

            return $this->json([
                'data' => $clientData,
                'pagination' => [
                    'page' => $page,
                    'per_page' => $perPage,
                    'total' => $totalClients,
                    'pages' => ceil($totalClients / $perPage)
                ]
            ]);

        } catch (\Exception $e) {
            $this->logger->error('Error retrieving clients list', [
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return $this->logAndReturnError(
                'Failed to retrieve clients list',
                Response::HTTP_INTERNAL_SERVER_ERROR,
                ['operation' => 'list_clients'],
                $e
            );
        }
    }

    #[Route('', name: 'api_v1_clients_create', methods: ['POST'])]
    #[IsGranted('ROLE_AGENCY_ADMIN')]
    public function createClient(Request $request): JsonResponse
    {
        try {
            $this->logger->info('Client creation requested', [
                'user_agent' => $request->headers->get('User-Agent'),
                'content_length' => $request->headers->get('Content-Length')
            ]);

            $data = $this->validateJsonRequest($request);

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
                return $this->handleValidationViolations($violations);
            }

            // Check if client with same slug already exists
            $slug = $data['slug'] ?? $this->generateSlug($data['name']);
            $existingClient = $this->clientRepository->findBySlug($slug);
            if ($existingClient) {
                $this->logger->warning('Client creation failed - slug already exists', ['slug' => $slug]);
                return $this->json([
                    'error' => 'Client with this slug already exists',
                    'status_code' => Response::HTTP_CONFLICT
                ], Response::HTTP_CONFLICT);
            }

            // Get current user's organization
            $currentUser = $this->getUser();
            if (!$currentUser) {
                $this->logger->error('Client creation failed - user not authenticated');
                return $this->json([
                    'error' => 'User not authenticated',
                    'status_code' => Response::HTTP_UNAUTHORIZED
                ], Response::HTTP_UNAUTHORIZED);
            }

            // Ensure we have a User entity
            if (!$currentUser instanceof User) {
                $this->logger->error('Client creation failed - invalid user type', [
                    'user_type' => get_class($currentUser)
                ]);
                return $this->json([
                    'error' => 'Invalid user type',
                    'status_code' => Response::HTTP_INTERNAL_SERVER_ERROR
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            // Create client
            $client = new Client($currentUser->getAgency(), $data['name']);
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

            // Note: Client is automatically associated with the organization
            // Tenant relationship is handled through the organization

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

            $this->logSuccess('Client created successfully', [
                'client_id' => $client->getId(),
                'client_name' => $client->getName(),
                'client_slug' => $client->getSlug()
            ]);

            return $this->json([
                'message' => 'Client created successfully',
                'client' => $clientData
            ], Response::HTTP_CREATED);

        } catch (BadRequestHttpException $e) {
            return $this->logAndReturnError(
                $e->getMessage(),
                Response::HTTP_BAD_REQUEST,
                ['operation' => 'create_client']
            );
        } catch (UniqueConstraintViolationException $e) {
            $this->logger->error('Database constraint violation during client creation', [
                'exception' => $e->getMessage()
            ]);
            return $this->logAndReturnError(
                'Client with this information already exists',
                Response::HTTP_CONFLICT,
                ['operation' => 'create_client'],
                $e
            );
        } catch (ORMException $e) {
            $this->logger->error('Database error during client creation', [
                'exception' => $e->getMessage()
            ]);
            return $this->logAndReturnError(
                'Database error occurred',
                Response::HTTP_INTERNAL_SERVER_ERROR,
                ['operation' => 'create_client'],
                $e
            );
        } catch (\Exception $e) {
            return $this->logAndReturnError(
                'Internal server error occurred',
                Response::HTTP_INTERNAL_SERVER_ERROR,
                ['operation' => 'create_client'],
                $e
            );
        }
    }

    #[Route('/{id}', name: 'api_v1_clients_get', methods: ['GET'])]
    #[IsGranted('ROLE_AGENCY_ADMIN')]
    public function getClient(string $id): JsonResponse
    {
        try {
            $this->logger->info('Client retrieval requested', ['client_id' => $id]);

            $client = $this->validateAndGetClient($id);

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

            $this->logSuccess('Client retrieved successfully', [
                'client_id' => $client->getId(),
                'client_name' => $client->getName()
            ]);

            return $this->json($clientData);

        } catch (BadRequestHttpException $e) {
            return $this->logAndReturnError(
                $e->getMessage(),
                Response::HTTP_BAD_REQUEST,
                ['operation' => 'get_client', 'client_id' => $id]
            );
        } catch (NotFoundHttpException $e) {
            return $this->logAndReturnError(
                $e->getMessage(),
                Response::HTTP_NOT_FOUND,
                ['operation' => 'get_client', 'client_id' => $id]
            );
        } catch (\Exception $e) {
            return $this->logAndReturnError(
                'Failed to retrieve client',
                Response::HTTP_INTERNAL_SERVER_ERROR,
                ['operation' => 'get_client', 'client_id' => $id],
                $e
            );
        }
    }

    #[Route('/{id}', name: 'api_v1_clients_update', methods: ['PATCH'])]
    #[IsGranted('ROLE_AGENCY_ADMIN')]
    public function updateClient(string $id, Request $request): JsonResponse
    {
        try {
            $this->logger->info('Client update requested', [
                'client_id' => $id,
                'user_agent' => $request->headers->get('User-Agent'),
                'content_length' => $request->headers->get('Content-Length')
            ]);

            $client = $this->validateAndGetClient($id);
            $data = $this->validateJsonRequest($request);

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
                return $this->handleValidationViolations($violations);
            }

            // Update fields
            if (isset($data['name'])) {
                $client->setName($data['name']);
            }

            if (isset($data['slug'])) {
                // Check if slug is unique
                $existingClient = $this->clientRepository->findBySlug($data['slug']);
                if ($existingClient && $existingClient->getId() !== $client->getId()) {
                    $this->logger->warning('Client update failed - slug already exists', [
                        'slug' => $data['slug'],
                        'client_id' => $id
                    ]);
                    return $this->json([
                        'error' => 'Client with this slug already exists',
                        'status_code' => Response::HTTP_CONFLICT
                    ], Response::HTTP_CONFLICT);
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

            $this->logSuccess('Client updated successfully', [
                'client_id' => $client->getId(),
                'client_name' => $client->getName(),
                'updated_fields' => array_keys($data)
            ]);

            return $this->json([
                'message' => 'Client updated successfully',
                'client' => $clientData
            ]);

        } catch (BadRequestHttpException $e) {
            return $this->logAndReturnError(
                $e->getMessage(),
                Response::HTTP_BAD_REQUEST,
                ['operation' => 'update_client', 'client_id' => $id]
            );
        } catch (NotFoundHttpException $e) {
            return $this->logAndReturnError(
                $e->getMessage(),
                Response::HTTP_NOT_FOUND,
                ['operation' => 'update_client', 'client_id' => $id]
            );
        } catch (UniqueConstraintViolationException $e) {
            $this->logger->error('Database constraint violation during client update', [
                'exception' => $e->getMessage(),
                'client_id' => $id
            ]);
            return $this->logAndReturnError(
                'Client with this information already exists',
                Response::HTTP_CONFLICT,
                ['operation' => 'update_client', 'client_id' => $id],
                $e
            );
        } catch (ORMException $e) {
            $this->logger->error('Database error during client update', [
                'exception' => $e->getMessage(),
                'client_id' => $id
            ]);
            return $this->logAndReturnError(
                'Database error occurred',
                Response::HTTP_INTERNAL_SERVER_ERROR,
                ['operation' => 'update_client', 'client_id' => $id],
                $e
            );
        } catch (\Exception $e) {
            return $this->logAndReturnError(
                'Failed to update client',
                Response::HTTP_INTERNAL_SERVER_ERROR,
                ['operation' => 'update_client', 'client_id' => $id],
                $e
            );
        }
    }

    #[Route('/{id}/locations', name: 'api_v1_clients_locations_get', methods: ['GET'])]
    #[IsGranted('ROLE_AGENCY_ADMIN')]
    public function getClientLocations(string $id): JsonResponse
    {
        try {
            $this->logger->info('Client locations requested', ['client_id' => $id]);

            $client = $this->validateAndGetClient($id);

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

            $this->logSuccess('Client locations retrieved successfully', [
                'client_id' => $client->getId(),
                'client_name' => $client->getName()
            ]);

            return $this->json([
                'data' => [$location]
            ]);

        } catch (BadRequestHttpException $e) {
            return $this->logAndReturnError(
                $e->getMessage(),
                Response::HTTP_BAD_REQUEST,
                ['operation' => 'get_client_locations', 'client_id' => $id]
            );
        } catch (NotFoundHttpException $e) {
            return $this->logAndReturnError(
                $e->getMessage(),
                Response::HTTP_NOT_FOUND,
                ['operation' => 'get_client_locations', 'client_id' => $id]
            );
        } catch (\Exception $e) {
            return $this->logAndReturnError(
                'Failed to retrieve client locations',
                Response::HTTP_INTERNAL_SERVER_ERROR,
                ['operation' => 'get_client_locations', 'client_id' => $id],
                $e
            );
        }
    }

    #[Route('/{id}/locations', name: 'api_v1_clients_locations_create', methods: ['POST'])]
    #[IsGranted('ROLE_AGENCY_ADMIN')]
    public function createClientLocation(string $id, Request $request): JsonResponse
    {
        try {
            $this->logger->info('Client location creation requested', [
                'client_id' => $id,
                'user_agent' => $request->headers->get('User-Agent')
            ]);

            // Validate client exists
            $client = $this->validateAndGetClient($id);

            // This endpoint would be used to create additional locations for a client
            // For now, return not implemented with proper logging
            $this->logger->info('Client location creation not implemented yet', [
                'client_id' => $id,
                'client_name' => $client->getName()
            ]);

            return $this->json([
                'error' => 'Multiple locations not implemented yet',
                'status_code' => Response::HTTP_NOT_IMPLEMENTED,
                'message' => 'This feature is planned for future development'
            ], Response::HTTP_NOT_IMPLEMENTED);

        } catch (BadRequestHttpException $e) {
            return $this->logAndReturnError(
                $e->getMessage(),
                Response::HTTP_BAD_REQUEST,
                ['operation' => 'create_client_location', 'client_id' => $id]
            );
        } catch (NotFoundHttpException $e) {
            return $this->logAndReturnError(
                $e->getMessage(),
                Response::HTTP_NOT_FOUND,
                ['operation' => 'create_client_location', 'client_id' => $id]
            );
        } catch (\Exception $e) {
            return $this->logAndReturnError(
                'Failed to process client location creation request',
                Response::HTTP_INTERNAL_SERVER_ERROR,
                ['operation' => 'create_client_location', 'client_id' => $id],
                $e
            );
        }
    }

    #[Route('/login', name: 'api_v1_clients_login', methods: ['POST'])]
    public function clientLogin(Request $request): JsonResponse
    {
        try {
            $this->logger->info('Client login attempt', [
                'user_agent' => $request->headers->get('User-Agent'),
                'ip_address' => $request->getClientIp()
            ]);

            $data = $this->validateJsonRequest($request);

            // Validate input
            $constraints = new Assert\Collection([
                'email' => [new Assert\NotBlank(), new Assert\Email()],
                'password' => [new Assert\NotBlank()]
            ]);

            $violations = $this->validator->validate($data, $constraints);
            if (count($violations) > 0) {
                return $this->handleValidationViolations($violations);
            }

            $email = $data['email'];
            $password = $data['password'];

            // Find user
            $user = $this->userRepository->findOneBy(['email' => $email]);
            if (!$user) {
                $this->logger->warning('Client login failed - user not found', ['email' => $email]);
                return $this->json([
                    'error' => 'Invalid credentials',
                    'status_code' => Response::HTTP_UNAUTHORIZED
                ], Response::HTTP_UNAUTHORIZED);
            }

            // Check password
            if (!$this->passwordHasher->isPasswordValid($user, $password)) {
                $this->logger->warning('Client login failed - invalid password', ['email' => $email]);
                return $this->json([
                    'error' => 'Invalid credentials',
                    'status_code' => Response::HTTP_UNAUTHORIZED
                ], Response::HTTP_UNAUTHORIZED);
            }

            // Verify this is a client user (not agency user)
            if (!$user->isClientUser()) {
                $this->logger->warning('Client login failed - user is not a client user', [
                    'email' => $email,
                    'user_role' => $user->getRole()
                ]);
                return $this->json([
                    'error' => 'Access denied. This endpoint is for client users only.',
                    'status_code' => Response::HTTP_FORBIDDEN
                ], Response::HTTP_FORBIDDEN);
            }

            // Check if user can log in
            if (!in_array($user->getStatus(), ['invited', 'active'])) {
                $this->logger->warning('Client login failed - account not active', [
                    'email' => $email,
                    'user_status' => $user->getStatus()
                ]);
                return $this->json([
                    'error' => 'Account is not active',
                    'status_code' => Response::HTTP_FORBIDDEN
                ], Response::HTTP_FORBIDDEN);
            }

            // Get client information
            $client = null;
            if ($user->getClientId()) {
                $client = $this->clientRepository->find($user->getClientId());
            }

            // Generate JWT token
            $token = $this->jwtManager->create($user);

            // Update last login
            $user->setLastLoginAt(new \DateTimeImmutable());
            $this->userRepository->save($user, true);

            // Return user data with client information
            $userData = [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'name' => $user->getName(),
                'first_name' => $user->getFirstName(),
                'last_name' => $user->getLastName(),
                'role' => $user->getRole(),
                'status' => $user->getStatus(),
                'client_id' => $user->getClientId(),
                'agency_id' => $user->getAgency()?->getId(),
                'created_at' => $user->getCreatedAt()->format('c'),
                'last_login_at' => $user->getLastLoginAt()?->format('c')
            ];

            $response = [
                'token' => $token,
                'user' => $userData
            ];

            // Add client information if available
            if ($client) {
                $response['client'] = [
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
                    'country' => $client->getCountry(),
                    'industry' => $client->getIndustry(),
                    'status' => $client->getStatus()
                ];
            }

            $this->logSuccess('Client login successful', [
                'user_id' => $user->getId(),
                'email' => $user->getEmail(),
                'client_id' => $user->getClientId()
            ]);

            return $this->json($response);

        } catch (BadRequestHttpException $e) {
            return $this->logAndReturnError(
                $e->getMessage(),
                Response::HTTP_BAD_REQUEST,
                ['operation' => 'client_login']
            );
        } catch (\Exception $e) {
            return $this->logAndReturnError(
                'Login failed due to internal server error',
                Response::HTTP_INTERNAL_SERVER_ERROR,
                ['operation' => 'client_login'],
                $e
            );
        }
    }

    #[Route('/register', name: 'api_v1_clients_register', methods: ['POST', 'OPTIONS'])]
    public function registerClient(Request $request): JsonResponse
    {
        // Handle CORS preflight requests
        if ($request->getMethod() === 'OPTIONS') {
            $response = new JsonResponse();
            $response->headers->set('Access-Control-Allow-Origin', 'http://localhost:3000');
            $response->headers->set('Access-Control-Allow-Methods', 'POST, OPTIONS');
            $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With');
            $response->headers->set('Access-Control-Max-Age', '3600');
            return $response;
        }

        try {
            $this->logger->info('Client registration requested', [
                'user_agent' => $request->headers->get('User-Agent'),
                'ip_address' => $request->getClientIp(),
                'content_length' => $request->headers->get('Content-Length')
            ]);

            $data = $this->validateJsonRequest($request);

            // Validate input
            $constraints = new Assert\Collection([
                'organization_name' => [new Assert\NotBlank()],
                'organization_domain' => [new Assert\Optional([new DomainOrUrl()])],
                'client_name' => [new Assert\NotBlank()],
                'client_slug' => [new Assert\Optional([new Assert\NotBlank()])],
                'client_description' => [new Assert\Optional([new Assert\NotBlank()])],
                'client_website' => [new Assert\Optional([new DomainOrUrl()])],
                'client_phone' => [new Assert\Optional([new Assert\NotBlank()])],
                'client_address' => [new Assert\Optional([new Assert\NotBlank()])],
                'client_city' => [new Assert\Optional([new Assert\NotBlank()])],
                'client_state' => [new Assert\Optional([new Assert\NotBlank()])],
                'client_zip_code' => [new Assert\Optional([new Assert\NotBlank()])],
                'client_country' => [new Assert\Optional([new Assert\NotBlank()])],
                'client_industry' => [new Assert\Optional([new Assert\Choice(choices: ['law', 'healthcare', 'real_estate', 'finance', 'other'])])],
                'admin_email' => [new Assert\NotBlank(), new Assert\Email()],
                'admin_password' => [new Assert\NotBlank(), new Assert\Length(min: 8)],
                'admin_first_name' => [new Assert\Optional([new Assert\NotBlank()])],
                'admin_last_name' => [new Assert\Optional([new Assert\NotBlank()])],
                'tenant_name' => [new Assert\Optional([new Assert\NotBlank()])],
                'tenant_slug' => [new Assert\Optional([new Assert\NotBlank()])]
            ]);

            $violations = $this->validator->validate($data, $constraints);
            if (count($violations) > 0) {
                return $this->handleValidationViolations($violations);
            }

            // Check if organization with same domain already exists
            if (isset($data['organization_domain'])) {
                $existingOrg = $this->organizationRepository->findByDomain($data['organization_domain']);
                if ($existingOrg) {
                    $this->logger->warning('Client registration failed - organization domain already exists', [
                        'domain' => $data['organization_domain']
                    ]);
                    return $this->json([
                        'error' => 'Organization with this domain already exists',
                        'status_code' => Response::HTTP_CONFLICT
                    ], Response::HTTP_CONFLICT);
                }
            }

            // Check if client with same slug already exists
            $clientSlug = $data['client_slug'] ?? $this->generateSlug($data['client_name']);
            $existingClient = $this->clientRepository->findBySlug($clientSlug);
            if ($existingClient) {
                $this->logger->warning('Client registration failed - client slug already exists', [
                    'slug' => $clientSlug
                ]);
                return $this->json([
                    'error' => 'Client with this slug already exists',
                    'status_code' => Response::HTTP_CONFLICT
                ], Response::HTTP_CONFLICT);
            }

            // Check if user with same email already exists
            $existingUser = $this->userRepository->findOneBy(['email' => $data['admin_email']]);
            if ($existingUser) {
                $this->logger->warning('Client registration failed - user email already exists', [
                    'email' => $data['admin_email']
                ]);
                return $this->json([
                    'error' => 'User with this email already exists',
                    'status_code' => Response::HTTP_CONFLICT
                ], Response::HTTP_CONFLICT);
            }

            // Create agency
            $agency = new Agency($data['organization_name']);
            if (isset($data['organization_domain'])) {
                $agency->setDomain($data['organization_domain']);
            }
            $this->entityManager->persist($agency);

            // Create tenant
            $tenantName = $data['tenant_name'] ?? $data['organization_name'];
            $tenantSlug = $data['tenant_slug'] ?? $this->generateSlug($tenantName);
            $tenant = new Tenant();
            $tenant->setName($tenantName);
            $tenant->setSlug($tenantSlug);
            $this->tenantRepository->save($tenant);

            // Create client
            $client = new Client($agency, $data['client_name']);
            $client->setSlug($clientSlug);

            if (isset($data['client_description'])) {
                $client->setDescription($data['client_description']);
            }

            if (isset($data['client_website'])) {
                $client->setWebsiteUrl($data['client_website']);
            }

            if (isset($data['client_phone'])) {
                $client->setPhone($data['client_phone']);
            }

            if (isset($data['client_address'])) {
                $client->setAddress($data['client_address']);
            }

            if (isset($data['client_city'])) {
                $client->setCity($data['client_city']);
            }

            if (isset($data['client_state'])) {
                $client->setState($data['client_state']);
            }

            if (isset($data['client_zip_code'])) {
                $client->setPostalCode($data['client_zip_code']);
            }

            if (isset($data['client_country'])) {
                $client->setCountry($data['client_country']);
            }

            if (isset($data['client_industry'])) {
                $client->setIndustry($data['client_industry']);
            }

            $this->entityManager->persist($client);

            // Get the default organization
            $organization = $this->entityManager->getRepository(\App\Entity\Organization::class)->findOneBy([]);
            if (!$organization) {
                throw new \RuntimeException('No organization found. Please create an organization first.');
            }

            // Create admin user
            $hashedPassword = $this->passwordHasher->hashPassword(
                new User($organization, $data['admin_email'], '', User::ROLE_CLIENT_USER),
                $data['admin_password']
            );

            $adminUser = new User($organization, $data['admin_email'], $hashedPassword, User::ROLE_CLIENT_USER);
            $adminUser->setAgency($agency);
            $adminUser->setClientId($client->getId());
            $adminUser->setStatus('active');

            if (isset($data['admin_first_name'])) {
                $adminUser->setFirstName($data['admin_first_name']);
            }

            if (isset($data['admin_last_name'])) {
                $adminUser->setLastName($data['admin_last_name']);
            }

            $this->userRepository->save($adminUser);

            // Flush all changes
            $this->entityManager->flush();

            // Return response
            $responseData = [
                'message' => 'Client registration successful',
                'agency' => [
                    'id' => $agency->getId(),
                    'name' => $agency->getName(),
                    'domain' => $agency->getDomain()
                ],
                'tenant' => [
                    'id' => $tenant->getId(),
                    'name' => $tenant->getName(),
                    'slug' => $tenant->getSlug()
                ],
                'client' => [
                    'id' => $client->getId(),
                    'name' => $client->getName(),
                    'slug' => $client->getSlug(),
                    'status' => $client->getStatus()
                ],
                'admin_user' => [
                    'id' => $adminUser->getId(),
                    'email' => $adminUser->getEmail(),
                    'role' => $adminUser->getRole(),
                    'status' => $adminUser->getStatus()
                ]
            ];

            $this->logSuccess('Client registration successful', [
                'agency_id' => $agency->getId(),
                'tenant_id' => $tenant->getId(),
                'client_id' => $client->getId(),
                'admin_user_id' => $adminUser->getId(),
                'admin_email' => $adminUser->getEmail()
            ]);

            $response = $this->json($responseData, Response::HTTP_CREATED);
            $response->headers->set('Access-Control-Allow-Origin', 'http://localhost:3000');
            return $response;

        } catch (BadRequestHttpException $e) {
            return $this->logAndReturnError(
                $e->getMessage(),
                Response::HTTP_BAD_REQUEST,
                ['operation' => 'register_client']
            );
        } catch (UniqueConstraintViolationException $e) {
            $this->logger->error('Database constraint violation during client registration', [
                'exception' => $e->getMessage()
            ]);
            return $this->logAndReturnError(
                'Registration failed - duplicate information provided',
                Response::HTTP_CONFLICT,
                ['operation' => 'register_client'],
                $e
            );
        } catch (ORMException $e) {
            $this->logger->error('Database error during client registration', [
                'exception' => $e->getMessage()
            ]);
            return $this->logAndReturnError(
                'Database error occurred during registration',
                Response::HTTP_INTERNAL_SERVER_ERROR,
                ['operation' => 'register_client'],
                $e
            );
        } catch (\Exception $e) {
            return $this->logAndReturnError(
                'Registration failed due to internal server error',
                Response::HTTP_INTERNAL_SERVER_ERROR,
                ['operation' => 'register_client'],
                $e
            );
        }
    }

    private function generateSlug(string $name): string
    {
        try {
            if (empty(trim($name))) {
                throw new \InvalidArgumentException('Name cannot be empty for slug generation');
            }

            // Convert to lowercase and replace spaces/special chars with hyphens
            $slug = strtolower(trim($name));
            $slug = preg_replace('/[^a-z0-9\s-]/', '', $slug);
            $slug = preg_replace('/[\s-]+/', '-', $slug);
            $slug = trim($slug, '-');
            
            // Ensure slug is not empty after processing
            if (empty($slug)) {
                $slug = 'client-' . uniqid();
            }
            
            // Limit length to prevent extremely long slugs
            if (strlen($slug) > 100) {
                $slug = substr($slug, 0, 100);
                $slug = rtrim($slug, '-');
            }
            
            return $slug;
            
        } catch (\Exception $e) {
            $this->logger->error('Error generating slug', [
                'name' => $name,
                'exception' => $e->getMessage()
            ]);
            
            // Fallback slug generation
            return 'client-' . uniqid();
        }
    }
}
