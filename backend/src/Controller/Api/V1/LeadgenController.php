<?php

namespace App\Controller\Api\V1;

use App\Service\LeadgenExecutionService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[Route('/api/v1/admin/leadgen')]
#[IsGranted('ROLE_SYSTEM_ADMIN')]
class LeadgenController extends AbstractController
{
    public function __construct(
        private LeadgenExecutionService $leadgenExecutionService,
        private ValidatorInterface $validator
    ) {}

    #[Route('/execute', name: 'api_v1_admin_leadgen_execute', methods: ['POST'])]
    public function executeCampaign(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            
            if (!$data) {
                return $this->json(['error' => 'Invalid JSON'], Response::HTTP_BAD_REQUEST);
            }

            // Validate input
            $constraints = new Assert\Collection([
                'name' => [new Assert\NotBlank()],
                'vertical' => [new Assert\NotBlank(), new Assert\Choice(['local_services', 'b2b_saas', 'ecommerce', 'healthcare', 'real_estate', 'other'])],
                'geo' => [
                    new Assert\NotBlank(),
                    new Assert\Collection([
                        'city' => [new Assert\NotBlank()],
                        'region' => [new Assert\NotBlank()],
                        'country' => [new Assert\Optional([new Assert\NotBlank()])],
                        'radius_km' => [new Assert\Optional([new Assert\PositiveOrZero()])]
                    ])
                ],
                'filters' => [new Assert\Optional([
                    new Assert\Collection([
                        'min_rating' => [new Assert\Optional([new Assert\Range(['min' => 0, 'max' => 5])])],
                        'keywords' => [new Assert\Optional([new Assert\Type('array')])],
                        'exclude_keywords' => [new Assert\Optional([new Assert\Type('array')])],
                        'max_results' => [new Assert\Optional([new Assert\PositiveOrZero()])]
                    ])
                ])],
                'sources' => [new Assert\Optional([new Assert\Type('array')])],
                'enrichment' => [new Assert\Optional([new Assert\Type('array')])],
                'budget' => [new Assert\Optional([
                    new Assert\Collection([
                        'max_cost_usd' => [new Assert\Optional([new Assert\PositiveOrZero()])]
                    ])
                ])],
                'schedule' => [new Assert\Optional([
                    new Assert\Collection([
                        'enabled' => [new Assert\Optional([new Assert\Type('boolean')])]
                    ])
                ])],
                'client_id' => [new Assert\Optional([new Assert\Uuid()])]
            ]);

            $violations = $this->validator->validate($data, $constraints);
            if (count($violations) > 0) {
                $errors = [];
                foreach ($violations as $violation) {
                    $errors[$violation->getPropertyPath()] = $violation->getMessage();
                }
                return $this->json(['error' => 'Validation failed', 'details' => $errors], Response::HTTP_BAD_REQUEST);
            }

            // Generate campaign ID
            $data['campaign_id'] = 'campaign_' . uniqid();

            $result = $this->leadgenExecutionService->executeCampaign($data);

            return $this->json([
                'message' => 'Leadgen campaign executed successfully',
                'result' => $result
            ], Response::HTTP_CREATED);

        } catch (\Exception $e) {
            return $this->json(['error' => 'Campaign execution failed: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/verticals', name: 'api_v1_admin_leadgen_verticals', methods: ['GET'])]
    public function getVerticals(): JsonResponse
    {
        $verticals = $this->leadgenExecutionService->getAvailableVerticals();
        return $this->json(['verticals' => $verticals]);
    }

    #[Route('/sources', name: 'api_v1_admin_leadgen_sources', methods: ['GET'])]
    public function getSources(): JsonResponse
    {
        $sources = $this->leadgenExecutionService->getAvailableSources();
        return $this->json(['sources' => $sources]);
    }

    #[Route('/status/{campaignId}', name: 'api_v1_admin_leadgen_status', methods: ['GET'])]
    public function getCampaignStatus(string $campaignId): JsonResponse
    {
        try {
            $status = $this->leadgenExecutionService->getCampaignStatus($campaignId);
            return $this->json(['status' => $status]);
        } catch (\Exception $e) {
            return $this->json(['error' => 'Failed to get campaign status: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/template', name: 'api_v1_admin_leadgen_template', methods: ['GET'])]
    public function getCampaignTemplate(): JsonResponse
    {
        $template = [
            'name' => 'Sample Campaign',
            'vertical' => 'local_services',
            'geo' => [
                'city' => 'New York',
                'region' => 'NY',
                'country' => 'US',
                'radius_km' => 30
            ],
            'filters' => [
                'min_rating' => 3.0,
                'keywords' => ['attorney', 'lawyer'],
                'exclude_keywords' => ['criminal'],
                'max_results' => 100
            ],
            'sources' => ['google_places'],
            'enrichment' => [],
            'budget' => [
                'max_cost_usd' => 25
            ],
            'schedule' => [
                'enabled' => false
            ]
        ];

        return $this->json(['template' => $template]);
    }
}
