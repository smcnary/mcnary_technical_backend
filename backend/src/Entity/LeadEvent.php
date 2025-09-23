<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use App\Repository\LeadEventRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: LeadEventRepository::class)]
#[ORM\Table(name: 'lead_events')]
#[ORM\HasLifecycleCallbacks]
#[ApiResource(
    operations: [
        new Post(
            normalizationContext: ['groups' => ['lead_event:read']],
            denormalizationContext: ['groups' => ['lead_event:write']],
            security: "is_granted('ROLE_AGENCY_ADMIN') or is_granted('ROLE_AGENCY_STAFF')"
        ),
        new Get(
            normalizationContext: ['groups' => ['lead_event:read']],
            security: "is_granted('ROLE_AGENCY_ADMIN') or is_granted('ROLE_AGENCY_STAFF')"
        ),
        new GetCollection(
            normalizationContext: ['groups' => ['lead_event:read']],
            security: "is_granted('ROLE_AGENCY_ADMIN') or is_granted('ROLE_AGENCY_STAFF')"
        ),
        new Patch(
            normalizationContext: ['groups' => ['lead_event:read']],
            denormalizationContext: ['groups' => ['lead_event:write']],
            security: "is_granted('ROLE_AGENCY_ADMIN') or is_granted('ROLE_AGENCY_STAFF')"
        ),
    ],
    paginationItemsPerPage: 50
)]
#[ApiFilter(DateFilter::class, properties: ['createdAt'])]
#[ApiFilter(OrderFilter::class, properties: ['createdAt' => 'DESC'], arguments: ['orderParameterName' => 'order'])]
class LeadEvent
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    #[Groups(['lead_event:read'])]
    #[ApiProperty(identifier: true)]
    private string $id;

    #[ORM\ManyToOne(targetEntity: Lead::class, inversedBy: 'events')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    #[Groups(['lead_event:read', 'lead_event:write'])]
    private Lead $lead;

    #[ORM\Column(type: Types::STRING, length: 50)]
    #[Assert\NotBlank]
    #[Assert\Choice(['phone_call', 'email', 'meeting', 'note', 'application'])]
    #[Groups(['lead_event:read', 'lead_event:write'])]
    private string $type;

    #[ORM\Column(type: Types::STRING, length: 20, nullable: true)]
    #[Assert\Choice(['inbound', 'outbound'])]
    #[Groups(['lead_event:read', 'lead_event:write'])]
    private ?string $direction = null;

    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    #[Assert\PositiveOrZero]
    #[Groups(['lead_event:read', 'lead_event:write'])]
    private ?int $duration = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['lead_event:read', 'lead_event:write'])]
    private ?string $notes = null;

    #[ORM\Column(type: Types::STRING, length: 20, nullable: true)]
    #[Assert\Choice(['positive', 'neutral', 'negative'])]
    #[Groups(['lead_event:read', 'lead_event:write'])]
    private ?string $outcome = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['lead_event:read', 'lead_event:write'])]
    private ?string $nextAction = null;

    #[ORM\Column(type: 'datetime_immutable')]
    #[Groups(['lead_event:read'])]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable')]
    #[Groups(['lead_event:read'])]
    private \DateTimeImmutable $updatedAt;

    #[ORM\PrePersist]
    public function setCreatedAtValue(): void
    {
        $now = new \DateTimeImmutable('now');
        $this->createdAt = $now;
        $this->updatedAt = $now;
    }

    #[ORM\PreUpdate]
    public function setUpdatedAtValue(): void
    {
        $this->updatedAt = new \DateTimeImmutable('now');
    }

    public function __construct()
    {
        $this->id = Uuid::v4()->toRfc4122();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getLead(): Lead
    {
        return $this->lead;
    }

    public function setLead(Lead $lead): self
    {
        $this->lead = $lead;
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

    public function getDirection(): ?string
    {
        return $this->direction;
    }

    public function setDirection(?string $direction): self
    {
        $this->direction = $direction;
        return $this;
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(?int $duration): self
    {
        $this->duration = $duration;
        return $this;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(?string $notes): self
    {
        $this->notes = $notes;
        return $this;
    }

    public function getOutcome(): ?string
    {
        return $this->outcome;
    }

    public function setOutcome(?string $outcome): self
    {
        $this->outcome = $outcome;
        return $this;
    }

    public function getNextAction(): ?string
    {
        return $this->nextAction;
    }

    public function setNextAction(?string $nextAction): self
    {
        $this->nextAction = $nextAction;
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

    /**
     * Get event type label for display
     */
    #[Groups(['lead_event:read'])]
    public function getTypeLabel(): string
    {
        $labels = [
            'phone_call' => 'Phone Call',
            'email' => 'Email',
            'meeting' => 'Meeting',
            'note' => 'Note',
            'application' => 'Application'
        ];
        
        return $labels[$this->type] ?? $this->type;
    }

    /**
     * Get outcome label for display
     */
    #[Groups(['lead_event:read'])]
    public function getOutcomeLabel(): ?string
    {
        if (!$this->outcome) {
            return null;
        }
        
        $labels = [
            'positive' => 'Positive',
            'neutral' => 'Neutral',
            'negative' => 'Negative'
        ];
        
        return $labels[$this->outcome] ?? $this->outcome;
    }

    /**
     * Get direction label for display
     */
    #[Groups(['lead_event:read'])]
    public function getDirectionLabel(): ?string
    {
        if (!$this->direction) {
            return null;
        }
        
        $labels = [
            'inbound' => 'Inbound',
            'outbound' => 'Outbound'
        ];
        
        return $labels[$this->direction] ?? $this->direction;
    }
}