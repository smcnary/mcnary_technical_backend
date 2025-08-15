<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\DateFilter;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\Table(name: 'audit_runs')]
#[ORM\HasLifecycleCallbacks]
#[ApiResource(
    operations: [
        new Get(security: "is_granted('ROLE_AGENCY_ADMIN') or is_granted('ROLE_AGENCY_STAFF') or (is_granted('ROLE_CLIENT_ADMIN') and object.getClientId() == user.getClientId())"),
        new GetCollection(security: "is_granted('ROLE_AGENCY_ADMIN') or is_granted('ROLE_AGENCY_STAFF') or is_granted('ROLE_CLIENT_ADMIN')"),
        new Post(security: "is_granted('ROLE_AGENCY_ADMIN') or is_granted('ROLE_CLIENT_ADMIN')"),
        new Put(security: "is_granted('ROLE_AGENCY_ADMIN') or (is_granted('ROLE_CLIENT_ADMIN') and object.getClientId() == user.getClientId())"),
        new Delete(security: "is_granted('ROLE_AGENCY_ADMIN')")
    ],
    normalizationContext: ['groups' => ['audit_run:read']],
    denormalizationContext: ['groups' => ['audit_run:write']]
)]
#[ApiFilter(SearchFilter::class, properties: [
    'name' => 'partial',
    'type' => 'exact',
    'status' => 'exact',
    'clientId' => 'exact'
])]
#[ApiFilter(OrderFilter::class, properties: ['createdAt' => 'DESC', 'startedAt' => 'DESC', 'name' => 'ASC'])]
#[ApiFilter(DateFilter::class, properties: ['createdAt', 'startedAt', 'completedAt'])]
class AuditRun
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    #[ApiProperty(identifier: true)]
    #[Groups(['audit_run:read'])]
    private string $id;

    #[ORM\Column(name: 'tenant_id', type: 'uuid', nullable: true)]
    private ?string $tenantId = null;

    #[ORM\Column(name: 'client_id', type: 'uuid')]
    #[Groups(['audit_run:read', 'audit_run:write'])]
    private string $clientId;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank]
    #[Groups(['audit_run:read', 'audit_run:write'])]
    private string $name;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['audit_run:read', 'audit_run:write'])]
    private ?string $description = null;

    #[ORM\Column(type: 'string', length: 50)]
    #[Assert\Choice(['seo', 'technical', 'content', 'local', 'mobile', 'accessibility', 'performance', 'security'])]
    #[Groups(['audit_run:read', 'audit_run:write'])]
    private string $type = 'seo';

    #[ORM\Column(type: 'string', length: 50)]
    #[Assert\Choice(['pending', 'running', 'completed', 'failed', 'cancelled'])]
    #[Groups(['audit_run:read', 'audit_run:write'])]
    private string $status = 'pending';

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['audit_run:read', 'audit_run:write'])]
    private ?string $targetUrl = null;

    #[ORM\Column(name: 'started_at', type: 'datetime_immutable', nullable: true)]
    #[Groups(['audit_run:read', 'audit_run:write'])]
    private ?\DateTimeImmutable $startedAt = null;

    #[ORM\Column(name: 'completed_at', type: 'datetime_immutable', nullable: true)]
    #[Groups(['audit_run:read', 'audit_run:write'])]
    private ?\DateTimeImmutable $completedAt = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    #[Groups(['audit_run:read', 'audit_run:write'])]
    private ?int $totalIssues = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    #[Groups(['audit_run:read', 'audit_run:write'])]
    private ?int $criticalIssues = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    #[Groups(['audit_run:read', 'audit_run:write'])]
    private ?int $highIssues = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    #[Groups(['audit_run:read', 'audit_run:write'])]
    private ?int $mediumIssues = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    #[Groups(['audit_run:read', 'audit_run:write'])]
    private ?int $lowIssues = null;

    #[ORM\Column(type: 'decimal', precision: 5, scale: 2, nullable: true)]
    #[Groups(['audit_run:read', 'audit_run:write'])]
    private ?float $score = null; // Overall audit score (0-100)

    #[ORM\Column(type: 'jsonb', nullable: true)]
    #[Groups(['audit_run:read', 'audit_run:write'])]
    private ?array $settings = [];

    #[ORM\Column(type: 'jsonb', nullable: true)]
    #[Groups(['audit_run:read', 'audit_run:write'])]
    private ?array $results = [];

    #[ORM\Column(type: 'jsonb', nullable: true)]
    #[Groups(['audit_run:read', 'audit_run:write'])]
    private ?array $metadata = [];

    #[ORM\Column(name: 'created_at', type: 'datetime_immutable')]
    #[Groups(['audit_run:read'])]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(name: 'updated_at', type: 'datetime_immutable')]
    #[Groups(['audit_run:read'])]
    private \DateTimeImmutable $updatedAt;

    public function __construct()
    {
        $this->id = Uuid::v4()->toRfc4122();
        $now = new \DateTimeImmutable();
        $this->createdAt = $now;
        $this->updatedAt = $now;
        $this->settings = [];
        $this->results = [];
        $this->metadata = [];
    }

    #[ORM\PreUpdate]
    public function setUpdatedAt(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }

    // Getters and Setters
    public function getId(): string
    {
        return $this->id;
    }

    public function getTenantId(): ?string
    {
        return $this->tenantId;
    }

    public function setTenantId(?string $tenantId): self
    {
        $this->tenantId = $tenantId;
        return $this;
    }

    public function getClientId(): string
    {
        return $this->clientId;
    }

    public function setClientId(string $clientId): self
    {
        $this->clientId = $clientId;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;
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

    public function getTargetUrl(): ?string
    {
        return $this->targetUrl;
    }

    public function setTargetUrl(?string $targetUrl): self
    {
        $this->targetUrl = $targetUrl;
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

    public function getTotalIssues(): ?int
    {
        return $this->totalIssues;
    }

    public function setTotalIssues(?int $totalIssues): self
    {
        $this->totalIssues = $totalIssues;
        return $this;
    }

    public function getCriticalIssues(): ?int
    {
        return $this->criticalIssues;
    }

    public function setCriticalIssues(?int $criticalIssues): self
    {
        $this->criticalIssues = $criticalIssues;
        return $this;
    }

    public function getHighIssues(): ?int
    {
        return $this->highIssues;
    }

    public function setHighIssues(?int $highIssues): self
    {
        $this->highIssues = $highIssues;
        return $this;
    }

    public function getMediumIssues(): ?int
    {
        return $this->mediumIssues;
    }

    public function setMediumIssues(?int $mediumIssues): self
    {
        $this->mediumIssues = $mediumIssues;
        return $this;
    }

    public function getLowIssues(): ?int
    {
        return $this->lowIssues;
    }

    public function setLowIssues(?int $lowIssues): self
    {
        $this->lowIssues = $lowIssues;
        return $this;
    }

    public function getScore(): ?float
    {
        return $this->score;
    }

    public function setScore(?float $score): self
    {
        $this->score = $score;
        return $this;
    }

    public function getSettings(): array
    {
        return $this->settings;
    }

    public function setSettings(array $settings): self
    {
        $this->settings = $settings;
        return $this;
    }

    public function getResults(): array
    {
        return $this->results;
    }

    public function setResults(array $results): self
    {
        $this->results = $results;
        return $this;
    }

    public function getMetadata(): array
    {
        return $this->metadata;
    }

    public function setMetadata(array $metadata): self
    {
        $this->metadata = $metadata;
        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }
}
