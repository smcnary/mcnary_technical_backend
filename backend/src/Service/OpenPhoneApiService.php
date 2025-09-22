<?php

namespace App\Service;

use App\Entity\Client;
use App\Entity\OpenPhoneIntegration;
use App\Entity\OpenPhoneCallLog;
use App\Entity\OpenPhoneMessageLog;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class OpenPhoneApiService
{
    private HttpClientInterface $httpClient;
    private string $apiKey;
    private string $baseUrl;

    public function __construct(
        private EntityManagerInterface $entityManager,
        private LoggerInterface $logger,
        string $openPhoneApiKey,
        string $openPhoneBaseUrl = 'https://api.openphone.com/v1'
    ) {
        $this->apiKey = $openPhoneApiKey;
        $this->baseUrl = $openPhoneBaseUrl;
        $this->httpClient = HttpClient::create([
            'headers' => [
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ],
        ]);
    }

    /**
     * Get all phone numbers for the account
     */
    public function getPhoneNumbers(): array
    {
        try {
            $response = $this->httpClient->request('GET', $this->baseUrl . '/phone-numbers');
            $data = $response->toArray();
            
            return $data['data'] ?? [];
        } catch (\Exception $e) {
            $this->logger->error('Failed to fetch OpenPhone numbers', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Make an outbound call
     */
    public function makeCall(string $phoneNumberId, string $toNumber, ?string $fromNumber = null): array
    {
        try {
            $payload = [
                'phoneNumberId' => $phoneNumberId,
                'to' => $toNumber,
            ];

            if ($fromNumber) {
                $payload['from'] = $fromNumber;
            }

            $response = $this->httpClient->request('POST', $this->baseUrl . '/calls', [
                'json' => $payload
            ]);

            return $response->toArray();
        } catch (\Exception $e) {
            $this->logger->error('Failed to make OpenPhone call', [
                'error' => $e->getMessage(),
                'phoneNumberId' => $phoneNumberId,
                'toNumber' => $toNumber
            ]);
            throw $e;
        }
    }

    /**
     * Send an SMS message
     */
    public function sendMessage(string $phoneNumberId, string $toNumber, string $message): array
    {
        try {
            $payload = [
                'phoneNumberId' => $phoneNumberId,
                'to' => $toNumber,
                'text' => $message
            ];

            $response = $this->httpClient->request('POST', $this->baseUrl . '/messages', [
                'json' => $payload
            ]);

            return $response->toArray();
        } catch (\Exception $e) {
            $this->logger->error('Failed to send OpenPhone message', [
                'error' => $e->getMessage(),
                'phoneNumberId' => $phoneNumberId,
                'toNumber' => $toNumber
            ]);
            throw $e;
        }
    }

    /**
     * Get call logs
     */
    public function getCallLogs(string $phoneNumberId, int $limit = 50, int $offset = 0): array
    {
        try {
            $response = $this->httpClient->request('GET', $this->baseUrl . '/calls', [
                'query' => [
                    'phoneNumberId' => $phoneNumberId,
                    'limit' => $limit,
                    'offset' => $offset
                ]
            ]);

            return $response->toArray();
        } catch (\Exception $e) {
            $this->logger->error('Failed to fetch OpenPhone call logs', [
                'error' => $e->getMessage(),
                'phoneNumberId' => $phoneNumberId
            ]);
            throw $e;
        }
    }

    /**
     * Get message logs
     */
    public function getMessageLogs(string $phoneNumberId, int $limit = 50, int $offset = 0): array
    {
        try {
            $response = $this->httpClient->request('GET', $this->baseUrl . '/messages', [
                'query' => [
                    'phoneNumberId' => $phoneNumberId,
                    'limit' => $limit,
                    'offset' => $offset
                ]
            ]);

            return $response->toArray();
        } catch (\Exception $e) {
            $this->logger->error('Failed to fetch OpenPhone message logs', [
                'error' => $e->getMessage(),
                'phoneNumberId' => $phoneNumberId
            ]);
            throw $e;
        }
    }

    /**
     * Sync call logs for a client
     */
    public function syncCallLogs(Client $client, OpenPhoneIntegration $integration): int
    {
        $syncedCount = 0;
        
        try {
            $callLogs = $this->getCallLogs($integration->getPhoneNumber());
            
            foreach ($callLogs['data'] ?? [] as $callData) {
                // Check if call log already exists
                $existingLog = $this->entityManager->getRepository(OpenPhoneCallLog::class)
                    ->findOneBy(['openPhoneCallId' => $callData['id']]);
                
                if ($existingLog) {
                    continue; // Skip if already synced
                }

                $callLog = new OpenPhoneCallLog($client, $integration, $callData['id']);
                $callLog->setDirection($callData['direction'] ?? 'unknown');
                $callLog->setStatus($callData['status'] ?? 'unknown');
                $callLog->setFromNumber($callData['from'] ?? null);
                $callLog->setToNumber($callData['to'] ?? null);
                $callLog->setDuration($callData['duration'] ?? null);
                
                if (isset($callData['startedAt'])) {
                    $callLog->setStartedAt(new \DateTimeImmutable($callData['startedAt']));
                }
                
                if (isset($callData['endedAt'])) {
                    $callLog->setEndedAt(new \DateTimeImmutable($callData['endedAt']));
                }
                
                $callLog->setRecordingUrl($callData['recordingUrl'] ?? null);
                $callLog->setTranscript($callData['transcript'] ?? null);
                $callLog->setMetadata($callData);

                $this->entityManager->persist($callLog);
                $syncedCount++;
            }

            $this->entityManager->flush();
            
        } catch (\Exception $e) {
            $this->logger->error('Failed to sync OpenPhone call logs', [
                'error' => $e->getMessage(),
                'clientId' => $client->getId(),
                'integrationId' => $integration->getId()
            ]);
            throw $e;
        }

        return $syncedCount;
    }

    /**
     * Sync message logs for a client
     */
    public function syncMessageLogs(Client $client, OpenPhoneIntegration $integration): int
    {
        $syncedCount = 0;
        
        try {
            $messageLogs = $this->getMessageLogs($integration->getPhoneNumber());
            
            foreach ($messageLogs['data'] ?? [] as $messageData) {
                // Check if message log already exists
                $existingLog = $this->entityManager->getRepository(OpenPhoneMessageLog::class)
                    ->findOneBy(['openPhoneMessageId' => $messageData['id']]);
                
                if ($existingLog) {
                    continue; // Skip if already synced
                }

                $messageLog = new OpenPhoneMessageLog($client, $integration, $messageData['id']);
                $messageLog->setDirection($messageData['direction'] ?? 'unknown');
                $messageLog->setStatus($messageData['status'] ?? 'unknown');
                $messageLog->setFromNumber($messageData['from'] ?? null);
                $messageLog->setToNumber($messageData['to'] ?? null);
                $messageLog->setContent($messageData['text'] ?? '');
                $messageLog->setAttachments($messageData['attachments'] ?? []);
                
                if (isset($messageData['sentAt'])) {
                    $messageLog->setSentAt(new \DateTimeImmutable($messageData['sentAt']));
                }
                
                $messageLog->setMetadata($messageData);

                $this->entityManager->persist($messageLog);
                $syncedCount++;
            }

            $this->entityManager->flush();
            
        } catch (\Exception $e) {
            $this->logger->error('Failed to sync OpenPhone message logs', [
                'error' => $e->getMessage(),
                'clientId' => $client->getId(),
                'integrationId' => $integration->getId()
            ]);
            throw $e;
        }

        return $syncedCount;
    }

    /**
     * Process webhook payload for call events
     */
    public function processCallWebhook(array $payload): void
    {
        try {
            $callId = $payload['id'] ?? null;
            $phoneNumber = $payload['phoneNumber'] ?? null;
            
            if (!$callId || !$phoneNumber) {
                $this->logger->warning('Invalid OpenPhone call webhook payload', ['payload' => $payload]);
                return;
            }

            // Find the integration by phone number
            $integration = $this->entityManager->getRepository(OpenPhoneIntegration::class)
                ->findOneBy(['phoneNumber' => $phoneNumber]);
            
            if (!$integration) {
                $this->logger->warning('No OpenPhone integration found for phone number', ['phoneNumber' => $phoneNumber]);
                return;
            }

            // Check if call log already exists
            $existingLog = $this->entityManager->getRepository(OpenPhoneCallLog::class)
                ->findOneBy(['openPhoneCallId' => $callId]);
            
            if ($existingLog) {
                // Update existing log
                $existingLog->setStatus($payload['status'] ?? $existingLog->getStatus());
                $existingLog->setDuration($payload['duration'] ?? $existingLog->getDuration());
                
                if (isset($payload['endedAt'])) {
                    $existingLog->setEndedAt(new \DateTimeImmutable($payload['endedAt']));
                }
                
                $existingLog->setRecordingUrl($payload['recordingUrl'] ?? $existingLog->getRecordingUrl());
                $existingLog->setTranscript($payload['transcript'] ?? $existingLog->getTranscript());
                $existingLog->setMetadata(array_merge($existingLog->getMetadata() ?? [], $payload));
            } else {
                // Create new call log
                $callLog = new OpenPhoneCallLog($integration->getClient(), $integration, $callId);
                $callLog->setDirection($payload['direction'] ?? 'unknown');
                $callLog->setStatus($payload['status'] ?? 'unknown');
                $callLog->setFromNumber($payload['from'] ?? null);
                $callLog->setToNumber($payload['to'] ?? null);
                $callLog->setDuration($payload['duration'] ?? null);
                
                if (isset($payload['startedAt'])) {
                    $callLog->setStartedAt(new \DateTimeImmutable($payload['startedAt']));
                }
                
                if (isset($payload['endedAt'])) {
                    $callLog->setEndedAt(new \DateTimeImmutable($payload['endedAt']));
                }
                
                $callLog->setRecordingUrl($payload['recordingUrl'] ?? null);
                $callLog->setTranscript($payload['transcript'] ?? null);
                $callLog->setMetadata($payload);

                $this->entityManager->persist($callLog);
            }

            $this->entityManager->flush();
            
        } catch (\Exception $e) {
            $this->logger->error('Failed to process OpenPhone call webhook', [
                'error' => $e->getMessage(),
                'payload' => $payload
            ]);
            throw $e;
        }
    }

    /**
     * Process webhook payload for message events
     */
    public function processMessageWebhook(array $payload): void
    {
        try {
            $messageId = $payload['id'] ?? null;
            $phoneNumber = $payload['phoneNumber'] ?? null;
            
            if (!$messageId || !$phoneNumber) {
                $this->logger->warning('Invalid OpenPhone message webhook payload', ['payload' => $payload]);
                return;
            }

            // Find the integration by phone number
            $integration = $this->entityManager->getRepository(OpenPhoneIntegration::class)
                ->findOneBy(['phoneNumber' => $phoneNumber]);
            
            if (!$integration) {
                $this->logger->warning('No OpenPhone integration found for phone number', ['phoneNumber' => $phoneNumber]);
                return;
            }

            // Check if message log already exists
            $existingLog = $this->entityManager->getRepository(OpenPhoneMessageLog::class)
                ->findOneBy(['openPhoneMessageId' => $messageId]);
            
            if ($existingLog) {
                // Update existing log
                $existingLog->setStatus($payload['status'] ?? $existingLog->getStatus());
                $existingLog->setMetadata(array_merge($existingLog->getMetadata() ?? [], $payload));
            } else {
                // Create new message log
                $messageLog = new OpenPhoneMessageLog($integration->getClient(), $integration, $messageId);
                $messageLog->setDirection($payload['direction'] ?? 'unknown');
                $messageLog->setStatus($payload['status'] ?? 'unknown');
                $messageLog->setFromNumber($payload['from'] ?? null);
                $messageLog->setToNumber($payload['to'] ?? null);
                $messageLog->setContent($payload['text'] ?? '');
                $messageLog->setAttachments($payload['attachments'] ?? []);
                
                if (isset($payload['sentAt'])) {
                    $messageLog->setSentAt(new \DateTimeImmutable($payload['sentAt']));
                }
                
                $messageLog->setMetadata($payload);

                $this->entityManager->persist($messageLog);
            }

            $this->entityManager->flush();
            
        } catch (\Exception $e) {
            $this->logger->error('Failed to process OpenPhone message webhook', [
                'error' => $e->getMessage(),
                'payload' => $payload
            ]);
            throw $e;
        }
    }
}
