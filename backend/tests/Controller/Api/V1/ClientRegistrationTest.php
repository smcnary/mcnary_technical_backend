<?php

namespace App\Tests\Controller\Api\V1;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class ClientRegistrationTest extends WebTestCase
{
    public function testClientRegistrationWithDomainName(): void
    {
        $client = static::createClient();
        
        $testData = [
            'organization_name' => 'McNary Law',
            'organization_domain' => 'smcnary.github.io',
            'client_name' => 'McNary Law',
            'client_website' => 'smcnary.github.io',
            'admin_email' => 'test@example.com',
            'admin_password' => 'TestPassword123!',
            'admin_first_name' => 'Sean',
            'admin_last_name' => 'Dawson'
        ];

        $client->request(
            'POST',
            '/api/v1/clients/register',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($testData)
        );

        // Should not return 400 Bad Request for validation errors
        $this->assertNotEquals(Response::HTTP_BAD_REQUEST, $client->getResponse()->getStatusCode());
        
        // The response might be 409 (conflict) if the email already exists, or 201 (created) if successful
        // But it should not be 400 (bad request) due to validation errors
        $responseData = json_decode($client->getResponse()->getContent(), true);
        
        if ($client->getResponse()->getStatusCode() === Response::HTTP_BAD_REQUEST) {
            // If we get a 400, it should not be due to domain validation errors
            $this->assertArrayNotHasKey('organization_domain', $responseData['details'] ?? []);
            $this->assertArrayNotHasKey('client_website', $responseData['details'] ?? []);
        }
    }

    public function testClientRegistrationWithFullUrl(): void
    {
        $client = static::createClient();
        
        $testData = [
            'organization_name' => 'Test Organization',
            'organization_domain' => 'https://example.com',
            'client_name' => 'Test Client',
            'client_website' => 'https://example.com',
            'admin_email' => 'test2@example.com',
            'admin_password' => 'TestPassword123!',
            'admin_first_name' => 'John',
            'admin_last_name' => 'Doe'
        ];

        $client->request(
            'POST',
            '/api/v1/clients/register',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($testData)
        );

        // Should not return 400 Bad Request for validation errors
        $this->assertNotEquals(Response::HTTP_BAD_REQUEST, $client->getResponse()->getStatusCode());
        
        $responseData = json_decode($client->getResponse()->getContent(), true);
        
        if ($client->getResponse()->getStatusCode() === Response::HTTP_BAD_REQUEST) {
            // If we get a 400, it should not be due to domain validation errors
            $this->assertArrayNotHasKey('organization_domain', $responseData['details'] ?? []);
            $this->assertArrayNotHasKey('client_website', $responseData['details'] ?? []);
        }
    }
}
