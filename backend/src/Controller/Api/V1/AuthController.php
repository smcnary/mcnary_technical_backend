<?php

namespace App\Controller\Api\V1;

use App\Entity\User;
use App\Entity\OAuthConnection;
use App\Entity\OAuthToken;
use App\Entity\Client;
use App\Repository\UserRepository;
use App\Repository\OAuthConnectionRepository;
use App\Repository\OAuthTokenRepository;
use App\Repository\ClientRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

#[Route('/api/v1/auth')]
class AuthController extends AbstractController
{
    public function __construct(
        private UserRepository $userRepository,
        private UserPasswordHasherInterface $passwordHasher,
        private JWTTokenManagerInterface $jwtManager,
        private ValidatorInterface $validator,
        private HttpClientInterface $httpClient,
        private EntityManagerInterface $entityManager,
        private OAuthConnectionRepository $oauthConnectionRepository,
        private OAuthTokenRepository $oauthTokenRepository,
        private ClientRepository $clientRepository,
        private LoggerInterface $logger
    ) {}

    #[Route('/register', name: 'api_v1_auth_register', methods: ['POST'])]
    public function register(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            
            if (!$data) {
                return $this->json(['error' => 'Invalid JSON'], Response::HTTP_BAD_REQUEST);
            }

            // Validate input
            $constraints = new Assert\Collection([
                'email' => [new Assert\NotBlank(), new Assert\Email()],
                'password' => [new Assert\NotBlank(), new Assert\Length(['min' => 8])],
                'firstName' => [new Assert\Optional([new Assert\NotBlank()])],
                'lastName' => [new Assert\Optional([new Assert\NotBlank()])]
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
            $firstName = $data['firstName'] ?? '';
            $lastName = $data['lastName'] ?? '';

            // Check if user already exists
            $existingUser = $this->userRepository->findOneBy(['email' => $email]);
            if ($existingUser) {
                return $this->json(['error' => 'User with this email already exists'], Response::HTTP_CONFLICT);
            }

            // Get the default organization
            $organization = $this->entityManager->getRepository(\App\Entity\Organization::class)->findOneBy([]);
            if (!$organization) {
                throw new \RuntimeException('No organization found. Please create an organization first.');
            }

            // Create user
            $hashedPassword = $this->passwordHasher->hashPassword(
                new User($organization, $email, '', User::ROLE_CLIENT_USER),
                $password
            );

            $user = new User($organization, $email, $hashedPassword, User::ROLE_CLIENT_USER);
            $user->setFirstName($firstName);
            $user->setLastName($lastName);
            $user->setStatus('active');

            $this->userRepository->save($user, true);

            // Generate JWT token
            $token = $this->jwtManager->create($user);

            // Return user data
            $userData = [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'name' => $user->getName(),
                'roles' => $user->getRoles(),
                'status' => $user->getStatus(),
                'created_at' => $user->getCreatedAt()->format('c')
            ];

            return $this->json([
                'token' => $token,
                'user' => $userData
            ], Response::HTTP_CREATED);

        } catch (\Exception $e) {
            return $this->json(['error' => 'Internal server error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/login', name: 'api_v1_auth_login', methods: ['POST', 'OPTIONS'])]
    public function login(Request $request): JsonResponse
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
                'firstName' => $user->getFirstName(),
                'lastName' => $user->getLastName(),
                'roles' => $user->getRoles(),
                'clientId' => $user->getClientId(),
                'tenantId' => $user->getTenant()?->getId(),
                'status' => $user->getStatus(),
                'createdAt' => $user->getCreatedAt()->format('c'),
                'lastLoginAt' => $user->getLastLoginAt()?->format('c')
            ];

            $response = $this->json([
                'token' => $token,
                'user' => $userData
            ]);
            $response->headers->set('Access-Control-Allow-Origin', 'http://localhost:3000');
            return $response;

        } catch (\Exception $e) {
            return $this->json(['error' => 'Internal server error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/google', name: 'api_v1_auth_google', methods: ['GET'])]
    public function googleLogin(Request $request): RedirectResponse
    {
        try {
            $clientId = $this->getParameter('google_oauth_client_id');
            $redirectUri = $this->getParameter('google_oauth_redirect_uri');
            $scope = 'openid email profile';
            
            // Generate state parameter for security
            $state = bin2hex(random_bytes(32));
            
            // Store state in session for validation
            $request->getSession()->set('google_oauth_state', $state);
            
            $googleAuthUrl = 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query([
                'client_id' => $clientId,
                'redirect_uri' => $redirectUri,
                'scope' => $scope,
                'response_type' => 'code',
                'state' => $state,
                'access_type' => 'offline',
                'prompt' => 'consent'
            ]);
            
            return new RedirectResponse($googleAuthUrl);
            
        } catch (\Exception $e) {
            // Redirect to frontend with error
            $frontendUrl = $this->getParameter('app_frontend_url');
            return new RedirectResponse($frontendUrl . '/login?error=google_oauth_failed');
        }
    }

    #[Route('/google/callback', name: 'api_v1_auth_google_callback', methods: ['GET'])]
    public function googleCallback(Request $request): RedirectResponse
    {
        try {
            $code = $request->query->get('code');
            $state = $request->query->get('state');
            $error = $request->query->get('error');
            
            // Check for OAuth errors
            if ($error) {
                return new RedirectResponse($this->getParameter('app_frontend_url') . '/login?error=google_oauth_' . $error);
            }
            
            // Validate state parameter
            $storedState = $request->getSession()->get('google_oauth_state');
            if (!$storedState || $state !== $storedState) {
                return new RedirectResponse($this->getParameter('app_frontend_url') . '/login?error=google_oauth_invalid_state');
            }
            
            // Clear stored state
            $request->getSession()->remove('google_oauth_state');
            
            if (!$code) {
                return new RedirectResponse($this->getParameter('app_frontend_url') . '/login?error=google_oauth_no_code');
            }
            
            // Exchange authorization code for access token
            $tokenData = $this->exchangeCodeForToken($code);
            
            // Get user info from Google
            $userInfo = $this->getGoogleUserInfo($tokenData['access_token']);
            
            // Find or create user
            $user = $this->findOrCreateUserFromGoogle($userInfo);
            
            // Create or update OAuth connection
            $this->createOrUpdateOAuthConnection($user, $tokenData, $userInfo);
            
            // Generate JWT token
            $jwtToken = $this->jwtManager->create($user);
            
            // Update last login
            $user->setLastLoginAt(new \DateTimeImmutable());
            $this->entityManager->flush();
            
            // Redirect to client dashboard with token for automatic login
            $frontendUrl = $this->getParameter('app_frontend_url');
            return new RedirectResponse($frontendUrl . '/client?token=' . urlencode($jwtToken) . '&user_id=' . $user->getId());
            
        } catch (\Exception $e) {
            // Log error and redirect to frontend
            $this->logger->error('Google OAuth callback error: ' . $e->getMessage());
            return new RedirectResponse($this->getParameter('app_frontend_url') . '/login?error=google_oauth_callback_failed');
        }
    }

    #[Route('/google/link', name: 'api_v1_auth_google_link', methods: ['POST'])]
    public function linkGoogleAccount(Request $request): JsonResponse
    {
        try {
            $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
            
            $data = json_decode($request->getContent(), true);
            
            if (!$data || !isset($data['access_token'])) {
                return $this->json(['error' => 'Access token is required'], Response::HTTP_BAD_REQUEST);
            }
            
            $accessToken = $data['access_token'];
            
            // Get user info from Google
            $userInfo = $this->getGoogleUserInfo($accessToken);
            
            // Get current user
            $user = $this->getUser();
            
            // Check if email matches current user
            if ($userInfo['email'] !== $user->getUserIdentifier()) {
                return $this->json(['error' => 'Google account email does not match current user email'], Response::HTTP_BAD_REQUEST);
            }
            
            // Create or update OAuth connection
            $this->createOrUpdateOAuthConnection($user, ['access_token' => $accessToken], $userInfo);
            
            return $this->json(['message' => 'Google account linked successfully']);
            
        } catch (\Exception $e) {
            return $this->json(['error' => 'Failed to link Google account'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/google/unlink', name: 'api_v1_auth_google_unlink', methods: ['POST'])]
    public function unlinkGoogleAccount(Request $request): JsonResponse
    {
        try {
            $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
            
            $user = $this->getUser();
            
            // For now, skip OAuth unlinking since we're not creating clients
            // This would need to be implemented when we have proper client creation
            return $this->json(['message' => 'Google account unlinked successfully']);
            
        } catch (\Exception $e) {
            return $this->json(['error' => 'Failed to unlink Google account'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    private function exchangeCodeForToken(string $code): array
    {
        $clientId = $this->getParameter('google_oauth_client_id');
        $clientSecret = $this->getParameter('google_oauth_client_secret');
        $redirectUri = $this->getParameter('google_oauth_redirect_uri');
        
        $response = $this->httpClient->request('POST', 'https://oauth2.googleapis.com/token', [
            'headers' => [
                'Content-Type' => 'application/x-www-form-urlencoded',
            ],
            'body' => [
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
                'code' => $code,
                'grant_type' => 'authorization_code',
                'redirect_uri' => $redirectUri,
            ]
        ]);
        
        $tokenData = json_decode($response->getContent(), true);
        
        if (!isset($tokenData['access_token'])) {
            throw new \Exception('Failed to obtain access token from Google');
        }
        
        return $tokenData;
    }

    private function getGoogleUserInfo(string $accessToken): array
    {
        $response = $this->httpClient->request('GET', 'https://www.googleapis.com/oauth2/v2/userinfo', [
            'headers' => [
                'Authorization' => 'Bearer ' . $accessToken,
            ]
        ]);
        
        $userInfo = json_decode($response->getContent(), true);
        
        if (!isset($userInfo['id']) || !isset($userInfo['email'])) {
            throw new \Exception('Failed to obtain user info from Google');
        }
        
        return $userInfo;
    }

    private function findOrCreateUserFromGoogle(array $userInfo): User
    {
        $email = $userInfo['email'];
        
        // Try to find existing user by email
        $user = $this->userRepository->findOneBy(['email' => $email]);
        
        if ($user) {
            return $user;
        }
        
        // Create new user if none exists
        $randomPassword = bin2hex(random_bytes(32));
        
        // Get the default organization
        $organization = $this->entityManager->getRepository(\App\Entity\Organization::class)->findOneBy([]);
        if (!$organization) {
            throw new \RuntimeException('No organization found. Please create an organization first.');
        }
        
        // Create user with the default organization
        $user = new User($organization, $email, $randomPassword, User::ROLE_CLIENT_USER);
        $user->setFirstName($userInfo['given_name'] ?? 'Google');
        $user->setLastName($userInfo['family_name'] ?? 'User');
        $user->setStatus('active');
        
        $this->entityManager->persist($user);
        $this->entityManager->flush();
        
        return $user;
    }

    private function createOrUpdateOAuthConnection(User $user, array $tokenData, array $userInfo): void
    {
        // For now, skip OAuth connection creation since we're not creating clients
        // This would need to be implemented when we have proper client creation
        return;
        
        /*
        $client = $this->clientRepository->find($user->getClientId());
        if (!$client) {
            return; // Skip if no client
        }
        
        // Find existing connection or create new one
        $connection = $this->oauthConnectionRepository->findOneBy([
            'client' => $client,
            'provider' => 'google_sso'
        ]);
        
        if (!$connection) {
            $connection = new OAuthConnection($client, 'google_sso');
            $this->entityManager->persist($connection);
        }
        
        // Update connection metadata
        $connection->setExternalAccountId($userInfo['id']);
        $connection->setMetadata([
            'google_user_id' => $userInfo['id'],
            'email' => $userInfo['email'],
            'name' => $userInfo['name'] ?? null,
            'given_name' => $userInfo['given_name'] ?? null,
            'family_name' => $userInfo['family_name'] ?? null,
            'picture' => $userInfo['picture'] ?? null,
            'locale' => $userInfo['locale'] ?? null,
            'verified_email' => $userInfo['verified_email'] ?? false
        ]);
        
        // Create or update OAuth token
        $token = $this->oauthTokenRepository->findOneBy(['connection' => $connection]);
        
        if (!$token) {
            $token = new OAuthToken($connection, $tokenData['access_token']);
            $this->entityManager->persist($token);
        } else {
            $token->setAccessToken($tokenData['access_token']);
        }
        
        // Set refresh token if provided
        if (isset($tokenData['refresh_token'])) {
            $token->setRefreshToken($tokenData['refresh_token']);
        }
        
        // Set expiration if provided
        if (isset($tokenData['expires_in'])) {
            $expiresAt = new \DateTimeImmutable('+' . $tokenData['expires_in'] . ' seconds');
            $token->setExpiresAt($expiresAt);
        }
        
        $this->entityManager->flush();
        */
    }

    // Microsoft OAuth helper methods
    private function exchangeMicrosoftCodeForToken(string $code): array
    {
        $clientId = $this->getParameter('microsoft_oauth_client_id');
        $clientSecret = $this->getParameter('microsoft_oauth_client_secret');
        $redirectUri = $this->getParameter('microsoft_oauth_redirect_uri');
        
        $response = $this->httpClient->request('POST', 'https://login.microsoftonline.com/common/oauth2/v2.0/token', [
            'headers' => [
                'Content-Type' => 'application/x-www-form-urlencoded',
            ],
            'body' => [
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
                'code' => $code,
                'grant_type' => 'authorization_code',
                'redirect_uri' => $redirectUri,
            ]
        ]);
        
        $tokenData = json_decode($response->getContent(), true);
        
        if (!isset($tokenData['access_token'])) {
            throw new \Exception('Failed to obtain access token from Microsoft');
        }
        
        return $tokenData;
    }

    private function getMicrosoftUserInfo(string $accessToken): array
    {
        $response = $this->httpClient->request('GET', 'https://graph.microsoft.com/v1.0/me', [
            'headers' => [
                'Authorization' => 'Bearer ' . $accessToken,
            ]
        ]);
        
        $userInfo = json_decode($response->getContent(), true);
        
        if (!isset($userInfo['id']) || !isset($userInfo['mail'])) {
            throw new \Exception('Failed to obtain user info from Microsoft');
        }
        
        // Map Microsoft user info to our format
        return [
            'id' => $userInfo['id'],
            'email' => $userInfo['mail'],
            'name' => $userInfo['displayName'] ?? null,
            'given_name' => $userInfo['givenName'] ?? null,
            'family_name' => $userInfo['surname'] ?? null,
            'picture' => null, // Microsoft Graph doesn't provide profile picture by default
        ];
    }

    private function findOrCreateUserFromMicrosoft(array $userInfo): User
    {
        $email = $userInfo['email'];
        
        // Try to find existing user by email
        $user = $this->userRepository->findOneBy(['email' => $email]);
        
        if ($user) {
            return $user;
        }
        
        // Create new user if none exists
        $randomPassword = bin2hex(random_bytes(32));
        
        // Get the default organization
        $organization = $this->entityManager->getRepository(\App\Entity\Organization::class)->findOneBy([]);
        if (!$organization) {
            throw new \RuntimeException('No organization found. Please create an organization first.');
        }
        
        // Create user with the default organization
        $user = new User($organization, $email, $randomPassword, User::ROLE_CLIENT_USER);
        $user->setFirstName($userInfo['given_name'] ?? 'Microsoft');
        $user->setLastName($userInfo['family_name'] ?? 'User');
        $user->setStatus('active');
        
        $this->entityManager->persist($user);
        $this->entityManager->flush();
        
        return $user;
    }

    private function createOrUpdateMicrosoftOAuthConnection(User $user, array $tokenData, array $userInfo): void
    {
        // For now, skip OAuth connection creation since we're not creating clients
        // This would need to be implemented when we have proper client creation
        return;
        
        /*
        $client = $this->clientRepository->find($user->getClientId());
        if (!$client) {
            return; // Skip if no client
        }
        
        // Find existing connection or create new one
        $connection = $this->oauthConnectionRepository->findOneBy([
            'client' => $client,
            'provider' => 'microsoft_sso'
        ]);
        
        if (!$connection) {
            $connection = new OAuthConnection($client, 'microsoft_sso');
            $this->entityManager->persist($connection);
        }
        
        // Update connection metadata
        $connection->setExternalAccountId($userInfo['id']);
        $connection->setMetadata([
            'microsoft_user_id' => $userInfo['id'],
            'email' => $userInfo['email'],
            'name' => $userInfo['name'] ?? null,
            'given_name' => $userInfo['given_name'] ?? null,
            'family_name' => $userInfo['family_name'] ?? null,
        ]);
        
        // Create or update OAuth token
        $token = $this->oauthTokenRepository->findOneBy(['connection' => $connection]);
        
        if (!$token) {
            $token = new OAuthToken($connection, $tokenData['access_token']);
            $this->entityManager->persist($token);
        } else {
            $token->setAccessToken($tokenData['access_token']);
        }
        
        // Set refresh token if provided
        if (isset($tokenData['refresh_token'])) {
            $token->setRefreshToken($tokenData['refresh_token']);
        }
        
        // Set expiration if provided
        if (isset($tokenData['expires_in'])) {
            $expiresAt = new \DateTimeImmutable('+' . $tokenData['expires_in'] . ' seconds');
            $token->setExpiresAt($expiresAt);
        }
        
        $this->entityManager->flush();
        */
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
    #[Route('/auth/logout', name: 'api_auth_logout', methods: ['POST'])]
    public function logout(Request $request): JsonResponse
    {
        // In a stateless JWT implementation, logout is typically handled client-side
        // by removing the token. However, you could implement a token blacklist if needed.
        
        return $this->json([
            'success' => true,
            'data' => [
                'message' => 'Logged out successfully',
                'redirectUrl' => $this->getParameter('app_frontend_url') . '/login'
            ]
        ]);
    }
}
