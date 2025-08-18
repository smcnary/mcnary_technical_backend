<?php

namespace App\Controller\Api\V1;

use App\Entity\User;
use App\Repository\UserRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[Route('/api/v1/auth')]
class AuthController extends AbstractController
{
    public function __construct(
        private UserRepository $userRepository,
        private UserPasswordHasherInterface $passwordHasher,
        private JWTTokenManagerInterface $jwtManager,
        private ValidatorInterface $validator
    ) {}

    #[Route('/login', name: 'api_v1_auth_login', methods: ['POST'])]
    public function login(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            
            if (!$data) {
                return $this->json(['error' => 'Invalid JSON'], Response::HTTP_BAD_REQUEST);
            }

            // Validate input
            $constraints = new Assert\Collection([
                'email' => [new Assert\NotBlank(), new Assert\Email()],
                'password' => [new Assert\NotBlank()]
            ]);

            $violations = $this->validator->validate($data, $constraints);
            if (count($violations) > 0) {
                $errors = [];
                foreach ($violations as $violation) {
                    $errors[$violation->getPropertyPath()] = $violation->getMessage();
                }
                return $this->json(['error' => 'Validation failed', 'details' => $errors], Response::HTTP_BAD_REQUEST);
            }

            $email = $data['email'];
            $password = $data['password'];

            // Find user
            $user = $this->userRepository->findOneBy(['email' => $email]);
            if (!$user) {
                return $this->json(['error' => 'Invalid credentials'], Response::HTTP_UNAUTHORIZED);
            }

            // Check password
            if (!$this->passwordHasher->isPasswordValid($user, $password)) {
                return $this->json(['error' => 'Invalid credentials'], Response::HTTP_UNAUTHORIZED);
            }

            // Check if user can log in (invited users can log in to complete setup, active users are fully active)
            if (!in_array($user->getStatus(), ['invited', 'active'])) {
                return $this->json(['error' => 'Account is not active'], Response::HTTP_FORBIDDEN);
            }

            // Generate JWT token
            $token = $this->jwtManager->create($user);

            // Update last login
            $user->setLastLoginAt(new \DateTimeImmutable());
            $this->userRepository->save($user, true);

            // Return user data (excluding sensitive information)
            $userData = [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'name' => $user->getName(),
                'roles' => $user->getRoles(),
                'client_id' => $user->getClientId(),
                'tenant_id' => $user->getTenant()?->getId(),
                'status' => $user->getStatus(),
                'created_at' => $user->getCreatedAt()->format('c'),
                'last_login_at' => $user->getLastLoginAt()?->format('c')
            ];

            return $this->json([
                'token' => $token,
                'user' => $userData
            ]);

        } catch (\Exception $e) {
            return $this->json(['error' => 'Internal server error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/refresh', name: 'api_v1_auth_refresh', methods: ['POST'])]
    public function refresh(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            
            if (!$data || !isset($data['token'])) {
                return $this->json(['error' => 'Token is required'], Response::HTTP_BAD_REQUEST);
            }

            // In a real implementation, you would validate the refresh token
            // For now, we'll return an error indicating refresh tokens aren't implemented yet
            return $this->json(['error' => 'Refresh tokens not implemented yet'], Response::HTTP_NOT_IMPLEMENTED);

        } catch (\Exception $e) {
            return $this->json(['error' => 'Internal server error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/logout', name: 'api_v1_auth_logout', methods: ['POST'])]
    public function logout(Request $request): JsonResponse
    {
        // In a stateless JWT implementation, logout is typically handled client-side
        // by removing the token. However, you could implement a token blacklist if needed.
        
        return $this->json(['message' => 'Logged out successfully']);
    }
}
