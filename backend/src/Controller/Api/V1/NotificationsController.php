<?php

namespace App\Controller\Api\V1;

use App\Entity\Notification;
use App\Entity\User;
use App\Repository\NotificationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/v1/notifications')]
#[IsGranted('IS_AUTHENTICATED_FULLY')]
class NotificationsController extends AbstractController
{
    public function __construct(
        private NotificationRepository $notificationRepository,
        private EntityManagerInterface $entityManager,
        private SerializerInterface $serializer
    ) {
    }

    #[Route('', name: 'api_v1_notifications_list', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        
        $page = (int) $request->query->get('page', 1);
        $limit = (int) $request->query->get('limit', 20);
        $unreadOnly = $request->query->getBoolean('unread_only', false);
        
        $offset = ($page - 1) * $limit;
        
        $notifications = $this->notificationRepository->findByUser(
            $user,
            $unreadOnly,
            $limit,
            $offset
        );
        
        $totalCount = $this->notificationRepository->countByUser($user, $unreadOnly);
        $unreadCount = $this->notificationRepository->countUnreadByUser($user);
        
        $data = [
            'notifications' => $notifications,
            'pagination' => [
                'page' => $page,
                'limit' => $limit,
                'total' => $totalCount,
                'pages' => ceil($totalCount / $limit)
            ],
            'unread_count' => $unreadCount
        ];
        
        return $this->json($data, 200, [], ['groups' => ['notification:read']]);
    }

    #[Route('/{id}', name: 'api_v1_notifications_show', methods: ['GET'])]
    public function show(Notification $notification): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        
        // Check if user owns this notification
        if ($notification->getUser()->getId() !== $user->getId()) {
            return $this->json(['error' => 'Access denied'], 403);
        }
        
        return $this->json($notification, 200, [], ['groups' => ['notification:read']]);
    }

    #[Route('/{id}/read', name: 'api_v1_notifications_mark_read', methods: ['PATCH'])]
    public function markAsRead(Notification $notification): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        
        // Check if user owns this notification
        if ($notification->getUser()->getId() !== $user->getId()) {
            return $this->json(['error' => 'Access denied'], 403);
        }
        
        $notification->setIsRead(true);
        $this->entityManager->flush();
        
        return $this->json(['message' => 'Notification marked as read'], 200);
    }

    #[Route('/{id}/unread', name: 'api_v1_notifications_mark_unread', methods: ['PATCH'])]
    public function markAsUnread(Notification $notification): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        
        // Check if user owns this notification
        if ($notification->getUser()->getId() !== $user->getId()) {
            return $this->json(['error' => 'Access denied'], 403);
        }
        
        $notification->setIsRead(false);
        $this->entityManager->flush();
        
        return $this->json(['message' => 'Notification marked as unread'], 200);
    }

    #[Route('/mark-all-read', name: 'api_v1_notifications_mark_all_read', methods: ['PATCH'])]
    public function markAllAsRead(): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        
        $this->notificationRepository->markAllAsReadForUser($user);
        $this->entityManager->flush();
        
        return $this->json(['message' => 'All notifications marked as read'], 200);
    }

    #[Route('/{id}', name: 'api_v1_notifications_delete', methods: ['DELETE'])]
    public function delete(Notification $notification): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        
        // Check if user owns this notification
        if ($notification->getUser()->getId() !== $user->getId()) {
            return $this->json(['error' => 'Access denied'], 403);
        }
        
        $this->entityManager->remove($notification);
        $this->entityManager->flush();
        
        return $this->json(['message' => 'Notification deleted'], 200);
    }

    #[Route('/count', name: 'api_v1_notifications_count', methods: ['GET'])]
    public function count(): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        
        $unreadCount = $this->notificationRepository->countUnreadByUser($user);
        $totalCount = $this->notificationRepository->countByUser($user, false);
        
        return $this->json([
            'unread_count' => $unreadCount,
            'total_count' => $totalCount
        ]);
    }
}
