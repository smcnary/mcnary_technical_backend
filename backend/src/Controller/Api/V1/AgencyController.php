<?php

namespace App\Controller\Api\V1;

use App\Entity\Agency;
use App\Entity\User;
use App\Repository\AgencyRepository;
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

#[Route('/api/v1')]
class AgencyController extends AbstractController
{
    public function __construct(
        private AgencyRepository $agencyRepository,
        private UserRepository $userRepository,
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher,
        private ValidatorInterface $validator
    ) {}

    #[Route('/agencies', name: 'api_v1_agencies_list', methods: ['GET'])]
    #[IsGranted('ROLE_SYSTEM_ADMIN')]
    public function listAgencies(Request $request): JsonResponse
    {
        $page = max(1, (int) $request->query->get('page', 1));
        $perPage = min(100, max(1, (int) $request->query->get('per_page', 20)));
        $sort = $request->query->get('sort', 'created_at');
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

        // Get agencies with pagination and filtering
        $criteria = [];
        if ($search) {
            $criteria['search'] = $search;
        }
        if ($status) {
            $criteria['status'] = $status;
        }

        $agencies = $this->agencyRepository->findByCriteria($criteria, $sortFields, $perPage, ($page - 1) * $perPage);
        $totalAgencies = $this->agencyRepository->countByCriteria($criteria);

        $agencyData = [];
        foreach ($agencies as $agency) {
            $agencyData[] = [
                'id' => $agency->getId(),
                'name' => $agency->getName(),
                'domain' => $agency->getDomain(),
                'description' => $agency->getDescription(),
                'website_url' => $agency->getWebsiteUrl(),
                'phone' => $agency->getPhone(),
                'email' => $agency->getEmail(),
                'address' => $agency->getAddress(),
                'city' => $agency->getCity(),
                'state' => $agency->getState(),
                'postal_code' => $agency->getPostalCode(),
                'country' => $agency->getCountry(),
                'status' => $agency->getStatus(),
                'created_at' => $agency->getCreatedAt()->format('c'),
                'updated_at' => $agency->getUpdatedAt()->format('c'),
                'metadata' => $agency->getMetadata()
            ];
        }

        return $this->json([
            'data' => $agencyData,
            'pagination' => [
                'page' => $page,
                'per_page' => $perPage,
                'total' => $totalAgencies,
                'pages' => ceil($totalAgencies / $perPage)
            ]
        ]);
    }

    #[Route('/agencies', name: 'api_v1_agencies_create', methods: ['POST'])]
    #[IsGranted('ROLE_SYSTEM_ADMIN')]
    public function createAgency(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            
            if (!$data) {
                return $this->json(['error' => 'Invalid JSON'], Response::HTTP_BAD_REQUEST);
            }

            // Validate input
            $constraints = new Assert\Collection([
                'name' => [new Assert\NotBlank()],
                'domain' => [new Assert\Optional([new Assert\NotBlank()])],
                'description' => [new Assert\Optional([new Assert\NotBlank()])],
                'website_url' => [new Assert\Optional([new Assert\Url()])],
                'phone' => [new Assert\Optional([new Assert\NotBlank()])],
                'email' => [new Assert\Optional([new Assert\Email()])],
                'address' => [new Assert\Optional([new Assert\NotBlank()])],
                'city' => [new Assert\Optional([new Assert\NotBlank()])],
                'state' => [new Assert\Optional([new Assert\NotBlank()])],
                'postal_code' => [new Assert\Optional([new Assert\NotBlank()])],
                'country' => [new Assert\Optional([new Assert\NotBlank()])],
                'status' => [new Assert\Optional([new Assert\Choice(['active', 'inactive'])])]
            ]);

            $violations = $this->validator->validate($data, $constraints);
            if (count($violations) > 0) {
                $errors = [];
                foreach ($violations as $violation) {
                    $errors[$violation->getPropertyPath()] = $violation->getMessage();
                }
                return $this->json(['error' => 'Validation failed', 'details' => $errors], Response::HTTP_BAD_REQUEST);
            }

            // Check if agency with same domain already exists
            if (isset($data['domain'])) {
                $existingAgency = $this->agencyRepository->findOneBy(['domain' => $data['domain']]);
                if ($existingAgency) {
                    return $this->json(['error' => 'Agency with this domain already exists'], Response::HTTP_CONFLICT);
                }
            }

            // Create agency
            $agency = new Agency($data['name']);
            
            if (isset($data['domain'])) {
                $agency->setDomain($data['domain']);
            }
            if (isset($data['description'])) {
                $agency->setDescription($data['description']);
            }
            if (isset($data['website_url'])) {
                $agency->setWebsiteUrl($data['website_url']);
            }
            if (isset($data['phone'])) {
                $agency->setPhone($data['phone']);
            }
            if (isset($data['email'])) {
                $agency->setEmail($data['email']);
            }
            if (isset($data['address'])) {
                $agency->setAddress($data['address']);
            }
            if (isset($data['city'])) {
                $agency->setCity($data['city']);
            }
            if (isset($data['state'])) {
                $agency->setState($data['state']);
            }
            if (isset($data['postal_code'])) {
                $agency->setPostalCode($data['postal_code']);
            }
            if (isset($data['country'])) {
                $agency->setCountry($data['country']);
            }
            if (isset($data['status'])) {
                $agency->setStatus($data['status']);
            }

            $this->entityManager->persist($agency);
            $this->entityManager->flush();

            $agencyData = [
                'id' => $agency->getId(),
                'name' => $agency->getName(),
                'domain' => $agency->getDomain(),
                'description' => $agency->getDescription(),
                'website_url' => $agency->getWebsiteUrl(),
                'phone' => $agency->getPhone(),
                'email' => $agency->getEmail(),
                'address' => $agency->getAddress(),
                'city' => $agency->getCity(),
                'state' => $agency->getState(),
                'postal_code' => $agency->getPostalCode(),
                'country' => $agency->getCountry(),
                'status' => $agency->getStatus(),
                'created_at' => $agency->getCreatedAt()->format('c')
            ];

            return $this->json([
                'message' => 'Agency created successfully',
                'agency' => $agencyData
            ], Response::HTTP_CREATED);

        } catch (\Exception $e) {
            return $this->json(['error' => 'Internal server error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/agencies/{id}', name: 'api_v1_agencies_update', methods: ['PATCH'])]
    #[IsGranted('ROLE_SYSTEM_ADMIN')]
    public function updateAgency(string $id, Request $request): JsonResponse
    {
        try {
            if (!Uuid::isValid($id)) {
                return $this->json(['error' => 'Invalid UUID'], Response::HTTP_BAD_REQUEST);
            }

            $agency = $this->agencyRepository->find($id);
            if (!$agency) {
                return $this->json(['error' => 'Agency not found'], Response::HTTP_NOT_FOUND);
            }

            $data = json_decode($request->getContent(), true);
            
            if (!$data) {
                return $this->json(['error' => 'Invalid JSON'], Response::HTTP_BAD_REQUEST);
            }

            // Validate input
            $constraints = new Assert\Collection([
                'name' => [new Assert\Optional([new Assert\NotBlank()])],
                'domain' => [new Assert\Optional([new Assert\NotBlank()])],
                'description' => [new Assert\Optional([new Assert\NotBlank()])],
                'website_url' => [new Assert\Optional([new Assert\Url()])],
                'phone' => [new Assert\Optional([new Assert\NotBlank()])],
                'email' => [new Assert\Optional([new Assert\Email()])],
                'address' => [new Assert\Optional([new Assert\NotBlank()])],
                'city' => [new Assert\Optional([new Assert\NotBlank()])],
                'state' => [new Assert\Optional([new Assert\NotBlank()])],
                'postal_code' => [new Assert\Optional([new Assert\NotBlank()])],
                'country' => [new Assert\Optional([new Assert\NotBlank()])],
                'status' => [new Assert\Optional([new Assert\Choice(['active', 'inactive'])])],
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

            // Check if domain is being changed and if it conflicts with existing agencies
            if (isset($data['domain']) && $data['domain'] !== $agency->getDomain()) {
                $existingAgency = $this->agencyRepository->findOneBy(['domain' => $data['domain']]);
                if ($existingAgency) {
                    return $this->json(['error' => 'Agency with this domain already exists'], Response::HTTP_CONFLICT);
                }
            }

            // Update fields
            if (isset($data['name'])) {
                $agency->setName($data['name']);
            }
            if (isset($data['domain'])) {
                $agency->setDomain($data['domain']);
            }
            if (isset($data['description'])) {
                $agency->setDescription($data['description']);
            }
            if (isset($data['website_url'])) {
                $agency->setWebsiteUrl($data['website_url']);
            }
            if (isset($data['phone'])) {
                $agency->setPhone($data['phone']);
            }
            if (isset($data['email'])) {
                $agency->setEmail($data['email']);
            }
            if (isset($data['address'])) {
                $agency->setAddress($data['address']);
            }
            if (isset($data['city'])) {
                $agency->setCity($data['city']);
            }
            if (isset($data['state'])) {
                $agency->setState($data['state']);
            }
            if (isset($data['postal_code'])) {
                $agency->setPostalCode($data['postal_code']);
            }
            if (isset($data['country'])) {
                $agency->setCountry($data['country']);
            }
            if (isset($data['status'])) {
                $agency->setStatus($data['status']);
            }
            if (isset($data['metadata'])) {
                $agency->setMetadata($data['metadata']);
            }

            $this->entityManager->flush();

            $agencyData = [
                'id' => $agency->getId(),
                'name' => $agency->getName(),
                'domain' => $agency->getDomain(),
                'description' => $agency->getDescription(),
                'website_url' => $agency->getWebsiteUrl(),
                'phone' => $agency->getPhone(),
                'email' => $agency->getEmail(),
                'address' => $agency->getAddress(),
                'city' => $agency->getCity(),
                'state' => $agency->getState(),
                'postal_code' => $agency->getPostalCode(),
                'country' => $agency->getCountry(),
                'status' => $agency->getStatus(),
                'created_at' => $agency->getCreatedAt()->format('c'),
                'updated_at' => $agency->getUpdatedAt()->format('c'),
                'metadata' => $agency->getMetadata()
            ];

            return $this->json([
                'message' => 'Agency updated successfully',
                'agency' => $agencyData
            ]);

        } catch (\Exception $e) {
            return $this->json(['error' => 'Internal server error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/agencies/{id}/invite-admin', name: 'api_v1_agencies_invite_admin', methods: ['POST'])]
    #[IsGranted('ROLE_SYSTEM_ADMIN')]
    public function inviteAgencyAdmin(string $id, Request $request): JsonResponse
    {
        try {
            if (!Uuid::isValid($id)) {
                return $this->json(['error' => 'Invalid UUID'], Response::HTTP_BAD_REQUEST);
            }

            $agency = $this->agencyRepository->find($id);
            if (!$agency) {
                return $this->json(['error' => 'Agency not found'], Response::HTTP_NOT_FOUND);
            }

            $data = json_decode($request->getContent(), true);
            
            if (!$data) {
                return $this->json(['error' => 'Invalid JSON'], Response::HTTP_BAD_REQUEST);
            }

            // Validate input
            $constraints = new Assert\Collection([
                'email' => [new Assert\NotBlank(), new Assert\Email()],
                'name' => [new Assert\NotBlank()]
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

            // Generate temporary password for invited users
            $tempPassword = bin2hex(random_bytes(8));
            
            // Get the default organization
            $organization = $this->entityManager->getRepository(\App\Entity\Organization::class)->findOneBy([]);
            if (!$organization) {
                throw new \RuntimeException('No organization found. Please create an organization first.');
            }
            
            $hashedPassword = $this->passwordHasher->hashPassword(new User($organization, 'temp', 'temp', 'temp'), $tempPassword);

            // Create agency admin user
            $user = new User(
                $organization,              // organization
                $data['email'],             // email
                $hashedPassword,            // hash
                User::ROLE_AGENCY_ADMIN     // role
            );
            
            // Set the agency relationship
            $user->setAgency($agency);
            
            // Set first and last name from the name field
            $nameParts = explode(' ', $data['name'], 2);
            $user->setFirstName($nameParts[0]);
            $user->setLastName($nameParts[1] ?? '');
            
            $user->setStatus('invited');

            $this->entityManager->persist($user);
            $this->entityManager->flush();

            $userData = [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'name' => $user->getName(),
                'roles' => $user->getRoles(),
                'organization_id' => $user->getOrganization()->getId(),
                'status' => $user->getStatus(),
                'created_at' => $user->getCreatedAt()->format('c')
            ];

            return $this->json([
                'message' => 'Agency admin invited successfully',
                'user' => $userData,
                'temp_password' => $tempPassword
            ], Response::HTTP_CREATED);

        } catch (\Exception $e) {
            return $this->json(['error' => 'Internal server error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
