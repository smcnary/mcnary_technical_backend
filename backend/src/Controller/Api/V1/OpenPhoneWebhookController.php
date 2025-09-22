<?php

namespace App\Controller\Api\V1;

use App\Service\OpenPhoneApiService;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/v1/openphone/webhooks')]
class OpenPhoneWebhookController extends AbstractController
{
    public function __construct(
        private OpenPhoneApiService $openPhoneApiService,
        private LoggerInterface $logger
    ) {}

    #[Route('/calls', name: 'openphone_call_webhook', methods: ['POST'])]
    public function handleCallWebhook(Request $request): JsonResponse
    {
        try {
            $payload = json_decode($request->getContent(), true);
            
            if (!$payload) {
                $this->logger->warning('Invalid JSON payload in OpenPhone call webhook');
                return $this->json([
                    'success' => false,
                    'error' => 'Invalid JSON payload'
                ], Response::HTTP_BAD_REQUEST);
            }

            // Verify webhook signature if needed (implement based on OpenPhone's webhook verification)
            // $this->verifyWebhookSignature($request, $payload);

            $this->logger->info('Received OpenPhone call webhook', [
                'event' => $payload['event'] ?? 'unknown',
                'callId' => $payload['id'] ?? 'unknown'
            ]);

            // Process the webhook payload
            $this->openPhoneApiService->processCallWebhook($payload);

            return $this->json([
                'success' => true,
                'message' => 'Webhook processed successfully'
            ]);

        } catch (\Exception $e) {
            $this->logger->error('Failed to process OpenPhone call webhook', [
                'error' => $e->getMessage(),
                'payload' => $request->getContent()
            ]);

            return $this->json([
                'success' => false,
                'error' => 'Failed to process webhook',
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/messages', name: 'openphone_message_webhook', methods: ['POST'])]
    public function handleMessageWebhook(Request $request): JsonResponse
    {
        try {
            $payload = json_decode($request->getContent(), true);
            
            if (!$payload) {
                $this->logger->warning('Invalid JSON payload in OpenPhone message webhook');
                return $this->json([
                    'success' => false,
                    'error' => 'Invalid JSON payload'
                ], Response::HTTP_BAD_REQUEST);
            }

            // Verify webhook signature if needed (implement based on OpenPhone's webhook verification)
            // $this->verifyWebhookSignature($request, $payload);

            $this->logger->info('Received OpenPhone message webhook', [
                'event' => $payload['event'] ?? 'unknown',
                'messageId' => $payload['id'] ?? 'unknown'
            ]);

            // Process the webhook payload
            $this->openPhoneApiService->processMessageWebhook($payload);

            return $this->json([
                'success' => true,
                'message' => 'Webhook processed successfully'
            ]);

        } catch (\Exception $e) {
            $this->logger->error('Failed to process OpenPhone message webhook', [
                'error' => $e->getMessage(),
                'payload' => $request->getContent()
            ]);

            return $this->json([
                'success' => false,
                'error' => 'Failed to process webhook',
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/contacts', name: 'openphone_contact_webhook', methods: ['POST'])]
    public function handleContactWebhook(Request $request): JsonResponse
    {
        try {
            $payload = json_decode($request->getContent(), true);
            
            if (!$payload) {
                $this->logger->warning('Invalid JSON payload in OpenPhone contact webhook');
                return $this->json([
                    'success' => false,
                    'error' => 'Invalid JSON payload'
                ], Response::HTTP_BAD_REQUEST);
            }

            $this->logger->info('Received OpenPhone contact webhook', [
                'event' => $payload['event'] ?? 'unknown',
                'contactId' => $payload['id'] ?? 'unknown'
            ]);

            // TODO: Implement contact synchronization logic
            // This would sync OpenPhone contacts with the CRM system

            return $this->json([
                'success' => true,
                'message' => 'Contact webhook processed successfully'
            ]);

        } catch (\Exception $e) {
            $this->logger->error('Failed to process OpenPhone contact webhook', [
                'error' => $e->getMessage(),
                'payload' => $request->getContent()
            ]);

            return $this->json([
                'success' => false,
                'error' => 'Failed to process webhook',
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/status', name: 'openphone_status_webhook', methods: ['POST'])]
    public function handleStatusWebhook(Request $request): JsonResponse
    {
        try {
            $payload = json_decode($request->getContent(), true);
            
            if (!$payload) {
                $this->logger->warning('Invalid JSON payload in OpenPhone status webhook');
                return $this->json([
                    'success' => false,
                    'error' => 'Invalid JSON payload'
                ], Response::HTTP_BAD_REQUEST);
            }

            $this->logger->info('Received OpenPhone status webhook', [
                'event' => $payload['event'] ?? 'unknown',
                'type' => $payload['type'] ?? 'unknown'
            ]);

            // Handle different status events (call status changes, message delivery status, etc.)
            $event = $payload['event'] ?? '';
            
            switch ($event) {
                case 'call.status_changed':
                    $this->openPhoneApiService->processCallWebhook($payload);
                    break;
                case 'message.status_changed':
                    $this->openPhoneApiService->processMessageWebhook($payload);
                    break;
                default:
                    $this->logger->info('Unhandled OpenPhone status event', ['event' => $event]);
            }

            return $this->json([
                'success' => true,
                'message' => 'Status webhook processed successfully'
            ]);

        } catch (\Exception $e) {
            $this->logger->error('Failed to process OpenPhone status webhook', [
                'error' => $e->getMessage(),
                'payload' => $request->getContent()
            ]);

            return $this->json([
                'success' => false,
                'error' => 'Failed to process webhook',
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Verify webhook signature for security
     * This should be implemented based on OpenPhone's webhook verification method
     */
    private function verifyWebhookSignature(Request $request, array $payload): bool
    {
        // TODO: Implement webhook signature verification
        // This would typically involve:
        // 1. Getting the signature from headers
        // 2. Computing HMAC of the payload
        // 3. Comparing signatures
        
        $signature = $request->headers->get('X-OpenPhone-Signature');
        $webhookSecret = $_ENV['OPENPHONE_WEBHOOK_SECRET'] ?? '';
        
        if (!$signature || !$webhookSecret) {
            $this->logger->warning('Missing webhook signature or secret');
            return false;
        }

        // For now, we'll skip verification but log the attempt
        $this->logger->info('Webhook signature verification skipped', [
            'signature' => $signature,
            'hasSecret' => !empty($webhookSecret)
        ]);

        return true;
    }
}
