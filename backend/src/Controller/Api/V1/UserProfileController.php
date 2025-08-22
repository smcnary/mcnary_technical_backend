<?php

namespace App\Controller\Api\V1;

use App\Entity\User;
use App\Entity\Agency;
use App\Entity\Client;
use App\Repository\UserRepository;
use App\Repository\AgencyRepository;
use App\Repository\ClientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Psr\Log\LoggerInterface;

#[Route('/api/v1/user-profile')]
class UserProfileController extends AbstractController
{
    public function __construct(
        private UserRepository $userRepository,
        private AgencyRepository $agencyRepository,
        private ClientRepository $clientRepository,
        private EntityManagerInterface $entityManager,
        private LoggerInterface $logger
    ) {}

    #[Route('/greeting', name: 'api_v1_user_profile_greeting', methods: ['GET'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function getUserGreeting(): JsonResponse
    {
        try {
            $currentUser = $this->getUser();
            
            if (!$currentUser instanceof User) {
                $this->logger->error('User profile greeting failed - invalid user type', [
                    'user_type' => get_class($currentUser)
                ]);
                return $this->json([
                    'error' => 'Invalid user type',
                    'status_code' => Response::HTTP_INTERNAL_SERVER_ERROR
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            // Get user's agency
            $agency = $currentUser->getAgency();
            if (!$agency) {
                $this->logger->warning('User profile greeting failed - user has no agency', [
                    'user_id' => $currentUser->getId(),
                    'email' => $currentUser->getEmail()
                ]);
                return $this->json([
                    'error' => 'User has no associated agency',
                    'status_code' => Response::HTTP_NOT_FOUND
                ], Response::HTTP_NOT_FOUND);
            }

            // Get client information if user is a client user
            $client = null;
            if ($currentUser->isClientUser() && $currentUser->getClientId()) {
                $client = $this->clientRepository->find($currentUser->getClientId());
            }

            // Build response data
            $responseData = [
                'user' => [
                    'id' => $currentUser->getId(),
                    'email' => $currentUser->getEmail(),
                    'firstName' => $currentUser->getFirstName(),
                    'lastName' => $currentUser->getLastName(),
                    'name' => $currentUser->getName(),
                    'role' => $currentUser->getRole(),
                    'status' => $currentUser->getStatus(),
                    'lastLoginAt' => $currentUser->getLastLoginAt()?->format('c')
                ],
                'agency' => [
                    'id' => $agency->getId(),
                    'name' => $agency->getName(),
                    'domain' => $agency->getDomain(),
                    'description' => $agency->getDescription()
                ],
                'client' => $client ? [
                    'id' => $client->getId(),
                    'name' => $client->getName(),
                    'slug' => $client->getSlug(),
                    'description' => $client->getDescription(),
                    'status' => $client->getStatus()
                ] : null,
                'greeting' => [
                    'displayName' => $this->getDisplayName($currentUser),
                    'organizationName' => $agency->getName(),
                    'userRole' => $this->getUserRoleDisplay($currentUser->getRole()),
                    'timeBasedGreeting' => $this->getTimeBasedGreeting()
                ]
            ];

            $this->logger->info('User profile greeting retrieved successfully', [
                'user_id' => $currentUser->getId(),
                'email' => $currentUser->getEmail(),
                'agency_id' => $agency->getId()
            ]);

            return $this->json($responseData);

        } catch (AccessDeniedException $e) {
            $this->logger->warning('User profile greeting failed - access denied', [
                'exception' => $e->getMessage()
            ]);
            return $this->json([
                'error' => 'Access denied',
                'status_code' => Response::HTTP_FORBIDDEN
            ], Response::HTTP_FORBIDDEN);
        } catch (\Exception $e) {
            $this->logger->error('User profile greeting failed - internal error', [
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return $this->json([
                'error' => 'Internal server error',
                'status_code' => Response::HTTP_INTERNAL_SERVER_ERROR
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get display name for user (preferred name or fallback to email)
     */
    private function getDisplayName(User $user): string
    {
        $name = $user->getName();
        if ($name && trim($name)) {
            return $name;
        }

        // Fallback to email prefix
        $emailParts = explode('@', $user->getEmail());
        return ucfirst($emailParts[0]);
    }

    /**
     * Get human-readable role display name
     */
    private function getUserRoleDisplay(string $role): string
    {
        return match ($role) {
            User::ROLE_SYSTEM_ADMIN => 'System Administrator',
            User::ROLE_AGENCY_ADMIN => 'Agency Administrator',
            User::ROLE_CLIENT_USER => 'Client User',
            User::ROLE_READ_ONLY => 'Read Only User',
            default => 'User'
        };
    }

    /**
     * Get time-based greeting based on current hour
     */
    private function getTimeBasedGreeting(): string
    {
        $hour = (int) (new \DateTime())->format('H');
        
        if ($hour >= 5 && $hour < 12) {
            return 'Good morning';
        } elseif ($hour >= 12 && $hour < 17) {
            return 'Good afternoon';
        } elseif ($hour >= 17 && $hour < 21) {
            return 'Good evening';
        } else {
            return 'Good night';
        }
    }
}
