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
#[ORM\Table(name: 'campaigns', uniqueConstraints: [
    new ORM\UniqueConstraint(name: 'uniq_client_name', columns: ['client_id','name'])
])]
#[ORM\HasLifecycleCallbacks]
#[ApiResource(
    operations: [
        new Get(security: "is_granted('ROLE_AGENCY_ADMIN') or is_granted('ROLE_AGENCY_STAFF') or (is_granted('ROLE_CLIENT_ADMIN') and object.getClient().getClientId() == user.getClientId())"),
        new GetCollection(security: "is_granted('ROLE_AGENCY_ADMIN') or is_granted('ROLE_AGENCY_STAFF') or is_granted('ROLE_CLIENT_ADMIN')"),
        new Post(security: "is_granted('ROLE_AGENCY_ADMIN') or is_granted('ROLE_CLIENT_ADMIN')"),
        new Put(security: "is_granted('ROLE_AGENCY_ADMIN') or (is_granted('ROLE_CLIENT_ADMIN') and object.getClient().getClientId() == user.getClientId())"),
        new Delete(security: "is_granted('ROLE_AGENCY_ADMIN')")
    ],
    normalizationContext: ['groups' => ['campaign:read']],
    denormalizationContext: ['groups' => ['campaign:write']]
)]
#[ApiFilter(SearchFilter::class, properties: [
    'name' => 'partial',
    'status' => 'exact',
    'type' => 'exact'
])]
#[ApiFilter(OrderFilter::class, properties: ['createdAt' => 'DESC', 'name' => 'ASC'])]
#[ApiFilter(DateFilter::class, properties: ['startDate', 'endDate', 'createdAt'])]
class Campaign
{
    use Timestamps;

    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    #[ApiProperty(identifier: true)]
    #[Groups(['campaign:read'])]
    private string $id;

    #[ORM\ManyToOne(inversedBy: 'campaigns')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Client $client;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank]
    #[Groups(['campaign:read', 'campaign:write'])]
    private string $name;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['campaign:read', 'campaign:write'])]
    private ?string $description = null;

    #[ORM\Column(type: 'string', length: 50)]
    #[Assert\Choice(['seo', 'ppc', 'social', 'content', 'email', 'other'])]
    #[Groups(['campaign:read', 'campaign:write'])]
    private string $type = 'seo';

    #[ORM\Column(type: 'string', length: 50)]
    #[Assert\Choice(['draft', 'active', 'paused', 'completed', 'cancelled'])]
    #[Groups(['campaign:read', 'campaign:write'])]
    private string $status = 'draft';

    #[ORM\Column(name: 'start_date', type: 'datetime_immutable', nullable: true)]
    #[Groups(['campaign:read', 'campaign:write'])]
    private ?\DateTimeImmutable $startDate = null;

    #[ORM\Column(name: 'end_date', type: 'datetime_immutable', nullable: true)]
    #[Groups(['campaign:read', 'campaign:write'])]
    private ?\DateTimeImmutable $endDate = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2, nullable: true)]
    #[Groups(['campaign:read', 'campaign:write'])]
    private ?string $budget = null;

    #[ORM\Column(type: 'jsonb', nullable: true)]
    #[Groups(['campaign:read', 'campaign:write'])]
    private ?array $goals = [];

    #[ORM\Column(type: 'jsonb', nullable: true)]
    #[Groups(['campaign:read', 'campaign:write'])]
    private ?array $metrics = [];

    #[ORM\Column(type: 'jsonb', nullable: true)]
    #[Groups(['campaign:read', 'campaign:write'])]
    private ?array $metadata = [];

    public function __construct(Client $client, string $name, string $type)
    {
        $this->id = Uuid::v4()->toRfc4122();
        $this->client = $client;
        $this->name = $name;
        $this->type = $type;
        $this->goals = [];
        $this->metrics = [];
        $this->metadata = [];
    }

    public function getId(): string
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

    public function getStartDate(): ?\DateTimeImmutable
    {
        return $this->startDate;
    }

    public function setStartDate(?\DateTimeImmutable $startDate): self
    {
        $this->startDate = $startDate;
        return $this;
    }

    public function getEndDate(): ?\DateTimeImmutable
    {
        return $this->endDate;
    }

    public function setEndDate(?\DateTimeImmutable $endDate): self
    {
        $this->endDate = $endDate;
        return $this;
    }

    public function getBudget(): ?string
    {
        return $this->budget;
    }

    public function setBudget(?string $budget): self
    {
        $this->budget = $budget;
        return $this;
    }

    public function getGoals(): ?array
    {
        return $this->goals;
    }

    public function setGoals(?array $goals): self
    {
        $this->goals = $goals;
        return $this;
    }

    public function getMetrics(): ?array
    {
        return $this->metrics;
    }

    public function setMetrics(?array $metrics): self
    {
        $this->metrics = $metrics;
        return $this;
    }

    public function getMetadata(): ?array
    {
        return $this->metadata;
    }

    public function setMetadata(?array $metadata): self
    {
        $this->metadata = $metadata;
        return $this;
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isPaused(): bool
    {
        return $this->status === 'paused';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    public function getDuration(): ?\DateInterval
    {
        if ($this->startDate && $this->endDate) {
            return $this->startDate->diff($this->endDate);
        }
        return null;
    }

    public function getDaysRemaining(): ?int
    {
        if ($this->endDate) {
            $now = new \DateTimeImmutable('now');
            $diff = $this->endDate->diff($now);
            return $diff->invert ? $diff->days : -$diff->days;
        }
        return null;
    }

    // Legacy getter for backward compatibility
    public function getClientId(): string
    {
        return $this->client->getId();
    }

    public function setClientId(string $clientId): self
    {
        // This method is kept for backward compatibility but should not be used
        // Use setClient() instead
        return $this;
    }
}
