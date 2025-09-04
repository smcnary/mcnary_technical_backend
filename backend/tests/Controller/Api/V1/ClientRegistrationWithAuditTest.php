<?php

namespace App\Tests\Controller\Api\V1;

use App\Entity\Organization;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class ClientRegistrationWithAuditTest extends WebTestCase
{
    public function testClientRegistrationWithAuditData(): void
    {
        $client = static::createClient();
        
        // Ensure we have an organization in the test database
        $entityManager = $client->getContainer()->get(EntityManagerInterface::class);
        
        // Check if organization exists, if not create one
        $organization = $entityManager->getRepository(Organization::class)->findOneBy([]);
        if (!$organization) {
            $organization = new Organization('Test Organization');
            $organization->setDomain('test.com');
            $organization->setStatus('active');
            $entityManager->persist($organization);
            $entityManager->flush();
        }
        
        // Test data that matches what the SEO Audit Wizard would send
        $uniqueId = uniqid();
        $testData = [
            'organization_name' => 'McNary Law ' . $uniqueId,
            'organization_domain' => 'smcnary.github.io',
            'client_name' => 'McNary Law ' . $uniqueId,
            'client_website' => 'smcnary.github.io',
            'client_industry' => 'other', // technology gets mapped to 'other'
            'admin_email' => 'test-audit-' . $uniqueId . '@example.com',
            'admin_password' => 'TestPassword123!',
            'admin_first_name' => 'Sean',
            'admin_last_name' => 'Dawson',
            'client_description' => 'Audit Request - Goals: More calls/leads, Rank locally, Budget: 12000, Tier: Audit + Retainer, Notes: Nope'
        ];

        $client->request(
            'POST',
            '/api/v1/clients/register',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($testData)
        );

        $responseData = json_decode($client->getResponse()->getContent(), true);
        
        // Should not return 400 Bad Request for validation errors
        $this->assertNotEquals(Response::HTTP_BAD_REQUEST, $client->getResponse()->getStatusCode());
        
        if ($client->getResponse()->getStatusCode() === Response::HTTP_BAD_REQUEST) {
            // If we get a 400, it should not be due to domain validation errors
            $this->assertArrayNotHasKey('organization_domain', $responseData['details'] ?? []);
            $this->assertArrayNotHasKey('client_website', $responseData['details'] ?? []);
        } else {
            // If successful, should return admin_user and client data (not user)
            $this->assertArrayHasKey('admin_user', $responseData);
            $this->assertArrayHasKey('client', $responseData);
            // Token is optional (may not be generated in test environment)
            if (isset($responseData['token'])) {
                $this->assertIsString($responseData['token']);
            }
        }
    }

    public function testClientRegistrationWithFullUrls(): void
    {
        $client = static::createClient();
        
        // Ensure we have an organization in the test database
        $entityManager = $client->getContainer()->get(EntityManagerInterface::class);
        
        // Check if organization exists, if not create one
        $organization = $entityManager->getRepository(Organization::class)->findOneBy([]);
        if (!$organization) {
            $organization = new Organization('Test Organization');
            $organization->setDomain('test.com');
            $organization->setStatus('active');
            $entityManager->persist($organization);
            $entityManager->flush();
        }
        
        $uniqueId = uniqid();
        $testData = [
            'organization_name' => 'Test Company ' . $uniqueId,
            'organization_domain' => 'https://example.com',
            'client_name' => 'Test Company ' . $uniqueId,
            'client_website' => 'https://example.com',
            'client_industry' => 'law',
            'admin_email' => 'test-full-url-' . $uniqueId . '@example.com',
            'admin_password' => 'TestPassword123!',
            'admin_first_name' => 'John',
            'admin_last_name' => 'Doe',
            'client_description' => 'Test audit request'
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
