<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\Table(name: 'openphone_call_logs')]
#[ORM\HasLifecycleCallbacks]
#[ApiResource(
    operations: [
        new Get(security: "is_granted('ROLE_AGENCY_ADMIN') or is_granted('ROLE_AGENCY_STAFF') or (is_granted('ROLE_CLIENT_ADMIN') and object.getClient().getClientId() == user.getClientId())"),
        new GetCollection(security: "is_granted('ROLE_AGENCY_ADMIN') or is_granted('ROLE_AGENCY_STAFF')"),
        new Post(security: "is_granted('ROLE_AGENCY_ADMIN') or is_granted('ROLE_AGENCY_STAFF')")
    ]
)]
class OpenPhoneCallLog
{
    use Timestamps;

    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private string $id;

    #[ORM\ManyToOne(targetEntity: Client::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    #[Assert\NotNull]
    private Client $client;

    #[ORM\ManyToOne(targetEntity: OpenPhoneIntegration::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    #[Assert\NotNull]
    private OpenPhoneIntegration $integration;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private string $openPhoneCallId;

    #[ORM\Column(length: 32)]
    #[Assert\Choice(['inbound', 'outbound'])]
    private string $direction;

    #[ORM\Column(length: 32)]
    #[Assert\Choice(['answered', 'missed', 'voicemail', 'busy', 'failed'])]
    private string $status;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $fromNumber = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $toNumber = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $duration = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $startedAt = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $endedAt = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $recordingUrl = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $transcript = null;

    #[ORM\Column(type: 'jsonb', nullable: true)]
    private ?array $metadata = [];

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $isFollowUpRequired = false;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $notes = null;

    public function __construct(Client $client, OpenPhoneIntegration $integration, string $openPhoneCallId)
    {
        $this->id = Uuid::v4()->toRfc4122();
        $this->client = $client;
        $this->integration = $integration;
        $this->openPhoneCallId = $openPhoneCallId;
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

    public function getIntegration(): OpenPhoneIntegration
    {
        return $this->integration;
    }

    public function setIntegration(OpenPhoneIntegration $integration): self
    {
        $this->integration = $integration;
        return $this;
    }

    public function getOpenPhoneCallId(): string
    {
        return $this->openPhoneCallId;
    }

    public function setOpenPhoneCallId(string $openPhoneCallId): self
    {
        $this->openPhoneCallId = $openPhoneCallId;
        return $this;
    }

    public function getDirection(): string
    {
        return $this->direction;
    }

    public function setDirection(string $direction): self
    {
        $this->direction = $direction;
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

    public function getFromNumber(): ?string
    {
        return $this->fromNumber;
    }

    public function setFromNumber(?string $fromNumber): self
    {
        $this->fromNumber = $fromNumber;
        return $this;
    }

    public function getToNumber(): ?string
    {
        return $this->toNumber;
    }

    public function setToNumber(?string $toNumber): self
    {
        $this->toNumber = $toNumber;
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

    public function getStartedAt(): ?\DateTimeImmutable
    {
        return $this->startedAt;
    }

    public function setStartedAt(?\DateTimeImmutable $startedAt): self
    {
        $this->startedAt = $startedAt;
        return $this;
    }

    public function getEndedAt(): ?\DateTimeImmutable
    {
        return $this->endedAt;
    }

    public function setEndedAt(?\DateTimeImmutable $endedAt): self
    {
        $this->endedAt = $endedAt;
        return $this;
    }

    public function getRecordingUrl(): ?string
    {
        return $this->recordingUrl;
    }

    public function setRecordingUrl(?string $recordingUrl): self
    {
        $this->recordingUrl = $recordingUrl;
        return $this;
    }

    public function getTranscript(): ?string
    {
        return $this->transcript;
    }

    public function setTranscript(?string $transcript): self
    {
        $this->transcript = $transcript;
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

    public function isFollowUpRequired(): bool
    {
        return $this->isFollowUpRequired;
    }

    public function setIsFollowUpRequired(bool $isFollowUpRequired): self
    {
        $this->isFollowUpRequired = $isFollowUpRequired;
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
}
