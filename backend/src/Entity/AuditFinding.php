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
#[ORM\Table(name: 'audit_findings')]
#[ORM\HasLifecycleCallbacks]
#[ApiResource(
    operations: [
        new Get(security: "is_granted('ROLE_AGENCY_ADMIN') or is_granted('ROLE_AGENCY_STAFF') or (is_granted('ROLE_CLIENT_ADMIN') and object.getClientId() == user.getClientId())"),
        new GetCollection(security: "is_granted('ROLE_AGENCY_ADMIN') or is_granted('ROLE_AGENCY_STAFF') or is_granted('ROLE_CLIENT_ADMIN')"),
        new Post(security: "is_granted('ROLE_AGENCY_ADMIN') or is_granted('ROLE_CLIENT_ADMIN')"),
        new Put(security: "is_granted('ROLE_AGENCY_ADMIN') or (is_granted('ROLE_CLIENT_ADMIN') and object.getClientId() == user.getClientId())"),
        new Delete(security: "is_granted('ROLE_AGENCY_ADMIN')")
    ],
    normalizationContext: ['groups' => ['audit_finding:read']],
    denormalizationContext: ['groups' => ['audit_finding:write']]
)]
#[ApiFilter(SearchFilter::class, properties: [
    'title' => 'partial',
    'severity' => 'exact',
    'status' => 'exact',
    'auditRunId' => 'exact',
    'clientId' => 'exact'
])]
#[ApiFilter(OrderFilter::class, properties: ['createdAt' => 'DESC', 'severity' => 'DESC', 'title' => 'ASC'])]
#[ApiFilter(DateFilter::class, properties: ['createdAt', 'updatedAt'])]
class AuditFinding
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    #[ApiProperty(identifier: true)]
    #[Groups(['audit_finding:read'])]
    private string $id;

    #[ORM\Column(name: 'tenant_id', type: 'uuid', nullable: true)]
    private ?string $tenantId = null;

    #[ORM\Column(name: 'client_id', type: 'uuid')]
    #[Groups(['audit_finding:read', 'audit_finding:write'])]
    private string $clientId;

    #[ORM\Column(name: 'audit_run_id', type: 'uuid')]
    #[Groups(['audit_finding:read', 'audit_finding:write'])]
    private string $auditRunId;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank]
    #[Groups(['audit_finding:read', 'audit_finding:write'])]
    private string $title;

    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank]
    #[Groups(['audit_finding:read', 'audit_finding:write'])]
    private string $description;

    #[ORM\Column(type: 'string', length: 20)]
    #[Assert\Choice(['critical', 'high', 'medium', 'low', 'info'])]
    #[Groups(['audit_finding:read', 'audit_finding:write'])]
    private string $severity = 'medium';

    #[ORM\Column(type: 'string', length: 50)]
    #[Assert\Choice(['open', 'in_progress', 'resolved', 'wont_fix', 'false_positive'])]
    #[Groups(['audit_finding:read', 'audit_finding:write'])]
    private string $status = 'open';

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['audit_finding:read', 'audit_finding:write'])]
    private ?string $category = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['audit_finding:read', 'audit_finding:write'])]
    private ?string $location = null; // URL, line number, element, etc.

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['audit_finding:read', 'audit_finding:write'])]
    private ?string $impact = null;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['audit_finding:read', 'audit_finding:write'])]
    private ?string $recommendation = null;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['audit_finding:read', 'audit_finding:write'])]
    private ?string $codeExample = null;

    #[ORM\Column(type: 'decimal', precision: 5, scale: 2, nullable: true)]
    #[Groups(['audit_finding:read', 'audit_finding:write'])]
    private ?float $score = null; // Impact score (0-100)

    #[ORM\Column(type: 'jsonb', nullable: true)]
    #[Groups(['audit_finding:read', 'audit_finding:write'])]
    private ?array $evidence = [];

    #[ORM\Column(type: 'jsonb', nullable: true)]
    #[Groups(['audit_finding:read', 'audit_finding:write'])]
    private ?array $references = [];

    #[ORM\Column(type: 'jsonb', nullable: true)]
    #[Groups(['audit_finding:read', 'audit_finding:write'])]
    private ?array $metadata = [];

    #[ORM\Column(name: 'created_at', type: 'datetime_immutable')]
    #[Groups(['audit_finding:read'])]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(name: 'updated_at', type: 'datetime_immutable')]
    #[Groups(['audit_finding:read'])]
    private \DateTimeImmutable $updatedAt;

    public function __construct()
    {
        $this->id = Uuid::v4()->toRfc4122();
        $now = new \DateTimeImmutable();
        $this->createdAt = $now;
        $this->updatedAt = $now;
        $this->evidence = [];
        $this->references = [];
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

    public function getAuditRunId(): string
    {
        return $this->auditRunId;
    }

    public function setAuditRunId(string $auditRunId): self
    {
        $this->auditRunId = $auditRunId;
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

    public function getCodeExample(): ?string
    {
        return $this->codeExample;
    }

    public function setCodeExample(?string $codeExample): self
    {
        $this->codeExample = $codeExample;
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

    public function getEvidence(): array
    {
        return $this->evidence;
    }

    public function setEvidence(array $evidence): self
    {
        $this->evidence = $evidence;
        return $this;
    }

    public function getReferences(): array
    {
        return $this->references;
    }

    public function setReferences(array $references): self
    {
        $this->references = $references;
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
