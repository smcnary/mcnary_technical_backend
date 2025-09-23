<?php

namespace App\Controller\Api\V1;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Repository\ClientRepository;
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

#[Route('/api/v1')]
class UserController extends AbstractController
{
    public function __construct(
        private UserRepository $userRepository,
        private ClientRepository $clientRepository,
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher,
        private ValidatorInterface $validator
    ) {}

    #[Route('/test', name: 'api_v1_test', methods: ['GET'])]
    public function test(): JsonResponse
    {
        return $this->json([
            'message' => 'API is working',
            'timestamp' => (new \DateTime())->format('c')
        ]);
    }

    #[Route('/me', name: 'api_v1_me', methods: ['GET'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function me(): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        
        $userData = [
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'firstName' => $user->getFirstName(),
            'lastName' => $user->getLastName(),
            'name' => $user->getName(),
            'roles' => $user->getRoles(),
            'agencyId' => $user->getAgency()?->getId(),
            'clientId' => $user->getClientId(),
            'tenantId' => $user->getTenant()?->getId(),
            'status' => $user->getStatus(),
            'createdAt' => $user->getCreatedAt()->format('c'),
            'lastLoginAt' => $user->getLastLoginAt()?->format('c'),
            'metadata' => $user->getMetadata()
        ];

        return $this->json($userData);
    }

    #[Route('/users', name: 'api_v1_users_list', methods: ['GET'])]
    #[IsGranted('ROLE_AGENCY_ADMIN')]
    public function listUsers(Request $request): JsonResponse
    {
        // Check if user has appropriate role
        if (!$this->isGranted('ROLE_SYSTEM_ADMIN') && !$this->isGranted('ROLE_AGENCY_ADMIN')) {
            return $this->json(['error' => 'Access denied. System Admin or Agency Admin role required.'], Response::HTTP_FORBIDDEN);
        }

        /** @var User $currentUser */
        $currentUser = $this->getUser();
        $isAgencyAdmin = $this->isGranted('ROLE_AGENCY_ADMIN') && !$this->isGranted('ROLE_SYSTEM_ADMIN');
        
        $page = max(1, (int) $request->query->get('page', 1));
        $perPage = min(100, max(1, (int) $request->query->get('per_page', 20)));
        $sort = $request->query->get('sort', 'created_at');
        $search = $request->query->get('search', '');
        $role = $request->query->get('role', '');
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

        // Get users with pagination and filtering
        $criteria = [];
        if ($search) {
            $criteria['search'] = $search;
        }
        if ($role) {
            $criteria['role'] = $role;
        }
        if ($status) {
            $criteria['status'] = $status;
        }
        
        // Agency admins can only see users within their agency scope
        if ($isAgencyAdmin && $currentUser->getAgency()) {
            $criteria['agency_id'] = $currentUser->getAgency()->getId();
        }

        $users = $this->userRepository->findByCriteria($criteria, $sortFields, $perPage, ($page - 1) * $perPage);
        $totalUsers = $this->userRepository->countByCriteria($criteria);

        $userData = [];
        foreach ($users as $user) {
            $userData[] = [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'name' => $user->getName(),
                'roles' => $user->getRoles(),
                'agency_id' => $user->getAgency()?->getId(),
                'client_id' => $user->getClientId(),
                'status' => $user->getStatus(),
                'created_at' => $user->getCreatedAt()->format('c'),
                'last_login_at' => $user->getLastLoginAt()?->format('c')
            ];
        }

        return $this->json([
            'data' => $userData,
            'pagination' => [
                'page' => $page,
                'per_page' => $perPage,
                'total' => $totalUsers,
                'pages' => ceil($totalUsers / $perPage)
            ]
        ]);
    }

    #[Route('/users', name: 'api_v1_users_create', methods: ['POST'])]
    #[IsGranted('ROLE_AGENCY_ADMIN')]
    public function createUser(Request $request): JsonResponse
    {
        // Check if user has appropriate role
        if (!$this->isGranted('ROLE_SYSTEM_ADMIN') && !$this->isGranted('ROLE_AGENCY_ADMIN')) {
            return $this->json(['error' => 'Access denied. System Admin or Agency Admin role required.'], Response::HTTP_FORBIDDEN);
        }

        /** @var User $currentUser */
        $currentUser = $this->getUser();
        $isAgencyAdmin = $this->isGranted('ROLE_AGENCY_ADMIN') && !$this->isGranted('ROLE_SYSTEM_ADMIN');
        
        try {
            $data = json_decode($request->getContent(), true);
            
            if (!$data) {
                return $this->json(['error' => 'Invalid JSON'], Response::HTTP_BAD_REQUEST);
            }

            // Validate input
            $constraints = new Assert\Collection([
                'email' => [new Assert\NotBlank(), new Assert\Email()],
                'name' => [new Assert\NotBlank()],
                'role' => [new Assert\NotBlank(), new Assert\Choice([
                    User::ROLE_AGENCY_ADMIN,
                    User::ROLE_AGENCY_STAFF,
                    User::ROLE_CLIENT_ADMIN,
                    User::ROLE_CLIENT_STAFF,
                    User::ROLE_CLIENT_USER,
                    User::ROLE_SALES_CONSULTANT,
                    User::ROLE_READ_ONLY
                ])],
                'agency_id' => [new Assert\Optional([new Assert\Uuid()])],
                'client_id' => [new Assert\Optional([new Assert\Uuid()])],
                'status' => [new Assert\Optional([new Assert\Choice(['invited', 'active', 'inactive'])])]
            ]);

            $violations = $this->validator->validate($data, $constraints);
            if (count($violations) > 0) {
                $errors = [];
                foreach ($violations as $violation) {
                    $errors[$violation->getPropertyPath()] = $violation->getMessage();
                }
                return $this->json(['error' => 'Validation failed', 'details' => $errors], Response::HTTP_BAD_REQUEST);
            }

            // Check if user already exists
            $existingUser = $this->userRepository->findOneBy(['email' => $data['email']]);
            if ($existingUser) {
                return $this->json(['error' => 'User with this email already exists'], Response::HTTP_CONFLICT);
            }

            // Security check for agency admins
            if ($isAgencyAdmin) {
                // Agency admins can only create users within their own agency scope
                if (!isset($data['agency_id']) || $data['agency_id'] !== $currentUser->getAgency()?->getId()) {
                    return $this->json(['error' => 'Agency admins can only create users within their own agency scope'], Response::HTTP_FORBIDDEN);
                }
                
                // Agency admins cannot create system admins
                if ($data['role'] === User::ROLE_SYSTEM_ADMIN) {
                    return $this->json(['error' => 'Agency admins cannot create system admins'], Response::HTTP_FORBIDDEN);
                }
            }

            // Validate agency_id if provided
            if (isset($data['agency_id'])) {
                $agency = $this->entityManager->getRepository('App\Entity\Agency')->find($data['agency_id']);
                if (!$agency) {
                    return $this->json(['error' => 'Agency not found'], Response::HTTP_NOT_FOUND);
                }
            }

            // Validate client_id if provided
            if (isset($data['client_id'])) {
                $client = $this->clientRepository->find($data['client_id']);
                if (!$client) {
                    return $this->json(['error' => 'Client not found'], Response::HTTP_NOT_FOUND);
                }
            }

            // Generate temporary password for invited users
            $tempPassword = bin2hex(random_bytes(8));
            $hashedPassword = $this->passwordHasher->hashPassword($currentUser, $tempPassword);

            // Create user
            $agency = isset($data['agency_id']) ? 
                $this->entityManager->getRepository('App\Entity\Agency')->find($data['agency_id']) : 
                $currentUser->getAgency();
                
            // Get the default organization
            $organization = $this->entityManager->getRepository(\App\Entity\Organization::class)->findOneBy([]);
            if (!$organization) {
                throw new \RuntimeException('No organization found. Please create an organization first.');
            }
                
            $user = new User(
                $organization,               // organization
                $data['email'],             // email
                $hashedPassword,            // hash
                $data['role']               // role
            );
            
            // Set the agency relationship
            if ($agency) {
                $user->setAgency($agency);
            }
            
            // Set first and last name from the name field
            $nameParts = explode(' ', $data['name'], 2);
            $user->setFirstName($nameParts[0]);
            $user->setLastName($nameParts[1] ?? '');
            
            $user->setStatus($data['status'] ?? 'invited');

            if (isset($data['client_id'])) {
                $user->setClientId($data['client_id']);
            }

            $this->entityManager->persist($user);
            $this->entityManager->flush();

            $userData = [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'name' => $user->getName(),
                'roles' => $user->getRoles(),
                'agency_id' => $user->getAgency()?->getId(),
                'client_id' => $user->getClientId(),
                'status' => $user->getStatus(),
                'created_at' => $user->getCreatedAt()->format('c')
            ];

            return $this->json([
                'message' => 'User created successfully',
                'user' => $userData,
                'temp_password' => $tempPassword // Only for invited users
            ], Response::HTTP_CREATED);

        } catch (\Exception $e) {
            return $this->json(['error' => 'Internal server error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/users/{id}', name: 'api_v1_users_update', methods: ['PATCH'])]
    #[IsGranted('ROLE_AGENCY_ADMIN')]
    public function updateUser(string $id, Request $request): JsonResponse
    {
        // Check if user has appropriate role
        if (!$this->isGranted('ROLE_SYSTEM_ADMIN') && !$this->isGranted('ROLE_AGENCY_ADMIN')) {
            return $this->json(['error' => 'Access denied. System Admin or Agency Admin role required.'], Response::HTTP_FORBIDDEN);
        }

        /** @var User $currentUser */
        $currentUser = $this->getUser();
        $isAgencyAdmin = $this->isGranted('ROLE_AGENCY_ADMIN') && !$this->isGranted('ROLE_SYSTEM_ADMIN');
        
        try {
            if (!Uuid::isValid($id)) {
                return $this->json(['error' => 'Invalid UUID'], Response::HTTP_BAD_REQUEST);
            }

            $user = $this->userRepository->find($id);
            if (!$user) {
                return $this->json(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
            }

            // Security check for agency admins
            if ($isAgencyAdmin) {
                // Agency admins can only update users within their own agency scope
                if ($user->getAgency()?->getId() !== $currentUser->getAgency()?->getId()) {
                    return $this->json(['error' => 'Agency admins can only update users within their own agency scope'], Response::HTTP_FORBIDDEN);
                }
                
                // Agency admins cannot update system admins
                if ($user->hasRole(User::ROLE_SYSTEM_ADMIN)) {
                    return $this->json(['error' => 'Agency admins cannot update system admins'], Response::HTTP_FORBIDDEN);
                }
            }

            $data = json_decode($request->getContent(), true);
            
            if (!$data) {
                return $this->json(['error' => 'Invalid JSON'], Response::HTTP_BAD_REQUEST);
            }

            // Validate input
            $constraints = new Assert\Collection([
                'name' => [new Assert\Optional([new Assert\NotBlank()])],
                'role' => [new Assert\Optional([new Assert\Choice([
                    User::ROLE_AGENCY_ADMIN,
                    User::ROLE_CLIENT_USER,
                    User::ROLE_READ_ONLY
                ])])],
                'agency_id' => [new Assert\Optional([new Assert\Uuid()])],
                'client_id' => [new Assert\Optional([new Assert\Uuid()])],
                'status' => [new Assert\Optional([new Assert\Choice(['invited', 'active', 'inactive'])])],
                'metadata' => [new Assert\Optional([new Assert\Type('array')])]
            ]);

            $violations = $this->validator->validate($data, $constraints);
            if (count($violations) > 0) {
                $errors = [];
                foreach ($violations as $violation) {
                    $errors[$violation->getPropertyPath()] = $violation->getMessage();
                }
                return $this->json(['error' => 'Validation failed', 'details' => $errors], Response::HTTP_BAD_REQUEST);
            }

            // Additional security check for role changes by agency admins
            if ($isAgencyAdmin && isset($data['role'])) {
                if ($data['role'] === User::ROLE_SYSTEM_ADMIN) {
                    return $this->json(['error' => 'Agency admins cannot assign system admin roles'], Response::HTTP_FORBIDDEN);
                }
            }

            // Update fields
            if (isset($data['name'])) {
                $nameParts = explode(' ', $data['name'], 2);
                $user->setFirstName($nameParts[0]);
                $user->setLastName($nameParts[1] ?? '');
            }

            if (isset($data['role'])) {
                $user->setRole($data['role']);
            }

            if (isset($data['agency_id'])) {
                // Agency admins cannot change agency_id
                if (!$isAgencyAdmin) {
                    $agency = $this->entityManager->getRepository('App\Entity\Agency')->find($data['agency_id']);
                    if (!$agency) {
                        return $this->json(['error' => 'Agency not found'], Response::HTTP_NOT_FOUND);
                    }
                    $user->setAgency($agency);
                }
            }

            if (isset($data['client_id'])) {
                $client = $this->clientRepository->find($data['client_id']);
                if (!$client) {
                    return $this->json(['error' => 'Client not found'], Response::HTTP_NOT_FOUND);
                }
                $user->setClientId($data['client_id']);
            }

            if (isset($data['status'])) {
                $user->setStatus($data['status']);
            }

            if (isset($data['metadata'])) {
                $user->setMetadata($data['metadata']);
            }

            $this->entityManager->flush();

            $userData = [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'name' => $user->getName(),
                'roles' => $user->getRoles(),
                'agency_id' => $user->getAgency()?->getId(),
                'client_id' => $user->getClientId(),
                'status' => $user->getStatus(),
                'created_at' => $user->getCreatedAt()->format('c'),
                'updated_at' => $user->getUpdatedAt()->format('c'),
                'metadata' => $user->getMetadata()
            ];

            return $this->json([
                'message' => 'User updated successfully',
                'user' => $userData
            ]);

        } catch (\Exception $e) {
            return $this->json(['error' => 'Internal server error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
