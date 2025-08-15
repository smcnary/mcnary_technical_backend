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
#[ORM\Table(name: 'content_briefs')]
#[ORM\HasLifecycleCallbacks]
#[ApiResource(
    operations: [
        new Get(security: "is_granted('ROLE_AGENCY_ADMIN') or is_granted('ROLE_AGENCY_STAFF') or (is_granted('ROLE_CLIENT_ADMIN') and object.getClientId() == user.getClientId())"),
        new GetCollection(security: "is_granted('ROLE_AGENCY_ADMIN') or is_granted('ROLE_AGENCY_STAFF') or is_granted('ROLE_CLIENT_ADMIN')"),
        new Post(security: "is_granted('ROLE_AGENCY_ADMIN') or is_granted('ROLE_CLIENT_ADMIN')"),
        new Put(security: "is_granted('ROLE_AGENCY_ADMIN') or (is_granted('ROLE_CLIENT_ADMIN') and object.getClientId() == user.getClientId())"),
        new Delete(security: "is_granted('ROLE_AGENCY_ADMIN')")
    ],
    normalizationContext: ['groups' => ['content_brief:read']],
    denormalizationContext: ['groups' => ['content_brief:write']]
)]
#[ApiFilter(SearchFilter::class, properties: [
    'title' => 'partial',
    'status' => 'exact',
    'clientId' => 'exact',
    'contentItemId' => 'exact',
    'assignedTo' => 'exact'
])]
#[ApiFilter(OrderFilter::class, properties: ['createdAt' => 'DESC', 'dueDate' => 'ASC', 'priority' => 'DESC'])]
#[ApiFilter(DateFilter::class, properties: ['createdAt', 'dueDate', 'updatedAt'])]
class ContentBrief
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    #[ApiProperty(identifier: true)]
    #[Groups(['content_brief:read'])]
    private string $id;

    #[ORM\Column(name: 'tenant_id', type: 'uuid', nullable: true)]
    private ?string $tenantId = null;

    #[ORM\Column(name: 'client_id', type: 'uuid')]
    #[Groups(['content_brief:read', 'content_brief:write'])]
    private string $clientId;

    #[ORM\Column(name: 'content_item_id', type: 'uuid', nullable: true)]
    #[Groups(['content_brief:read', 'content_brief:write'])]
    private ?string $contentItemId = null;

    #[ORM\Column(name: 'assigned_to', type: 'uuid', nullable: true)]
    #[Groups(['content_brief:read', 'content_brief:write'])]
    private ?string $assignedTo = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank]
    #[Groups(['content_brief:read', 'content_brief:write'])]
    private string $title;

    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank]
    #[Groups(['content_brief:read', 'content_brief:write'])]
    private string $description;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['content_brief:read', 'content_brief:write'])]
    private ?string $targetAudience = null;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['content_brief:read', 'content_brief:write'])]
    private ?string $keyMessages = null;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['content_brief:read', 'content_brief:write'])]
    private ?string $callToAction = null;

    #[ORM\Column(type: 'string', length: 50)]
    #[Assert\Choice(['draft', 'in_review', 'approved', 'in_progress', 'completed', 'cancelled'])]
    #[Groups(['content_brief:read', 'content_brief:write'])]
    private string $status = 'draft';

    #[ORM\Column(type: 'string', length: 20)]
    #[Assert\Choice(['low', 'medium', 'high', 'urgent'])]
    #[Groups(['content_brief:read', 'content_brief:write'])]
    private string $priority = 'medium';

    #[ORM\Column(name: 'due_date', type: 'datetime_immutable', nullable: true)]
    #[Groups(['content_brief:read', 'content_brief:write'])]
    private ?\DateTimeImmutable $dueDate = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    #[Groups(['content_brief:read', 'content_brief:write'])]
    private ?int $estimatedWordCount = null;

    #[ORM\Column(type: 'jsonb', nullable: true)]
    #[Groups(['content_brief:read', 'content_brief:write'])]
    private ?array $keywords = [];

    #[ORM\Column(type: 'jsonb', nullable: true)]
    #[Groups(['content_brief:read', 'content_brief:write'])]
    private ?array $references = [];

    #[ORM\Column(type: 'jsonb', nullable: true)]
    #[Groups(['content_brief:read', 'content_brief:write'])]
    private ?array $requirements = [];

    #[ORM\Column(type: 'jsonb', nullable: true)]
    #[Groups(['content_brief:read', 'content_brief:write'])]
    private ?array $metadata = [];

    #[ORM\Column(name: 'created_at', type: 'datetime_immutable')]
    #[Groups(['content_brief:read'])]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(name: 'updated_at', type: 'datetime_immutable')]
    #[Groups(['content_brief:read'])]
    private \DateTimeImmutable $updatedAt;

    public function __construct()
    {
        $this->id = Uuid::v4()->toRfc4122();
        $now = new \DateTimeImmutable();
        $this->createdAt = $now;
        $this->updatedAt = $now;
        $this->keywords = [];
        $this->references = [];
        $this->requirements = [];
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

    public function getContentItemId(): ?string
    {
        return $this->contentItemId;
    }

    public function setContentItemId(?string $contentItemId): self
    {
        $this->contentItemId = $contentItemId;
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

    public function getTargetAudience(): ?string
    {
        return $this->targetAudience;
    }

    public function setTargetAudience(?string $targetAudience): self
    {
        $this->targetAudience = $targetAudience;
        return $this;
    }

    public function getKeyMessages(): ?string
    {
        return $this->keyMessages;
    }

    public function setKeyMessages(?string $keyMessages): self
    {
        $this->keyMessages = $keyMessages;
        return $this;
    }

    public function getCallToAction(): ?string
    {
        return $this->callToAction;
    }

    public function setCallToAction(?string $callToAction): self
    {
        $this->callToAction = $callToAction;
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

    public function getPriority(): string
    {
        return $this->priority;
    }

    public function setPriority(string $priority): self
    {
        $this->priority = $priority;
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

    public function getEstimatedWordCount(): ?int
    {
        return $this->estimatedWordCount;
    }

    public function setEstimatedWordCount(?int $estimatedWordCount): self
    {
        $this->estimatedWordCount = $estimatedWordCount;
        return $this;
    }

    public function getKeywords(): array
    {
        return $this->keywords;
    }

    public function setKeywords(array $keywords): self
    {
        $this->keywords = $keywords;
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

    public function getRequirements(): array
    {
        return $this->requirements;
    }

    public function setRequirements(array $requirements): self
    {
        $this->requirements = $requirements;
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
