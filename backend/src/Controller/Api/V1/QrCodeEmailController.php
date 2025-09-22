<?php

namespace App\Controller\Api\V1;

use App\Service\QrCodeEmailService;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Psr\Log\LoggerInterface;

#[Route('/api/v1/qr-emails', name: 'api_v1_qr_emails_')]
class QrCodeEmailController extends AbstractController
{
    public function __construct(
        private QrCodeEmailService $qrCodeEmailService,
        private ValidatorInterface $validator,
        private LoggerInterface $logger
    ) {}

    /**
     * Send QR code email to audit wizard
     */
    #[Route('/send-audit-wizard', name: 'send_audit_wizard', methods: ['POST'])]
    #[IsGranted('ROLE_SYSTEM_ADMIN or ROLE_SALES_CONSULTANT')]
    public function sendAuditWizardQrEmail(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            
            if (!$data) {
                return $this->json(['error' => 'Invalid JSON'], Response::HTTP_BAD_REQUEST);
            }

            // Validate input
            $constraints = new Assert\Collection([
                'recipient_email' => [new Assert\NotBlank(), new Assert\Email()],
                'recipient_name' => [new Assert\Optional([new Assert\NotBlank()])],
                'custom_data' => [new Assert\Optional([new Assert\Type('array')])],
                'sender_email' => [new Assert\Optional([new Assert\Email()])],
                'sender_name' => [new Assert\Optional([new Assert\NotBlank()])]
            ]);

            $violations = $this->validator->validate($data, $constraints);
            if (count($violations) > 0) {
                $errors = [];
                foreach ($violations as $violation) {
                    $errors[$violation->getPropertyPath()] = $violation->getMessage();
                }
                return $this->json(['error' => 'Validation failed', 'details' => $errors], Response::HTTP_BAD_REQUEST);
            }

            // Get current user for logging
            /** @var User $user */
            $user = $this->getUser();

            // Send QR code email
            $success = $this->qrCodeEmailService->sendAuditWizardQrEmail(
                $data['recipient_email'],
                $data['recipient_name'] ?? null,
                $data['custom_data'] ?? [],
                $data['sender_email'] ?? null,
                $data['sender_name'] ?? null
            );

            if ($success) {
                $this->logger->info('QR code email sent via API', [
                    'recipient' => $data['recipient_email'],
                    'sent_by' => $user->getEmail(),
                    'user_role' => $user->getRole()
                ]);

                return $this->json([
                    'success' => true,
                    'message' => 'QR code email sent successfully',
                    'recipient' => $data['recipient_email']
                ]);
            } else {
                return $this->json([
                    'success' => false,
                    'error' => 'Failed to send QR code email'
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }

        } catch (\Exception $e) {
            $this->logger->error('Error sending QR code email via API', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->json([
                'success' => false,
                'error' => 'An error occurred while sending the email'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Send bulk QR code emails to audit wizard
     */
    #[Route('/send-bulk-audit-wizard', name: 'send_bulk_audit_wizard', methods: ['POST'])]
    #[IsGranted('ROLE_SYSTEM_ADMIN or ROLE_SALES_CONSULTANT')]
    public function sendBulkAuditWizardQrEmails(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            
            if (!$data) {
                return $this->json(['error' => 'Invalid JSON'], Response::HTTP_BAD_REQUEST);
            }

            // Validate input
            $constraints = new Assert\Collection([
                'recipients' => [
                    new Assert\NotBlank(),
                    new Assert\Type('array'),
                    new Assert\Count(['min' => 1, 'max' => 100]) // Limit bulk emails
                ],
                'custom_data' => [new Assert\Optional([new Assert\Type('array')])],
                'sender_email' => [new Assert\Optional([new Assert\Email()])],
                'sender_name' => [new Assert\Optional([new Assert\NotBlank()])]
            ]);

            $violations = $this->validator->validate($data, $constraints);
            if (count($violations) > 0) {
                $errors = [];
                foreach ($violations as $violation) {
                    $errors[$violation->getPropertyPath()] = $violation->getMessage();
                }
                return $this->json(['error' => 'Validation failed', 'details' => $errors], Response::HTTP_BAD_REQUEST);
            }

            // Validate each recipient
            $recipientConstraints = new Assert\Collection([
                'email' => [new Assert\NotBlank(), new Assert\Email()],
                'name' => [new Assert\Optional([new Assert\NotBlank()])]
            ]);

            foreach ($data['recipients'] as $index => $recipient) {
                $recipientViolations = $this->validator->validate($recipient, $recipientConstraints);
                if (count($recipientViolations) > 0) {
                    return $this->json([
                        'error' => "Validation failed for recipient at index {$index}",
                        'details' => (string) $recipientViolations
                    ], Response::HTTP_BAD_REQUEST);
                }
            }

            // Get current user for logging
            /** @var User $user */
            $user = $this->getUser();

            // Send bulk QR code emails
            $results = $this->qrCodeEmailService->sendBulkAuditWizardQrEmails(
                $data['recipients'],
                $data['custom_data'] ?? []
            );

            $this->logger->info('Bulk QR code emails sent via API', [
                'total_recipients' => count($data['recipients']),
                'successful' => count($results['success']),
                'failed' => count($results['failed']),
                'sent_by' => $user->getEmail(),
                'user_role' => $user->getRole()
            ]);

            return $this->json([
                'success' => true,
                'message' => 'Bulk QR code emails processed',
                'results' => [
                    'total' => count($data['recipients']),
                    'successful' => count($results['success']),
                    'failed' => count($results['failed']),
                    'successful_emails' => $results['success'],
                    'failed_details' => $results['failed']
                ]
            ]);

        } catch (\Exception $e) {
            $this->logger->error('Error sending bulk QR code emails via API', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->json([
                'success' => false,
                'error' => 'An error occurred while sending the bulk emails'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Generate QR code for audit wizard (for testing/preview)
     */
    #[Route('/generate-qr-code', name: 'generate_qr_code', methods: ['POST'])]
    #[IsGranted('ROLE_SYSTEM_ADMIN or ROLE_SALES_CONSULTANT')]
    public function generateQrCode(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            
            if (!$data) {
                return $this->json(['error' => 'Invalid JSON'], Response::HTTP_BAD_REQUEST);
            }

            // Validate input
            $constraints = new Assert\Collection([
                'custom_data' => [new Assert\Optional([new Assert\Type('array')])]
            ]);

            $violations = $this->validator->validate($data, $constraints);
            if (count($violations) > 0) {
                $errors = [];
                foreach ($violations as $violation) {
                    $errors[$violation->getPropertyPath()] = $violation->getMessage();
                }
                return $this->json(['error' => 'Validation failed', 'details' => $errors], Response::HTTP_BAD_REQUEST);
            }

            // Generate audit wizard URL
            $baseUrl = $this->getParameter('app_frontend_url');
            $auditWizardPath = '/audit-wizard';
            
            if (!empty($data['custom_data'])) {
                $queryParams = http_build_query($data['custom_data']);
                $auditWizardPath .= '?' . $queryParams;
            }
            
            $auditWizardUrl = rtrim($baseUrl, '/') . $auditWizardPath;
            
            // Generate QR code
            $qrCodeDataUri = $this->qrCodeEmailService->generateQrCode($auditWizardUrl);

            return $this->json([
                'success' => true,
                'qr_code_data_uri' => $qrCodeDataUri,
                'audit_wizard_url' => $auditWizardUrl
            ]);

        } catch (\Exception $e) {
            $this->logger->error('Error generating QR code via API', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->json([
                'success' => false,
                'error' => 'An error occurred while generating the QR code'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
