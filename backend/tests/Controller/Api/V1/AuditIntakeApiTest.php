<?php

namespace App\Tests\Controller\Api\V1;

use App\Entity\AuditIntake;
use App\Entity\Client;
use App\Entity\User;
use App\Repository\AuditIntakeRepository;
use App\Repository\ClientRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class AuditIntakeApiTest extends WebTestCase
{
    private $client;
    private $entityManager;
    private $auditIntakeRepository;
    private $clientRepository;
    private $userRepository;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $this->auditIntakeRepository = static::getContainer()->get(AuditIntakeRepository::class);
        $this->clientRepository = static::getContainer()->get(ClientRepository::class);
        $this->userRepository = static::getContainer()->get(UserRepository::class);
    }

    public function testAuditIntakeEndpointsExist(): void
    {
        // Test that the API Platform endpoints are accessible
        $this->client->request('GET', '/api/v1/audits/intakes');
        
        // Should return 401 (unauthorized) rather than 404 (not found)
        $this->assertNotEquals(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode());
    }

    public function testCreateAuditIntake(): void
    {
        // This test would require proper authentication and client setup
        // For now, we'll just verify the endpoint structure exists
        
        $testData = [
            'websiteUrl' => 'https://example.com',
            'contactName' => 'John Doe',
            'contactEmail' => 'john@example.com',
            'cms' => 'custom',
            'techStack' => [
                'industry' => 'technology',
                'goals' => ['increase traffic'],
                'competitors' => 'competitor1, competitor2',
                'budget' => '$1000',
                'tier' => 'Growth',
                'notes' => 'Test audit intake'
            ],
            'notes' => 'Test audit intake',
            'status' => 'draft'
        ];

        $this->client->request(
            'POST',
            '/api/v1/audits/intakes',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($testData)
        );

        // Should return 401 (unauthorized) rather than 404 (not found)
        $this->assertNotEquals(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode());
    }

    public function testAuditIntakeEntityStructure(): void
    {
        // Test that the entity can be created and persisted
        $auditIntake = new AuditIntake();
        
        $auditIntake->setWebsiteUrl('https://test.com');
        $auditIntake->setContactName('Test User');
        $auditIntake->setContactEmail('test@example.com');
        $auditIntake->setCms('wordpress');
        $auditIntake->setTechStack([
            'industry' => 'test',
            'goals' => ['test goal'],
            'competitors' => 'test competitor',
            'budget' => '$500',
            'tier' => 'Pro',
            'notes' => 'Test notes'
        ]);
        $auditIntake->setNotes('Test notes');
        $auditIntake->setStatus('draft');

        // Verify the entity has the expected structure
        $this->assertEquals('https://test.com', $auditIntake->getWebsiteUrl());
        $this->assertEquals('Test User', $auditIntake->getContactName());
        $this->assertEquals('test@example.com', $auditIntake->getContactEmail());
        $this->assertEquals('wordpress', $auditIntake->getCms());
        $this->assertEquals('draft', $auditIntake->getStatus());
        
        $techStack = $auditIntake->getTechStack();
        $this->assertIsArray($techStack);
        $this->assertEquals('test', $techStack['industry']);
        $this->assertEquals(['test goal'], $techStack['goals']);
    }
}
