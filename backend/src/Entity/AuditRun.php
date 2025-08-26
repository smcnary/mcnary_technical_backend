<?php

namespace App\Entity;

use ApiPlatform\Metadata as API;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

/**
 * ==========================
 * AUDIT RUN
 * ==========================
 */
#[ORM\Entity(repositoryClass: \App\Repository\AuditRunRepository::class)]
#[ORM\Table(name: 'audit_run')]
#[API\ApiResource(
    operations: [
        new API\GetCollection(uriTemplate: '/v1/audits/runs'),
        new API\Post(uriTemplate: '/v1/audits/runs'),
        new API\Get(uriTemplate: '/v1/audits/runs/{id}'),
        new API\Patch(uriTemplate: '/v1/audits/runs/{id}')
    ],
    security: "is_granted('ROLE_AGENCY_STAFF') or is_granted('ROLE_CLIENT_ADMIN')"
)]
class AuditRun
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    private Uuid $id;

    #[ORM\ManyToOne(targetEntity: Client::class, inversedBy: 'auditRuns')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Client $client;

    #[ORM\ManyToOne(targetEntity: AuditIntake::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private AuditIntake $intake;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'initiatedAuditRuns')]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    private ?User $initiatedBy = null;

    #[ORM\Column(length: 16)]
    private string $status = 'queued';

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $scope = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $startedAt = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $completedAt = null;

    public function __construct() 
    { 
        $this->id = Uuid::v7(); 
    }

    // Getters and Setters
    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getClient(): Client
    {
        return $this->client;
    }

    public function setClient(Client $client): self
    {
        $this->client = $client;
        return $this;
    }

    public function getIntake(): AuditIntake
    {
        return $this->intake;
    }

    public function setIntake(AuditIntake $intake): self
    {
        $this->intake = $intake;
        return $this;
    }

    public function getInitiatedBy(): ?User
    {
        return $this->initiatedBy;
    }

    public function setInitiatedBy(?User $initiatedBy): self
    {
        $this->initiatedBy = $initiatedBy;
        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function getScope(): ?array
    {
        return $this->scope;
    }

    public function setScope(?array $scope): self
    {
        $this->scope = $scope;
        return $this;
    }

    public function getStartedAt(): ?\DateTimeImmutable
    {
        return $this->startedAt;
    }

    public function setStartedAt(?\DateTimeImmutable $startedAt): self
    {
        $this->startedAt = $startedAt;
        return $this;
    }

    public function getCompletedAt(): ?\DateTimeImmutable
    {
        return $this->completedAt;
    }

    public function setCompletedAt(?\DateTimeImmutable $completedAt): self
    {
        $this->completedAt = $completedAt;
        return $this;
    }
}
