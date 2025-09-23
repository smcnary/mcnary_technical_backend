<?php

namespace App\Service;

use App\Entity\Notification;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class NotificationService
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
    }

    /**
     * Create a new notification for a user
     */
    public function createNotification(
        User $user,
        string $title,
        ?string $message = null,
        string $type = 'info',
        ?string $actionUrl = null,
        ?string $actionLabel = null,
        ?array $metadata = null
    ): Notification {
        $notification = new Notification();
        $notification->setUser($user);
        $notification->setTitle($title);
        $notification->setMessage($message);
        $notification->setType($type);
        $notification->setActionUrl($actionUrl);
        $notification->setActionLabel($actionLabel);
        $notification->setMetadata($metadata);

        $this->entityManager->persist($notification);
        $this->entityManager->flush();

        return $notification;
    }

    /**
     * Create a notification for multiple users
     */
    public function createNotificationForUsers(
        array $users,
        string $title,
        ?string $message = null,
        string $type = 'info',
        ?string $actionUrl = null,
        ?string $actionLabel = null,
        ?array $metadata = null
    ): array {
        $notifications = [];
        
        foreach ($users as $user) {
            $notifications[] = $this->createNotification(
                $user,
                $title,
                $message,
                $type,
                $actionUrl,
                $actionLabel,
                $metadata
            );
        }

        return $notifications;
    }

    /**
     * Create a lead-related notification
     */
    public function createLeadNotification(
        User $user,
        string $leadName,
        string $action,
        ?string $actionUrl = null
    ): Notification {
        $title = "Lead Update: {$leadName}";
        $message = "Lead {$leadName} has been {$action}";
        
        return $this->createNotification(
            $user,
            $title,
            $message,
            'info',
            $actionUrl,
            'View Lead'
        );
    }

    /**
     * Create a system notification
     */
    public function createSystemNotification(
        User $user,
        string $title,
        string $message,
        string $type = 'info'
    ): Notification {
        return $this->createNotification(
            $user,
            $title,
            $message,
            $type,
            null,
            null,
            ['system' => true]
        );
    }

    /**
     * Create a welcome notification for new users
     */
    public function createWelcomeNotification(User $user): Notification
    {
        return $this->createNotification(
            $user,
            'Welcome to SEO Clients CRM!',
            'Welcome to the SEO Clients CRM system. You can now manage your leads, track campaigns, and monitor your SEO progress.',
            'success',
            '/seo-clients',
            'Get Started'
        );
    }

    /**
     * Create a notification for new lead import
     */
    public function createLeadImportNotification(
        User $user,
        int $importedCount,
        int $skippedCount = 0
    ): Notification {
        $title = 'Lead Import Complete';
        $message = "Successfully imported {$importedCount} leads";
        
        if ($skippedCount > 0) {
            $message .= " and skipped {$skippedCount} duplicates";
        }

        return $this->createNotification(
            $user,
            $title,
            $message,
            'success',
            '/seo-clients',
            'View Leads'
        );
    }

    /**
     * Create a notification for lead status change
     */
    public function createLeadStatusChangeNotification(
        User $user,
        string $leadName,
        string $oldStatus,
        string $newStatus
    ): Notification {
        $title = "Lead Status Updated: {$leadName}";
        $message = "Lead status changed from {$oldStatus} to {$newStatus}";

        return $this->createNotification(
            $user,
            $title,
            $message,
            'info',
            '/seo-clients',
            'View Lead'
        );
    }
}
