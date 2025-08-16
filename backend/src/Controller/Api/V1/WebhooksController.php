<?php

namespace App\Controller\Api\V1;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/v1/webhooks')]
class WebhooksController extends AbstractController
{
    #[Route('/stripe', name: 'api_v1_webhooks_stripe', methods: ['POST'])]
    public function stripeWebhook(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            
            if (!$data) {
                return $this->json(['error' => 'Invalid JSON'], Response::HTTP_BAD_REQUEST);
            }

            // Validate webhook signature (you should implement this)
            // $signature = $request->headers->get('Stripe-Signature');
            // if (!$this->verifyStripeSignature($request->getContent(), $signature)) {
            //     return $this->json(['error' => 'Invalid signature'], Response::HTTP_UNAUTHORIZED);
            // }

            $eventType = $data['type'] ?? '';
            $eventData = $data['data'] ?? [];

            // Process different webhook events
            switch ($eventType) {
                case 'invoice.payment_succeeded':
                    $this->handleInvoicePaymentSucceeded($eventData);
                    break;
                    
                case 'invoice.payment_failed':
                    $this->handleInvoicePaymentFailed($eventData);
                    break;
                    
                case 'customer.subscription.created':
                    $this->handleSubscriptionCreated($eventData);
                    break;
                    
                case 'customer.subscription.updated':
                    $this->handleSubscriptionUpdated($eventData);
                    break;
                    
                case 'customer.subscription.deleted':
                    $this->handleSubscriptionDeleted($eventData);
                    break;
                    
                default:
                    // Log unknown event type
                    $this->logUnknownEvent($eventType, $eventData);
            }

            return $this->json(['status' => 'success']);

        } catch (\Exception $e) {
            // Log the error and return 200 to acknowledge receipt
            // Stripe will retry failed webhooks
            $this->logWebhookError($e->getMessage());
            return $this->json(['status' => 'error', 'message' => $e->getMessage()], Response::HTTP_OK);
        }
    }

    private function handleInvoicePaymentSucceeded(array $eventData): void
    {
        $invoice = $eventData['object'] ?? [];
        $invoiceId = $invoice['id'] ?? '';
        $subscriptionId = $invoice['subscription'] ?? '';
        $amount = $invoice['amount_paid'] ?? 0;
        $currency = $invoice['currency'] ?? 'usd';

        // Update invoice status in your database
        // $this->invoiceService->markAsPaid($invoiceId, $amount, $currency);
        
        // Log the successful payment
        $this->logWebhookEvent('invoice.payment_succeeded', [
            'invoice_id' => $invoiceId,
            'subscription_id' => $subscriptionId,
            'amount' => $amount,
            'currency' => $currency
        ]);
    }

    private function handleInvoicePaymentFailed(array $eventData): void
    {
        $invoice = $eventData['object'] ?? [];
        $invoiceId = $invoice['id'] ?? '';
        $subscriptionId = $invoice['subscription'] ?? '';
        $attemptCount = $invoice['attempt_count'] ?? 0;

        // Update invoice status in your database
        // $this->invoiceService->markAsFailed($invoiceId, $attemptCount);
        
        // Log the failed payment
        $this->logWebhookEvent('invoice.payment_failed', [
            'invoice_id' => $invoiceId,
            'subscription_id' => $subscriptionId,
            'attempt_count' => $attemptCount
        ]);
    }

    private function handleSubscriptionCreated(array $eventData): void
    {
        $subscription = $eventData['object'] ?? [];
        $subscriptionId = $subscription['id'] ?? '';
        $customerId = $subscription['customer'] ?? '';
        $status = $subscription['status'] ?? '';

        // Update subscription status in your database
        // $this->subscriptionService->updateStatus($subscriptionId, $status);
        
        // Log the subscription creation
        $this->logWebhookEvent('customer.subscription.created', [
            'subscription_id' => $subscriptionId,
            'customer_id' => $customerId,
            'status' => $status
        ]);
    }

    private function handleSubscriptionUpdated(array $eventData): void
    {
        $subscription = $eventData['object'] ?? [];
        $subscriptionId = $subscription['id'] ?? '';
        $status = $subscription['status'] ?? '';
        $currentPeriodEnd = $subscription['current_period_end'] ?? null;

        // Update subscription in your database
        // $this->subscriptionService->updateSubscription($subscriptionId, $status, $currentPeriodEnd);
        
        // Log the subscription update
        $this->logWebhookEvent('customer.subscription.updated', [
            'subscription_id' => $subscriptionId,
            'status' => $status,
            'current_period_end' => $currentPeriodEnd
        ]);
    }

    private function handleSubscriptionDeleted(array $eventData): void
    {
        $subscription = $eventData['object'] ?? [];
        $subscriptionId = $subscription['id'] ?? '';
        $status = $subscription['status'] ?? '';

        // Update subscription status in your database
        // $this->subscriptionService->markAsCancelled($subscriptionId);
        
        // Log the subscription deletion
        $this->logWebhookEvent('customer.subscription.deleted', [
            'subscription_id' => $subscriptionId,
            'status' => $status
        ]);
    }

    private function logUnknownEvent(string $eventType, array $eventData): void
    {
        // Log unknown event types for monitoring
        $this->logWebhookEvent('unknown_event', [
            'event_type' => $eventType,
            'event_data' => $eventData
        ]);
    }

    private function logWebhookEvent(string $eventType, array $data): void
    {
        // Implement your logging logic here
        // You might want to use Symfony's LoggerInterface or write to a database
        error_log("Stripe Webhook: {$eventType} - " . json_encode($data));
    }

    private function logWebhookError(string $error): void
    {
        // Implement your error logging logic here
        error_log("Stripe Webhook Error: {$error}");
    }

    // private function verifyStripeSignature(string $payload, string $signature): bool
    // {
    //     // Implement Stripe webhook signature verification
    //     // This is important for security in production
    //     return true; // Placeholder
    // }
}
