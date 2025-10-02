<?php

namespace App\Controller\Api\V1;

use App\Entity\Client;
use App\Service\TwilioService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[Route('/api/v1/twilio')]
class TwilioController extends AbstractController
{
    public function __construct(
        private TwilioService $twilioService,
        private EntityManagerInterface $entityManager
    ) {}

    #[Route('/test-connection', name: 'api_v1_twilio_test_connection', methods: ['GET'])]
    public function testConnection(): JsonResponse
    {
        $result = $this->twilioService->testConnection();
        
        if ($result['success']) {
            return $this->json([
                'success' => true,
                'message' => 'Twilio connection successful',
                'data' => $result
            ]);
        }

        return $this->json([
            'success' => false,
            'message' => 'Twilio connection failed',
            'error' => $result['error']
        ], 500);
    }

    #[Route('/call-target', name: 'api_v1_twilio_call_target', methods: ['POST'])]
    public function callTargetNumber(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        $twimlUrl = $data['twiml_url'] ?? null;
        $twiml = $data['twiml'] ?? null;

        $result = $this->twilioService->makeCallToTargetNumber($twimlUrl, $twiml);

        if ($result['success']) {
            return $this->json([
                'success' => true,
                'message' => 'Call initiated successfully to 786-213-3333',
                'data' => $result
            ]);
        }

        return $this->json([
            'success' => false,
            'message' => 'Failed to initiate call',
            'error' => $result['error']
        ], 400);
    }

    #[Route('/sms-target', name: 'api_v1_twilio_sms_target', methods: ['POST'])]
    public function sendSmsToTargetNumber(Request $request, ValidatorInterface $validator): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        // Validate message
        $message = $data['message'] ?? '';
        $violations = $validator->validate($message, [
            new Assert\NotBlank(message: 'Message is required'),
            new Assert\Length(min: 1, max: 1600, minMessage: 'Message cannot be empty', maxMessage: 'Message is too long')
        ]);

        if (count($violations) > 0) {
            $errors = [];
            foreach ($violations as $violation) {
                $errors[] = $violation->getMessage();
            }
            
            return $this->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $errors
            ], 400);
        }

        $result = $this->twilioService->sendSmsToTargetNumber($message);

        if ($result['success']) {
            return $this->json([
                'success' => true,
                'message' => 'SMS sent successfully to 786-213-3333',
                'data' => $result
            ]);
        }

        return $this->json([
            'success' => false,
            'message' => 'Failed to send SMS',
            'error' => $result['error']
        ], 400);
    }

    #[Route('/call-client/{clientId}', name: 'api_v1_twilio_call_client', methods: ['POST'])]
    public function callClient(string $clientId, Request $request): JsonResponse
    {
        $client = $this->entityManager->getRepository(Client::class)->find($clientId);
        
        if (!$client) {
            return $this->json([
                'success' => false,
                'message' => 'Client not found'
            ], 404);
        }

        $data = json_decode($request->getContent(), true);
        $twimlUrl = $data['twiml_url'] ?? null;
        $twiml = $data['twiml'] ?? null;

        $result = $this->twilioService->makeCallToClient($client, $twimlUrl, $twiml);

        if ($result['success']) {
            return $this->json([
                'success' => true,
                'message' => sprintf('Call initiated successfully to client %s', $client->getName()),
                'data' => $result
            ]);
        }

        return $this->json([
            'success' => false,
            'message' => 'Failed to initiate call to client',
            'error' => $result['error']
        ], 400);
    }

    #[Route('/sms-client/{clientId}', name: 'api_v1_twilio_sms_client', methods: ['POST'])]
    public function sendSmsToClient(string $clientId, Request $request, ValidatorInterface $validator): JsonResponse
    {
        $client = $this->entityManager->getRepository(Client::class)->find($clientId);
        
        if (!$client) {
            return $this->json([
                'success' => false,
                'message' => 'Client not found'
            ], 404);
        }

        $data = json_decode($request->getContent(), true);
        
        // Validate message
        $message = $data['message'] ?? '';
        $violations = $validator->validate($message, [
            new Assert\NotBlank(message: 'Message is required'),
            new Assert\Length(min: 1, max: 1600, minMessage: 'Message cannot be empty', maxMessage: 'Message is too long')
        ]);

        if (count($violations) > 0) {
            $errors = [];
            foreach ($violations as $violation) {
                $errors[] = $violation->getMessage();
            }
            
            return $this->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $errors
            ], 400);
        }

        $result = $this->twilioService->sendSmsToClient($client, $message);

        if ($result['success']) {
            return $this->json([
                'success' => true,
                'message' => sprintf('SMS sent successfully to client %s', $client->getName()),
                'data' => $result
            ]);
        }

        return $this->json([
            'success' => false,
            'message' => 'Failed to send SMS to client',
            'error' => $result['error']
        ], 400);
    }

    #[Route('/call-details/{callSid}', name: 'api_v1_twilio_call_details', methods: ['GET'])]
    public function getCallDetails(string $callSid): JsonResponse
    {
        $details = $this->twilioService->getCallDetails($callSid);

        if ($details) {
            return $this->json([
                'success' => true,
                'data' => $details
            ]);
        }

        return $this->json([
            'success' => false,
            'message' => 'Call details not found'
        ], 404);
    }

    #[Route('/message-details/{messageSid}', name: 'api_v1_twilio_message_details', methods: ['GET'])]
    public function getMessageDetails(string $messageSid): JsonResponse
    {
        $details = $this->twilioService->getMessageDetails($messageSid);

        if ($details) {
            return $this->json([
                'success' => true,
                'data' => $details
            ]);
        }

        return $this->json([
            'success' => false,
            'message' => 'Message details not found'
        ], 404);
    }

    #[Route('/phone-info', name: 'api_v1_twilio_phone_info', methods: ['GET'])]
    public function getPhoneInfo(): JsonResponse
    {
        return $this->json([
            'success' => true,
            'data' => [
                'twilio_phone_number' => $this->twilioService->getTwilioPhoneNumber(),
                'target_phone_number' => '+17862133333',
                'target_phone_formatted' => '786-213-3333'
            ]
        ]);
    }
}
