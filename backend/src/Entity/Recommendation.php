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
#[ORM\Table(name: 'recommendations')]
#[ORM\HasLifecycleCallbacks]
#[ApiResource(
    operations: [
        new Get(security: "is_granted('ROLE_AGENCY_ADMIN') or is_granted('ROLE_AGENCY_STAFF') or (is_granted('ROLE_CLIENT_ADMIN') and object.getClientId() == user.getClientId())"),
        new GetCollection(security: "is_granted('ROLE_AGENCY_ADMIN') or is_granted('ROLE_AGENCY_STAFF') or is_granted('ROLE_CLIENT_ADMIN')"),
        new Post(security: "is_granted('ROLE_AGENCY_ADMIN') or is_granted('ROLE_CLIENT_ADMIN')"),
        new Put(security: "is_granted('ROLE_AGENCY_ADMIN') or (is_granted('ROLE_CLIENT_ADMIN') and object.getClientId() == user.getClientId())"),
        new Delete(security: "is_granted('ROLE_AGENCY_ADMIN')")
    ],
    normalizationContext: ['groups' => ['recommendation:read']],
    denormalizationContext: ['groups' => ['recommendation:write']]
)]
#[ApiFilter(SearchFilter::class, properties: [
    'title' => 'partial',
    'category' => 'exact',
    'priority' => 'exact',
    'status' => 'exact',
    'clientId' => 'exact'
])]
#[ApiFilter(OrderFilter::class, properties: ['createdAt' => 'DESC', 'priority' => 'DESC', 'dueDate' => 'ASC'])]
#[ApiFilter(DateFilter::class, properties: ['createdAt', 'dueDate', 'updatedAt'])]
class Recommendation
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    #[ApiProperty(identifier: true)]
    #[Groups(['recommendation:read'])]
    private string $id;

    #[ORM\Column(name: 'tenant_id', type: 'uuid', nullable: true)]
    private ?string $tenantId = null;

    #[ORM\Column(name: 'client_id', type: 'uuid')]
    #[Groups(['recommendation:read', 'recommendation:write'])]
    private string $clientId;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank]
    #[Groups(['recommendation:read', 'recommendation:write'])]
    private string $title;

    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank]
    #[Groups(['recommendation:read', 'recommendation:write'])]
    private string $description;

    #[ORM\Column(type: 'string', length: 50)]
    #[Assert\Choice(['seo', 'technical', 'content', 'local', 'mobile', 'accessibility', 'performance', 'security', 'other'])]
    #[Groups(['recommendation:read', 'recommendation:write'])]
    private string $category = 'seo';

    #[ORM\Column(type: 'string', length: 20)]
    #[Assert\Choice(['low', 'medium', 'high', 'critical'])]
    #[Groups(['recommendation:read', 'recommendation:write'])]
    private string $priority = 'medium';

    #[ORM\Column(type: 'string', length: 50)]
    #[Assert\Choice(['todo', 'in_progress', 'completed', 'cancelled', 'deferred'])]
    #[Groups(['recommendation:read', 'recommendation:write'])]
    private string $status = 'todo';

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['recommendation:read', 'recommendation:write'])]
    private ?string $rationale = null;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['recommendation:read', 'recommendation:write'])]
    private ?string $implementation = null;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['recommendation:read', 'recommendation:write'])]
    private ?string $expectedOutcome = null;

    #[ORM\Column(name: 'due_date', type: 'datetime_immutable', nullable: true)]
    #[Groups(['recommendation:read', 'recommendation:write'])]
    private ?\DateTimeImmutable $dueDate = null;

    #[ORM\Column(name: 'completed_at', type: 'datetime_immutable', nullable: true)]
    #[Groups(['recommendation:read', 'recommendation:write'])]
    private ?\DateTimeImmutable $completedAt = null;

    #[ORM\Column(name: 'assigned_to', type: 'uuid', nullable: true)]
    #[Groups(['recommendation:read', 'recommendation:write'])]
    private ?string $assignedTo = null;

    #[ORM\Column(type: 'decimal', precision: 5, scale: 2, nullable: true)]
    #[Groups(['recommendation:read', 'recommendation:write'])]
    private ?float $estimatedEffort = null; // Hours

    #[ORM\Column(type: 'decimal', precision: 5, scale: 2, nullable: true)]
    #[Groups(['recommendation:read', 'recommendation:write'])]
    private ?float $estimatedCost = null;

    #[ORM\Column(type: 'jsonb', nullable: true)]
    #[Groups(['recommendation:read', 'recommendation:write'])]
    private ?array $tags = [];

    #[ORM\Column(type: 'jsonb', nullable: true)]
    #[Groups(['recommendation:read', 'recommendation:write'])]
    private ?array $metadata = [];

    #[ORM\Column(name: 'created_at', type: 'datetime_immutable')]
    #[Groups(['recommendation:read'])]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(name: 'updated_at', type: 'datetime_immutable')]
    #[Groups(['recommendation:read'])]
    private \DateTimeImmutable $updatedAt;

    public function __construct()
    {
        $this->id = Uuid::v4()->toRfc4122();
        $now = new \DateTimeImmutable();
        $this->createdAt = $now;
        $this->updatedAt = $now;
        $this->tags = [];
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

    public function getCategory(): string
    {
        return $this->category;
    }

    public function setCategory(string $category): self
    {
        $this->category = $category;
        return $this;
    }

    public function getPriority(): string
    {
        return $this->priority;
    }

    public function setPriority(string $priority): self
    {
        $this->priority = $priority;
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

    public function getRationale(): ?string
    {
        return $this->rationale;
    }

    public function setRationale(?string $rationale): self
    {
        $this->rationale = $rationale;
        return $this;
    }

    public function getImplementation(): ?string
    {
        return $this->implementation;
    }

    public function setImplementation(?string $implementation): self
    {
        $this->implementation = $implementation;
        return $this;
    }

    public function getExpectedOutcome(): ?string
    {
        return $this->expectedOutcome;
    }

    public function setExpectedOutcome(?string $expectedOutcome): self
    {
        $this->expectedOutcome = $expectedOutcome;
        return $this;
    }

    public function getDueDate(): ?\DateTimeImmutable
    {
        return $this->dueDate;
    }

    public function setDueDate(?\DateTimeImmutable $dueDate): self
    {
        $this->dueDate = $dueDate;
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

    public function getAssignedTo(): ?string
    {
        return $this->assignedTo;
    }

    public function setAssignedTo(?string $assignedTo): self
    {
        $this->assignedTo = $assignedTo;
        return $this;
    }

    public function getEstimatedEffort(): ?float
    {
        return $this->estimatedEffort;
    }

    public function setEstimatedEffort(?float $estimatedEffort): self
    {
        $this->estimatedEffort = $estimatedEffort;
        return $this;
    }

    public function getEstimatedCost(): ?float
    {
        return $this->estimatedCost;
    }

    public function setEstimatedCost(?float $estimatedCost): self
    {
        $this->estimatedCost = $estimatedCost;
        return $this;
    }

    public function getTags(): array
    {
        return $this->tags;
    }

    public function setTags(array $tags): self
    {
        $this->tags = $tags;
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
