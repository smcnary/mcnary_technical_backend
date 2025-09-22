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
#[ORM\Table(name: 'openphone_message_logs')]
#[ORM\HasLifecycleCallbacks]
#[ApiResource(
    operations: [
        new Get(security: "is_granted('ROLE_AGENCY_ADMIN') or is_granted('ROLE_AGENCY_STAFF') or (is_granted('ROLE_CLIENT_ADMIN') and object.getClient().getClientId() == user.getClientId())"),
        new GetCollection(security: "is_granted('ROLE_AGENCY_ADMIN') or is_granted('ROLE_AGENCY_STAFF')"),
        new Post(security: "is_granted('ROLE_AGENCY_ADMIN') or is_granted('ROLE_AGENCY_STAFF')")
    ]
)]
class OpenPhoneMessageLog
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
    private string $openPhoneMessageId;

    #[ORM\Column(length: 32)]
    #[Assert\Choice(['inbound', 'outbound'])]
    private string $direction;

    #[ORM\Column(length: 32)]
    #[Assert\Choice(['sent', 'delivered', 'failed', 'pending'])]
    private string $status;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $fromNumber = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $toNumber = null;

    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank]
    private string $content;

    #[ORM\Column(type: 'jsonb', nullable: true)]
    private ?array $attachments = [];

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $sentAt;

    #[ORM\Column(type: 'jsonb', nullable: true)]
    private ?array $metadata = [];

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $isFollowUpRequired = false;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $notes = null;

    public function __construct(Client $client, OpenPhoneIntegration $integration, string $openPhoneMessageId)
    {
        $this->id = Uuid::v4()->toRfc4122();
        $this->client = $client;
        $this->integration = $integration;
        $this->openPhoneMessageId = $openPhoneMessageId;
        $this->attachments = [];
        $this->metadata = [];
        $this->sentAt = new \DateTimeImmutable();
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

    public function getOpenPhoneMessageId(): string
    {
        return $this->openPhoneMessageId;
    }

    public function setOpenPhoneMessageId(string $openPhoneMessageId): self
    {
        $this->openPhoneMessageId = $openPhoneMessageId;
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

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;
        return $this;
    }

    public function getAttachments(): ?array
    {
        return $this->attachments;
    }

    public function setAttachments(?array $attachments): self
    {
        $this->attachments = $attachments;
        return $this;
    }

    public function getSentAt(): \DateTimeImmutable
    {
        return $this->sentAt;
    }

    public function setSentAt(\DateTimeImmutable $sentAt): self
    {
        $this->sentAt = $sentAt;
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
