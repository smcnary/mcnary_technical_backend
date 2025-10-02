<?php

namespace App\Service;

use App\Entity\Client;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Twilio\Rest\Client as TwilioClient;
use Twilio\Exceptions\TwilioException;

class TwilioService
{
    private TwilioClient $twilioClient;
    private string $twilioPhoneNumber;

    public function __construct(
        private EntityManagerInterface $entityManager,
        private LoggerInterface $logger,
        string $twilioAccountSid,
        string $twilioAuthToken,
        string $twilioPhoneNumber
    ) {
        $this->twilioClient = new TwilioClient($twilioAccountSid, $twilioAuthToken);
        $this->twilioPhoneNumber = $twilioPhoneNumber;
    }

    /**
     * Make a call from Twilio phone number to target number
     */
    public function makeCall(string $toNumber, ?string $twimlUrl = null, ?string $twiml = null): array
    {
        try {
            // Ensure phone numbers are in E.164 format
            $fromNumber = $this->twilioPhoneNumber;
            $toNumber = $this->formatPhoneNumber($toNumber);

            $callOptions = [
                'from' => $fromNumber,
                'to' => $toNumber,
            ];

            // Add TwiML URL or inline TwiML
            if ($twimlUrl) {
                $callOptions['url'] = $twimlUrl;
            } elseif ($twiml) {
                $callOptions['twiml'] = $twiml;
            } else {
                // Default TwiML - just say hello and hang up
                $callOptions['twiml'] = '<Response><Say>Hello! This is a call from CounselRank Legal Services. Thank you for your interest.</Say></Response>';
            }

            $call = $this->twilioClient->calls->create(
                $toNumber,
                $fromNumber,
                $callOptions
            );

            $this->logger->info('Twilio call initiated', [
                'callSid' => $call->sid,
                'from' => $fromNumber,
                'to' => $toNumber,
                'status' => $call->status
            ]);

            return [
                'success' => true,
                'callSid' => $call->sid,
                'status' => $call->status,
                'from' => $fromNumber,
                'to' => $toNumber,
                'direction' => 'outbound-api'
            ];

        } catch (TwilioException $e) {
            $this->logger->error('Twilio call failed', [
                'error' => $e->getMessage(),
                'errorCode' => $e->getCode(),
                'toNumber' => $toNumber
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'errorCode' => $e->getCode()
            ];
        }
    }

    /**
     * Send an SMS message
     */
    public function sendSms(string $toNumber, string $message): array
    {
        try {
            $fromNumber = $this->twilioPhoneNumber;
            $toNumber = $this->formatPhoneNumber($toNumber);

            $sms = $this->twilioClient->messages->create(
                $toNumber,
                [
                    'from' => $fromNumber,
                    'body' => $message
                ]
            );

            $this->logger->info('Twilio SMS sent', [
                'messageSid' => $sms->sid,
                'from' => $fromNumber,
                'to' => $toNumber,
                'status' => $sms->status
            ]);

            return [
                'success' => true,
                'messageSid' => $sms->sid,
                'status' => $sms->status,
                'from' => $fromNumber,
                'to' => $toNumber,
                'body' => $message
            ];

        } catch (TwilioException $e) {
            $this->logger->error('Twilio SMS failed', [
                'error' => $e->getMessage(),
                'errorCode' => $e->getCode(),
                'toNumber' => $toNumber
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'errorCode' => $e->getCode()
            ];
        }
    }

    /**
     * Get call details by SID
     */
    public function getCallDetails(string $callSid): ?array
    {
        try {
            $call = $this->twilioClient->calls($callSid)->fetch();

            return [
                'sid' => $call->sid,
                'from' => $call->from,
                'to' => $call->to,
                'status' => $call->status,
                'direction' => $call->direction,
                'duration' => $call->duration,
                'startTime' => $call->startTime,
                'endTime' => $call->endTime,
                'price' => $call->price,
                'priceUnit' => $call->priceUnit
            ];

        } catch (TwilioException $e) {
            $this->logger->error('Failed to fetch Twilio call details', [
                'error' => $e->getMessage(),
                'callSid' => $callSid
            ]);

            return null;
        }
    }

    /**
     * Get message details by SID
     */
    public function getMessageDetails(string $messageSid): ?array
    {
        try {
            $message = $this->twilioClient->messages($messageSid)->fetch();

            return [
                'sid' => $message->sid,
                'from' => $message->from,
                'to' => $message->to,
                'status' => $message->status,
                'direction' => $message->direction,
                'body' => $message->body,
                'dateCreated' => $message->dateCreated,
                'dateSent' => $message->dateSent,
                'price' => $message->price,
                'priceUnit' => $message->priceUnit
            ];

        } catch (TwilioException $e) {
            $this->logger->error('Failed to fetch Twilio message details', [
                'error' => $e->getMessage(),
                'messageSid' => $messageSid
            ]);

            return null;
        }
    }

    /**
     * Make a call to a specific client
     */
    public function makeCallToClient(Client $client, ?string $twimlUrl = null, ?string $twiml = null): array
    {
        if (!$client->getPhone()) {
            return [
                'success' => false,
                'error' => 'Client does not have a phone number'
            ];
        }

        $result = $this->makeCall($client->getPhone(), $twimlUrl, $twiml);

        if ($result['success']) {
            $this->logger->info('Call made to client', [
                'clientId' => $client->getId(),
                'clientName' => $client->getName(),
                'phoneNumber' => $client->getPhone(),
                'callSid' => $result['callSid']
            ]);
        }

        return $result;
    }

    /**
     * Send SMS to a specific client
     */
    public function sendSmsToClient(Client $client, string $message): array
    {
        if (!$client->getPhone()) {
            return [
                'success' => false,
                'error' => 'Client does not have a phone number'
            ];
        }

        $result = $this->sendSms($client->getPhone(), $message);

        if ($result['success']) {
            $this->logger->info('SMS sent to client', [
                'clientId' => $client->getId(),
                'clientName' => $client->getName(),
                'phoneNumber' => $client->getPhone(),
                'messageSid' => $result['messageSid']
            ]);
        }

        return $result;
    }

    /**
     * Make a call to the specific number 786-213-3333
     */
    public function makeCallToTargetNumber(?string $twimlUrl = null, ?string $twiml = null): array
    {
        $targetNumber = '+17862133333'; // 786-213-3333 in E.164 format
        return $this->makeCall($targetNumber, $twimlUrl, $twiml);
    }

    /**
     * Send SMS to the specific number 786-213-3333
     */
    public function sendSmsToTargetNumber(string $message): array
    {
        $targetNumber = '+17862133333'; // 786-213-3333 in E.164 format
        return $this->sendSms($targetNumber, $message);
    }

    /**
     * Format phone number to E.164 format
     */
    private function formatPhoneNumber(string $phoneNumber): string
    {
        // Remove all non-digit characters
        $digits = preg_replace('/\D/', '', $phoneNumber);

        // If it's already in E.164 format, return as is
        if (str_starts_with($digits, '1') && strlen($digits) === 11) {
            return '+' . $digits;
        }

        // If it's a 10-digit US number, add country code
        if (strlen($digits) === 10) {
            return '+1' . $digits;
        }

        // If it's 11 digits and starts with 1, add +
        if (strlen($digits) === 11 && str_starts_with($digits, '1')) {
            return '+' . $digits;
        }

        // Default: assume it's a US number and add +1
        return '+1' . $digits;
    }

    /**
     * Get Twilio phone number
     */
    public function getTwilioPhoneNumber(): string
    {
        return $this->twilioPhoneNumber;
    }

    /**
     * Test Twilio connection
     */
    public function testConnection(): array
    {
        try {
            $account = $this->twilioClient->api->accounts($this->twilioClient->getAccountSid())->fetch();
            
            return [
                'success' => true,
                'accountSid' => $account->sid,
                'friendlyName' => $account->friendlyName,
                'status' => $account->status
            ];

        } catch (TwilioException $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'errorCode' => $e->getCode()
            ];
        }
    }
}
