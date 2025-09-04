<?php

namespace App\Tests\Service;

use App\Entity\Client;
use App\Repository\ClientRepository;
use App\Service\AuditIntakeValidationService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

class AuditIntakeValidationServiceTest extends TestCase
{
    private AuditIntakeValidationService $service;
    private ClientRepository $clientRepository;

    protected function setUp(): void
    {
        $this->clientRepository = $this->createMock(ClientRepository::class);
        $this->service = new AuditIntakeValidationService($this->clientRepository);
    }

    public function testCheckEmailExistsReturnsNullWhenNoClientFound(): void
    {
        $this->clientRepository
            ->expects($this->once())
            ->method('findByEmail')
            ->with('test@example.com')
            ->willReturn(null);

        $result = $this->service->checkEmailExists('test@example.com');
        
        $this->assertNull($result);
    }

    public function testCheckEmailExistsReturnsClientInfoWhenFound(): void
    {
        $client = $this->createMockClient();
        
        $this->clientRepository
            ->expects($this->once())
            ->method('findByEmail')
            ->with('existing@example.com')
            ->willReturn($client);

        $result = $this->service->checkEmailExists('existing@example.com');
        
        $this->assertNotNull($result);
        $this->assertTrue($result['exists']);
        $this->assertEquals($client->getId(), $result['client_id']);
        $this->assertEquals($client->getName(), $result['client_name']);
        $this->assertEquals($client->getSlug(), $result['client_slug']);
        $this->assertStringContainsString('existing@example.com', $result['message']);
    }

    public function testCheckWebsiteExistsReturnsNullWhenNoClientFound(): void
    {
        $this->clientRepository
            ->expects($this->once())
            ->method('findBySlug')
            ->with('example-com')
            ->willReturn(null);

        $result = $this->service->checkWebsiteExists('https://www.example.com');
        
        $this->assertNull($result);
    }

    public function testCheckWebsiteExistsReturnsClientInfoWhenFound(): void
    {
        $client = $this->createMockClient();
        
        $this->clientRepository
            ->expects($this->once())
            ->method('findBySlug')
            ->with('example-com')
            ->willReturn($client);

        $result = $this->service->checkWebsiteExists('https://www.example.com');
        
        $this->assertNotNull($result);
        $this->assertTrue($result['exists']);
        $this->assertEquals($client->getId(), $result['client_id']);
        $this->assertEquals($client->getName(), $result['client_name']);
        $this->assertEquals($client->getSlug(), $result['client_slug']);
        $this->assertEquals('example-com', $result['domain']);
        $this->assertStringContainsString('https://www.example.com', $result['message']);
    }

    public function testValidateAuditIntakeDataWithNoConflicts(): void
    {
        $this->clientRepository
            ->expects($this->once())
            ->method('findByEmail')
            ->with('test@example.com')
            ->willReturn(null);

        $this->clientRepository
            ->expects($this->once())
            ->method('findBySlug')
            ->with('example-com')
            ->willReturn(null);

        $result = $this->service->validateAuditIntakeData('test@example.com', 'https://example.com');
        
        $this->assertFalse($result['has_conflicts']);
        $this->assertEmpty($result['conflicts']);
        $this->assertNull($result['email_check']);
        $this->assertNull($result['website_check']);
    }

    public function testValidateAuditIntakeDataWithEmailConflict(): void
    {
        $client = $this->createMockClient();
        
        $this->clientRepository
            ->expects($this->once())
            ->method('findByEmail')
            ->with('existing@example.com')
            ->willReturn($client);

        $this->clientRepository
            ->expects($this->once())
            ->method('findBySlug')
            ->willReturn(null);

        $result = $this->service->validateAuditIntakeData('existing@example.com', 'https://example.com');
        
        $this->assertTrue($result['has_conflicts']);
        $this->assertCount(1, $result['conflicts']);
        $this->assertNotNull($result['email_check']);
        $this->assertNull($result['website_check']);
    }

    public function testExtractDomainFromUrl(): void
    {
        $this->assertEquals('example-com', $this->service->extractDomain('https://www.example.com'));
        $this->assertEquals('example-com', $this->service->extractDomain('https://example.com'));
        $this->assertEquals('sub-example-com', $this->service->extractDomain('https://sub.example.com'));
        $this->assertNull($this->service->extractDomain('invalid-url'));
    }

    private function createMockClient(): Client
    {
        $client = $this->createMock(Client::class);
        $client->method('getId')->willReturn('test-uuid');
        $client->method('getName')->willReturn('Test Client');
        $client->method('getSlug')->willReturn('test-client');
        
        return $client;
    }
}
