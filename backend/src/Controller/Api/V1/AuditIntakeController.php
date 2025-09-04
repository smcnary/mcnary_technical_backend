<?php

namespace App\Controller\Api\V1;

use App\Service\AuditIntakeValidationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[Route('/api/v1/audit-intakes')]
class AuditIntakeController extends AbstractController
{
    public function __construct(
        private AuditIntakeValidationService $validationService,
        private ValidatorInterface $validator
    ) {}

    /**
     * Validate audit intake data before submission
     * This endpoint can be used by the frontend to check for conflicts
     */
    #[Route('/validate', name: 'api_v1_audit_intakes_validate', methods: ['POST'])]
    public function validateAuditIntake(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            
            if (!$data) {
                return $this->json(['error' => 'Invalid JSON'], Response::HTTP_BAD_REQUEST);
            }

            // Validate input
            $constraints = new Assert\Collection([
                'contact_email' => [new Assert\Optional([new Assert\Email()])],
                'website_url' => [new Assert\Optional([new Assert\Url()])]
            ]);

            $violations = $this->validator->validate($data, $constraints);
            if (count($violations) > 0) {
                $errors = [];
                foreach ($violations as $violation) {
                    $errors[$violation->getPropertyPath()] = $violation->getMessage();
                }
                return $this->json(['error' => 'Validation failed', 'details' => $errors], Response::HTTP_BAD_REQUEST);
            }

            $email = $data['contact_email'] ?? null;
            $websiteUrl = $data['website_url'] ?? null;

            // Check for existing client associations
            $validationResults = $this->validationService->validateAuditIntakeData($email, $websiteUrl);

            return $this->json([
                'valid' => !$validationResults['has_conflicts'],
                'validation_results' => $validationResults
            ]);

        } catch (\Exception $e) {
            return $this->json([
                'error' => 'Internal server error',
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Check if a specific email is already associated with a client
     */
    #[Route('/check-email', name: 'api_v1_audit_intakes_check_email', methods: ['POST'])]
    public function checkEmail(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            
            if (!$data || !isset($data['email'])) {
                return $this->json(['error' => 'Email is required'], Response::HTTP_BAD_REQUEST);
            }

            $email = $data['email'];
            
            // Validate email format
            $constraints = new Assert\Collection([
                'email' => [new Assert\Email()]
            ]);

            $violations = $this->validator->validate(['email' => $email], $constraints);
            if (count($violations) > 0) {
                return $this->json(['error' => 'Invalid email format'], Response::HTTP_BAD_REQUEST);
            }

            $result = $this->validationService->checkEmailExists($email);

            return $this->json([
                'email' => $email,
                'exists' => $result !== null,
                'result' => $result
            ]);

        } catch (\Exception $e) {
            return $this->json([
                'error' => 'Internal server error',
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Check if a specific website URL domain matches an existing client slug
     */
    #[Route('/check-website', name: 'api_v1_audit_intakes_check_website', methods: ['POST'])]
    public function checkWebsite(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            
            if (!$data || !isset($data['website_url'])) {
                return $this->json(['error' => 'Website URL is required'], Response::HTTP_BAD_REQUEST);
            }

            $websiteUrl = $data['website_url'];
            
            // Validate URL format
            $constraints = new Assert\Collection([
                'website_url' => [new Assert\Url()]
            ]);

            $violations = $this->validator->validate(['website_url' => $websiteUrl], $constraints);
            if (count($violations) > 0) {
                return $this->json(['error' => 'Invalid URL format'], Response::HTTP_BAD_REQUEST);
            }

            $result = $this->validationService->checkWebsiteExists($websiteUrl);

            return $this->json([
                'website_url' => $websiteUrl,
                'exists' => $result !== null,
                'result' => $result
            ]);

        } catch (\Exception $e) {
            return $this->json([
                'error' => 'Internal server error',
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
