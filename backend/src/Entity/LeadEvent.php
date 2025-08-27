<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Delete;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\Table(name: 'lead_events')]
#[ORM\HasLifecycleCallbacks]
#[ApiResource(
    operations: [
        new Get(security: "is_granted('ROLE_AGENCY_ADMIN') or is_granted('ROLE_AGENCY_STAFF') or (is_granted('ROLE_CLIENT_ADMIN') and object.getLead().getClient().getClientId() == user.getClientId())"),
        new GetCollection(security: "is_granted('ROLE_AGENCY_ADMIN') or is_granted('ROLE_AGENCY_STAFF')"),
        new Post(security: "is_granted('ROLE_AGENCY_ADMIN') or is_granted('ROLE_AGENCY_STAFF') or is_granted('ROLE_CLIENT_ADMIN')"),
        new Put(security: "is_granted('ROLE_AGENCY_ADMIN') or is_granted('ROLE_AGENCY_STAFF')"),
        new Delete(security: "is_granted('ROLE_AGENCY_ADMIN')")
    ]
)]
class LeadEvent
{
    use Timestamps;

    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private string $id;

    #[ORM\ManyToOne(targetEntity: Lead::class, inversedBy: 'events')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Lead $lead;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true)]
    private ?User $createdBy = null;

    #[ORM\Column]
    #[Assert\NotBlank]
    private string $eventType; // status_change, note, call, email, meeting, follow_up

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: 'jsonb')]
    private array $payloadJson = [];

    #[ORM\Column(type: 'string', options: ['default' => 'info'])]
    private string $severity = 'info'; // info, warning, error, success

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $scheduledAt = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $completedAt = null;

    public function __construct(Lead $lead, string $eventType, ?User $createdBy = null)
    {
        $this->id = Uuid::v4()->toRfc4122();
        $this->lead = $lead;
        $this->eventType = $eventType;
        $this->createdBy = $createdBy;
        $this->payloadJson = [];
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getLead(): Lead
    {
        return $this->lead;
    }

    public function setLead(?Lead $lead): self
    {
        $this->lead = $lead;
        return $this;
    }

    public function getCreatedBy(): ?User
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?User $createdBy): self
    {
        $this->createdBy = $createdBy;
        return $this;
    }

    public function getEventType(): string
    {
        return $this->eventType;
    }

    public function setEventType(string $eventType): self
    {
        $this->eventType = $eventType;
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

    public function getPayloadJson(): array
    {
        return $this->payloadJson;
    }

    public function setPayloadJson(array $payloadJson): self
    {
        $this->payloadJson = $payloadJson;
        return $this;
    }

    public function getPayloadValue(string $key, $default = null)
    {
        return $this->payloadJson[$key] ?? $default;
    }

    public function setPayloadValue(string $key, $value): self
    {
        $this->payloadJson[$key] = $value;
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

    public function getScheduledAt(): ?\DateTimeImmutable
    {
        return $this->scheduledAt;
    }

    public function setScheduledAt(?\DateTimeImmutable $scheduledAt): self
    {
        $this->scheduledAt = $scheduledAt;
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

    public function isScheduled(): bool
    {
        return $this->scheduledAt !== null;
    }

    public function isCompleted(): bool
    {
        return $this->completedAt !== null;
    }

    public function isOverdue(): bool
    {
        if (!$this->isScheduled() || $this->isCompleted()) {
            return false;
        }
        
        return $this->scheduledAt < new \DateTimeImmutable('now');
    }

    public function markCompleted(): self
    {
        $this->completedAt = new \DateTimeImmutable('now');
        return $this;
    }

    public function getEventTypeLabel(): string
    {
        $labels = [
            'status_change' => 'Status Changed',
            'note' => 'Note Added',
            'call' => 'Phone Call',
            'email' => 'Email Sent',
            'meeting' => 'Meeting Scheduled',
            'follow_up' => 'Follow Up',
            'qualification' => 'Qualified',
            'disqualification' => 'Disqualified',
            'conversion' => 'Converted'
        ];
        
        return $labels[$this->eventType] ?? ucfirst(str_replace('_', ' ', $this->eventType));
    }
}
