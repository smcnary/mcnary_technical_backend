<?php

namespace App\Entity;

use ApiPlatform\Metadata as API;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

/**
 * ==========================
 * AUDIT FINDING
 * ==========================
 */
#[ORM\Entity(repositoryClass: \App\Repository\AuditFindingRepository::class)]
#[ORM\Table(name: 'audit_finding')]
#[API\ApiResource(
    operations: [
        new API\GetCollection(uriTemplate: '/v1/audits/findings'),
        new API\Post(uriTemplate: '/v1/audits/findings'),
        new API\Get(uriTemplate: '/v1/audits/findings/{id}'),
        new API\Patch(uriTemplate: '/v1/audits/findings/{id}')
    ],
    security: "is_granted('ROLE_AGENCY_STAFF') or is_granted('ROLE_CLIENT_ADMIN')"
)]
class AuditFinding
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    private Uuid $id;

    #[ORM\ManyToOne(targetEntity: Client::class, inversedBy: 'auditFindings')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Client $client;

    #[ORM\ManyToOne(targetEntity: AuditRun::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private AuditRun $auditRun;

    #[ORM\Column(length: 255)]
    private string $title;

    #[ORM\Column(type: Types::TEXT)]
    private string $description;

    #[ORM\Column(length: 16)]
    private string $severity = 'medium';

    #[ORM\Column(length: 16)]
    private string $status = 'open';

    #[ORM\Column(length: 64, nullable: true)]
    private ?string $category = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $location = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $impact = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $recommendation = null;

    #[ORM\Column(type: Types::SMALLINT, options: ['default' => 3])]
    private int $impactScore = 3;

    #[ORM\Column(type: Types::SMALLINT, options: ['default' => 3])]
    private int $effortScore = 3;

    #[ORM\Column(type: Types::SMALLINT, options: ['default' => 3])]
    private int $priorityScore = 3;

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

    public function getAuditRun(): AuditRun
    {
        return $this->auditRun;
    }

    public function setAuditRun(AuditRun $auditRun): self
    {
        $this->auditRun = $auditRun;
        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getSeverity(): string
    {
        return $this->severity;
    }

    public function setSeverity(string $severity): self
    {
        $this->severity = $severity;
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

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(?string $category): self
    {
        $this->category = $category;
        return $this;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(?string $location): self
    {
        $this->location = $location;
        return $this;
    }

    public function getImpact(): ?string
    {
        return $this->impact;
    }

    public function setImpact(?string $impact): self
    {
        $this->impact = $impact;
        return $this;
    }

    public function getRecommendation(): ?string
    {
        return $this->recommendation;
    }

    public function setRecommendation(?string $recommendation): self
    {
        $this->recommendation = $recommendation;
        return $this;
    }

    public function getImpactScore(): int
    {
        return $this->impactScore;
    }

    public function setImpactScore(int $impactScore): self
    {
        $this->impactScore = $impactScore;
        return $this;
    }

    public function getEffortScore(): int
    {
        return $this->effortScore;
    }

    public function setEffortScore(int $effortScore): self
    {
        $this->effortScore = $effortScore;
        return $this;
    }

    public function getPriorityScore(): int
    {
        return $this->priorityScore;
    }

    public function setPriorityScore(int $priorityScore): self
    {
        $this->priorityScore = $priorityScore;
        return $this;
    }
}
