<?php

namespace App\Controller\Api\V1;

use App\Entity\Subscription;
use App\Repository\SubscriptionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[Route('/api/v1/subscriptions')]
class SubscriptionsController extends AbstractController
{
    public function __construct(
        private SubscriptionRepository $subscriptionRepository,
        private EntityManagerInterface $entityManager,
        private ValidatorInterface $validator
    ) {}

    #[Route('', name: 'api_v1_subscriptions_list', methods: ['GET'])]
    #[IsGranted('ROLE_AGENCY_ADMIN')]
    public function listSubscriptions(Request $request): JsonResponse
    {
        $page = max(1, (int) $request->query->get('page', 1));
        $perPage = min(100, max(1, (int) $request->query->get('per_page', 20)));
        $sort = $request->query->get('sort', 'created_at');
        $clientId = $request->query->get('client_id', '');
        $status = $request->query->get('status', '');

        // Parse sort parameter
        $sortFields = [];
        foreach (explode(',', $sort) as $field) {
            $direction = 'ASC';
            if (str_starts_with($field, '-')) {
                $direction = 'DESC';
                $field = substr($field, 1);
            }
            $sortFields[$field] = $direction;
        }

        // Build criteria
        $criteria = [];
        if ($clientId) {
            $criteria['client_id'] = $clientId;
        }
        if ($status) {
            $criteria['status'] = $status;
        }

        // Get subscriptions with pagination and filtering
        $subscriptions = $this->subscriptionRepository->findByCriteria($criteria, $sortFields, $perPage, ($page - 1) * $perPage);
        $totalSubscriptions = $this->subscriptionRepository->countByCriteria($criteria);

        $subscriptionData = [];
        foreach ($subscriptions as $subscription) {
            $subscriptionData[] = [
                'id' => $subscription->getId(),
                'client_id' => $subscription->getClientId(),
                'package_id' => $subscription->getPackageId(),
                'status' => $subscription->getStatus(),
                'billing_cycle' => $subscription->getBillingCycle(),
                'start_date' => $subscription->getStartDate()?->format('Y-m-d'),
                'next_billing_date' => $subscription->getNextBillingDate()?->format('Y-m-d'),
                'auto_renew' => $subscription->isAutoRenew(),
                'amount' => $subscription->getAmount(),
                'currency' => $subscription->getCurrency(),
                'created_at' => $subscription->getCreatedAt()->format('c'),
                'updated_at' => $subscription->getUpdatedAt()->format('c')
            ];
        }

        return $this->json([
            'data' => $subscriptionData,
            'pagination' => [
                'page' => $page,
                'per_page' => $perPage,
                'total' => $totalSubscriptions,
                'pages' => ceil($totalSubscriptions / $perPage)
            ]
        ]);
    }

    #[Route('', name: 'api_v1_subscriptions_create', methods: ['POST'])]
    #[IsGranted('ROLE_AGENCY_ADMIN')]
    public function createSubscription(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            
            if (!$data) {
                return $this->json(['error' => 'Invalid JSON'], Response::HTTP_BAD_REQUEST);
            }

            // Validate input
            $constraints = new Assert\Collection([
                'client_id' => [new Assert\NotBlank(), new Assert\Uuid()],
                'package_id' => [new Assert\NotBlank(), new Assert\Uuid()],
                'payment_method_id' => [new Assert\NotBlank()],
                'billing_cycle' => [new Assert\Optional([new Assert\Choice(['monthly', 'quarterly', 'yearly'])])],
                'start_date' => [new Assert\Optional([new Assert\Date()])],
                'auto_renew' => [new Assert\Optional([new Assert\Type('boolean')])]
            ]);

            $violations = $this->validator->validate($data, $constraints);
            if (count($violations) > 0) {
                $errors = [];
                foreach ($violations as $violation) {
                    $errors[$violation->getPropertyPath()] = $violation->getMessage();
                }
                return $this->json(['error' => 'Validation failed', 'details' => $errors], Response::HTTP_BAD_REQUEST);
            }

            // Create subscription
            $subscription = new Subscription();
            $subscription->setClientId($data['client_id']);
            $subscription->setPackageId($data['client_id']);
            $subscription->setStatus('active');
            $subscription->setBillingCycle($data['billing_cycle'] ?? 'monthly');
            $subscription->setStartDate(new \DateTimeImmutable($data['start_date'] ?? 'now'));
            $subscription->setAutoRenew($data['auto_renew'] ?? true);

            // Here you would typically integrate with Stripe to create the subscription
            // For now, we'll set some default values
            $subscription->setAmount(0.00);
            $subscription->setCurrency('USD');

            $this->entityManager->persist($subscription);
            $this->entityManager->flush();

            $subscriptionData = [
                'id' => $subscription->getId(),
                'client_id' => $subscription->getClientId(),
                'package_id' => $subscription->getPackageId(),
                'status' => $subscription->getStatus(),
                'billing_cycle' => $subscription->getBillingCycle(),
                'start_date' => $subscription->getStartDate()?->format('Y-m-d'),
                'next_billing_date' => $subscription->getNextBillingDate()?->format('Y-m-d'),
                'auto_renew' => $subscription->isAutoRenew(),
                'amount' => $subscription->getAmount(),
                'currency' => $subscription->getCurrency(),
                'created_at' => $subscription->getCreatedAt()->format('c'),
                'updated_at' => $subscription->getUpdatedAt()->format('c')
            ];

            return $this->json([
                'message' => 'Subscription created successfully',
                'subscription' => $subscriptionData
            ], Response::HTTP_CREATED);

        } catch (\Exception $e) {
            return $this->json(['error' => 'Internal server error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/{id}', name: 'api_v1_subscriptions_get', methods: ['GET'])]
    #[IsGranted('ROLE_AGENCY_ADMIN')]
    public function getSubscription(string $id): JsonResponse
    {
        if (!Uuid::isValid($id)) {
            return $this->json(['error' => 'Invalid UUID'], Response::HTTP_BAD_REQUEST);
        }

        $subscription = $this->subscriptionRepository->find($id);
        if (!$subscription) {
            return $this->json(['error' => 'Subscription not found'], Response::HTTP_NOT_FOUND);
        }

        $subscriptionData = [
            'id' => $subscription->getId(),
            'client_id' => $subscription->getClientId(),
            'package_id' => $subscription->getPackageId(),
            'status' => $subscription->getStatus(),
            'billing_cycle' => $subscription->getBillingCycle(),
            'start_date' => $subscription->getStartDate()?->format('Y-m-d'),
            'next_billing_date' => $subscription->getNextBillingDate()?->format('Y-m-d'),
            'auto_renew' => $subscription->isAutoRenew(),
            'amount' => $subscription->getAmount(),
            'currency' => $subscription->getCurrency(),
            'metadata' => $subscription->getMetadata(),
            'created_at' => $subscription->getCreatedAt()->format('c'),
            'updated_at' => $subscription->getUpdatedAt()->format('c')
        ];

        return $this->json($subscriptionData);
    }
}
