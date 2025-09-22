<?php

namespace App\Controller\Api\V1;

use App\Entity\Client;
use App\Entity\OpenPhoneIntegration;
use App\Entity\OpenPhoneCallLog;
use App\Entity\OpenPhoneMessageLog;
use App\Service\OpenPhoneApiService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[Route('/api/v1/openphone')]
#[IsGranted('ROLE_AGENCY_ADMIN')]
class OpenPhoneController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private OpenPhoneApiService $openPhoneApiService,
        private ValidatorInterface $validator
    ) {}

    #[Route('/phone-numbers', name: 'openphone_phone_numbers', methods: ['GET'])]
    public function getPhoneNumbers(): JsonResponse
    {
        try {
            $phoneNumbers = $this->openPhoneApiService->getPhoneNumbers();
            
            return $this->json([
                'success' => true,
                'data' => $phoneNumbers
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'error' => 'Failed to fetch phone numbers',
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/integrations', name: 'openphone_integrations', methods: ['GET'])]
    public function getIntegrations(): JsonResponse
    {
        try {
            $integrations = $this->entityManager->getRepository(OpenPhoneIntegration::class)
                ->findAll();
            
            $data = array_map(function (OpenPhoneIntegration $integration) {
                return [
                    'id' => $integration->getId(),
                    'clientId' => $integration->getClient()->getId(),
                    'clientName' => $integration->getClient()->getName(),
                    'phoneNumber' => $integration->getPhoneNumber(),
                    'displayName' => $integration->getDisplayName(),
                    'status' => $integration->getStatus(),
                    'isDefault' => $integration->isDefault(),
                    'autoLogCalls' => $integration->isAutoLogCalls(),
                    'autoLogMessages' => $integration->isAutoLogMessages(),
                    'syncContacts' => $integration->isSyncContacts(),
                    'createdAt' => $integration->getCreatedAt()->format('c'),
                    'updatedAt' => $integration->getUpdatedAt()->format('c')
                ];
            }, $integrations);
            
            return $this->json([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'error' => 'Failed to fetch integrations',
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/integrations', name: 'openphone_create_integration', methods: ['POST'])]
    public function createIntegration(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            
            if (!$data) {
                return $this->json([
                    'success' => false,
                    'error' => 'Invalid JSON'
                ], Response::HTTP_BAD_REQUEST);
            }

            // Validate input
            $constraints = new Assert\Collection([
                'clientId' => [new Assert\NotBlank(), new Assert\Uuid()],
                'phoneNumber' => [new Assert\NotBlank()],
                'displayName' => [new Assert\Optional([new Assert\Type('string')])],
                'isDefault' => [new Assert\Optional([new Assert\Type('boolean')])],
                'autoLogCalls' => [new Assert\Optional([new Assert\Type('boolean')])],
                'autoLogMessages' => [new Assert\Optional([new Assert\Type('boolean')])],
                'syncContacts' => [new Assert\Optional([new Assert\Type('boolean')])]
            ]);

            $violations = $this->validator->validate($data, $constraints);
            if (count($violations) > 0) {
                $errors = [];
                foreach ($violations as $violation) {
                    $errors[$violation->getPropertyPath()] = $violation->getMessage();
                }
                return $this->json([
                    'success' => false,
                    'error' => 'Validation failed',
                    'details' => $errors
                ], Response::HTTP_BAD_REQUEST);
            }

            // Find client
            $client = $this->entityManager->getRepository(Client::class)
                ->find($data['clientId']);
            
            if (!$client) {
                return $this->json([
                    'success' => false,
                    'error' => 'Client not found'
                ], Response::HTTP_NOT_FOUND);
            }

            // Check if phone number is already integrated
            $existingIntegration = $this->entityManager->getRepository(OpenPhoneIntegration::class)
                ->findOneBy(['phoneNumber' => $data['phoneNumber']]);
            
            if ($existingIntegration) {
                return $this->json([
                    'success' => false,
                    'error' => 'Phone number is already integrated'
                ], Response::HTTP_CONFLICT);
            }

            // Create integration
            $integration = new OpenPhoneIntegration($client, $data['phoneNumber']);
            $integration->setDisplayName($data['displayName'] ?? null);
            $integration->setIsDefault($data['isDefault'] ?? false);
            $integration->setAutoLogCalls($data['autoLogCalls'] ?? true);
            $integration->setAutoLogMessages($data['autoLogMessages'] ?? true);
            $integration->setSyncContacts($data['syncContacts'] ?? false);

            $this->entityManager->persist($integration);
            $this->entityManager->flush();

            return $this->json([
                'success' => true,
                'data' => [
                    'id' => $integration->getId(),
                    'clientId' => $integration->getClient()->getId(),
                    'phoneNumber' => $integration->getPhoneNumber(),
                    'displayName' => $integration->getDisplayName(),
                    'status' => $integration->getStatus(),
                    'isDefault' => $integration->isDefault(),
                    'autoLogCalls' => $integration->isAutoLogCalls(),
                    'autoLogMessages' => $integration->isAutoLogMessages(),
                    'syncContacts' => $integration->isSyncContacts(),
                    'createdAt' => $integration->getCreatedAt()->format('c'),
                    'updatedAt' => $integration->getUpdatedAt()->format('c')
                ]
            ], Response::HTTP_CREATED);

        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'error' => 'Failed to create integration',
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/integrations/{id}', name: 'openphone_update_integration', methods: ['PUT'])]
    public function updateIntegration(string $id, Request $request): JsonResponse
    {
        try {
            $integration = $this->entityManager->getRepository(OpenPhoneIntegration::class)
                ->find($id);
            
            if (!$integration) {
                return $this->json([
                    'success' => false,
                    'error' => 'Integration not found'
                ], Response::HTTP_NOT_FOUND);
            }

            $data = json_decode($request->getContent(), true);
            
            if (!$data) {
                return $this->json([
                    'success' => false,
                    'error' => 'Invalid JSON'
                ], Response::HTTP_BAD_REQUEST);
            }

            // Update fields
            if (isset($data['displayName'])) {
                $integration->setDisplayName($data['displayName']);
            }
            if (isset($data['isDefault'])) {
                $integration->setIsDefault($data['isDefault']);
            }
            if (isset($data['autoLogCalls'])) {
                $integration->setAutoLogCalls($data['autoLogCalls']);
            }
            if (isset($data['autoLogMessages'])) {
                $integration->setAutoLogMessages($data['autoLogMessages']);
            }
            if (isset($data['syncContacts'])) {
                $integration->setSyncContacts($data['syncContacts']);
            }
            if (isset($data['status'])) {
                $integration->setStatus($data['status']);
            }

            $this->entityManager->flush();

            return $this->json([
                'success' => true,
                'data' => [
                    'id' => $integration->getId(),
                    'clientId' => $integration->getClient()->getId(),
                    'phoneNumber' => $integration->getPhoneNumber(),
                    'displayName' => $integration->getDisplayName(),
                    'status' => $integration->getStatus(),
                    'isDefault' => $integration->isDefault(),
                    'autoLogCalls' => $integration->isAutoLogCalls(),
                    'autoLogMessages' => $integration->isAutoLogMessages(),
                    'syncContacts' => $integration->isSyncContacts(),
                    'createdAt' => $integration->getCreatedAt()->format('c'),
                    'updatedAt' => $integration->getUpdatedAt()->format('c')
                ]
            ]);

        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'error' => 'Failed to update integration',
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/integrations/{id}', name: 'openphone_delete_integration', methods: ['DELETE'])]
    public function deleteIntegration(string $id): JsonResponse
    {
        try {
            $integration = $this->entityManager->getRepository(OpenPhoneIntegration::class)
                ->find($id);
            
            if (!$integration) {
                return $this->json([
                    'success' => false,
                    'error' => 'Integration not found'
                ], Response::HTTP_NOT_FOUND);
            }

            $this->entityManager->remove($integration);
            $this->entityManager->flush();

            return $this->json([
                'success' => true,
                'message' => 'Integration deleted successfully'
            ]);

        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'error' => 'Failed to delete integration',
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/calls', name: 'openphone_make_call', methods: ['POST'])]
    public function makeCall(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            
            if (!$data) {
                return $this->json([
                    'success' => false,
                    'error' => 'Invalid JSON'
                ], Response::HTTP_BAD_REQUEST);
            }

            // Validate input
            $constraints = new Assert\Collection([
                'phoneNumberId' => [new Assert\NotBlank()],
                'toNumber' => [new Assert\NotBlank()],
                'fromNumber' => [new Assert\Optional([new Assert\Type('string')])]
            ]);

            $violations = $this->validator->validate($data, $constraints);
            if (count($violations) > 0) {
                $errors = [];
                foreach ($violations as $violation) {
                    $errors[$violation->getPropertyPath()] = $violation->getMessage();
                }
                return $this->json([
                    'success' => false,
                    'error' => 'Validation failed',
                    'details' => $errors
                ], Response::HTTP_BAD_REQUEST);
            }

            $result = $this->openPhoneApiService->makeCall(
                $data['phoneNumberId'],
                $data['toNumber'],
                $data['fromNumber'] ?? null
            );

            return $this->json([
                'success' => true,
                'data' => $result
            ]);

        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'error' => 'Failed to make call',
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/messages', name: 'openphone_send_message', methods: ['POST'])]
    public function sendMessage(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            
            if (!$data) {
                return $this->json([
                    'success' => false,
                    'error' => 'Invalid JSON'
                ], Response::HTTP_BAD_REQUEST);
            }

            // Validate input
            $constraints = new Assert\Collection([
                'phoneNumberId' => [new Assert\NotBlank()],
                'toNumber' => [new Assert\NotBlank()],
                'message' => [new Assert\NotBlank()]
            ]);

            $violations = $this->validator->validate($data, $constraints);
            if (count($violations) > 0) {
                $errors = [];
                foreach ($violations as $violation) {
                    $errors[$violation->getPropertyPath()] = $violation->getMessage();
                }
                return $this->json([
                    'success' => false,
                    'error' => 'Validation failed',
                    'details' => $errors
                ], Response::HTTP_BAD_REQUEST);
            }

            $result = $this->openPhoneApiService->sendMessage(
                $data['phoneNumberId'],
                $data['toNumber'],
                $data['message']
            );

            return $this->json([
                'success' => true,
                'data' => $result
            ]);

        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'error' => 'Failed to send message',
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/integrations/{id}/sync', name: 'openphone_sync_integration', methods: ['POST'])]
    public function syncIntegration(string $id): JsonResponse
    {
        try {
            $integration = $this->entityManager->getRepository(OpenPhoneIntegration::class)
                ->find($id);
            
            if (!$integration) {
                return $this->json([
                    'success' => false,
                    'error' => 'Integration not found'
                ], Response::HTTP_NOT_FOUND);
            }

            $callSyncCount = 0;
            $messageSyncCount = 0;

            if ($integration->isAutoLogCalls()) {
                $callSyncCount = $this->openPhoneApiService->syncCallLogs(
                    $integration->getClient(),
                    $integration
                );
            }

            if ($integration->isAutoLogMessages()) {
                $messageSyncCount = $this->openPhoneApiService->syncMessageLogs(
                    $integration->getClient(),
                    $integration
                );
            }

            return $this->json([
                'success' => true,
                'data' => [
                    'callLogsSynced' => $callSyncCount,
                    'messageLogsSynced' => $messageSyncCount,
                    'totalSynced' => $callSyncCount + $messageSyncCount
                ]
            ]);

        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'error' => 'Failed to sync integration',
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/call-logs', name: 'openphone_call_logs', methods: ['GET'])]
    public function getCallLogs(Request $request): JsonResponse
    {
        try {
            $clientId = $request->query->get('clientId');
            $integrationId = $request->query->get('integrationId');
            $limit = (int) $request->query->get('limit', 50);
            $offset = (int) $request->query->get('offset', 0);

            $qb = $this->entityManager->getRepository(OpenPhoneCallLog::class)
                ->createQueryBuilder('cl')
                ->orderBy('cl.createdAt', 'DESC')
                ->setMaxResults($limit)
                ->setFirstResult($offset);

            if ($clientId) {
                $qb->andWhere('cl.client = :clientId')
                   ->setParameter('clientId', $clientId);
            }

            if ($integrationId) {
                $qb->andWhere('cl.integration = :integrationId')
                   ->setParameter('integrationId', $integrationId);
            }

            $callLogs = $qb->getQuery()->getResult();

            $data = array_map(function (OpenPhoneCallLog $callLog) {
                return [
                    'id' => $callLog->getId(),
                    'openPhoneCallId' => $callLog->getOpenPhoneCallId(),
                    'clientId' => $callLog->getClient()->getId(),
                    'clientName' => $callLog->getClient()->getName(),
                    'integrationId' => $callLog->getIntegration()->getId(),
                    'direction' => $callLog->getDirection(),
                    'status' => $callLog->getStatus(),
                    'fromNumber' => $callLog->getFromNumber(),
                    'toNumber' => $callLog->getToNumber(),
                    'duration' => $callLog->getDuration(),
                    'startedAt' => $callLog->getStartedAt()?->format('c'),
                    'endedAt' => $callLog->getEndedAt()?->format('c'),
                    'recordingUrl' => $callLog->getRecordingUrl(),
                    'transcript' => $callLog->getTranscript(),
                    'isFollowUpRequired' => $callLog->isFollowUpRequired(),
                    'notes' => $callLog->getNotes(),
                    'createdAt' => $callLog->getCreatedAt()->format('c'),
                    'updatedAt' => $callLog->getUpdatedAt()->format('c')
                ];
            }, $callLogs);

            return $this->json([
                'success' => true,
                'data' => $data
            ]);

        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'error' => 'Failed to fetch call logs',
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/message-logs', name: 'openphone_message_logs', methods: ['GET'])]
    public function getMessageLogs(Request $request): JsonResponse
    {
        try {
            $clientId = $request->query->get('clientId');
            $integrationId = $request->query->get('integrationId');
            $limit = (int) $request->query->get('limit', 50);
            $offset = (int) $request->query->get('offset', 0);

            $qb = $this->entityManager->getRepository(OpenPhoneMessageLog::class)
                ->createQueryBuilder('ml')
                ->orderBy('ml.createdAt', 'DESC')
                ->setMaxResults($limit)
                ->setFirstResult($offset);

            if ($clientId) {
                $qb->andWhere('ml.client = :clientId')
                   ->setParameter('clientId', $clientId);
            }

            if ($integrationId) {
                $qb->andWhere('ml.integration = :integrationId')
                   ->setParameter('integrationId', $integrationId);
            }

            $messageLogs = $qb->getQuery()->getResult();

            $data = array_map(function (OpenPhoneMessageLog $messageLog) {
                return [
                    'id' => $messageLog->getId(),
                    'openPhoneMessageId' => $messageLog->getOpenPhoneMessageId(),
                    'clientId' => $messageLog->getClient()->getId(),
                    'clientName' => $messageLog->getClient()->getName(),
                    'integrationId' => $messageLog->getIntegration()->getId(),
                    'direction' => $messageLog->getDirection(),
                    'status' => $messageLog->getStatus(),
                    'fromNumber' => $messageLog->getFromNumber(),
                    'toNumber' => $messageLog->getToNumber(),
                    'content' => $messageLog->getContent(),
                    'attachments' => $messageLog->getAttachments(),
                    'sentAt' => $messageLog->getSentAt()->format('c'),
                    'isFollowUpRequired' => $messageLog->isFollowUpRequired(),
                    'notes' => $messageLog->getNotes(),
                    'createdAt' => $messageLog->getCreatedAt()->format('c'),
                    'updatedAt' => $messageLog->getUpdatedAt()->format('c')
                ];
            }, $messageLogs);

            return $this->json([
                'success' => true,
                'data' => $data
            ]);

        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'error' => 'Failed to fetch message logs',
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
