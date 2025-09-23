<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Patch;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\Table(name: 'notifications')]
#[ORM\HasLifecycleCallbacks]
#[ApiResource(
    operations: [
        new GetCollection(
            uriTemplate: '/notifications',
            normalizationContext: ['groups' => ['notification:read']],
            security: 'is_granted("ROLE_SYSTEM_ADMIN") or is_granted("ROLE_AGENCY_ADMIN") or is_granted("ROLE_AGENCY_STAFF") or is_granted("ROLE_CLIENT_STAFF")'
        ),
        new Get(
            uriTemplate: '/notifications/{id}',
            normalizationContext: ['groups' => ['notification:read']],
            security: 'is_granted("ROLE_SYSTEM_ADMIN") or is_granted("ROLE_AGENCY_ADMIN") or is_granted("ROLE_AGENCY_STAFF") or is_granted("ROLE_CLIENT_STAFF")'
        ),
        new Post(
            uriTemplate: '/notifications',
            denormalizationContext: ['groups' => ['notification:write']],
            security: 'is_granted("ROLE_SYSTEM_ADMIN") or is_granted("ROLE_AGENCY_ADMIN")'
        ),
        new Put(
            uriTemplate: '/notifications/{id}',
            denormalizationContext: ['groups' => ['notification:write']],
            security: 'is_granted("ROLE_SYSTEM_ADMIN") or is_granted("ROLE_AGENCY_ADMIN")'
        ),
        new Patch(
            uriTemplate: '/notifications/{id}',
            denormalizationContext: ['groups' => ['notification:write']],
            security: 'is_granted("ROLE_SYSTEM_ADMIN") or is_granted("ROLE_AGENCY_ADMIN") or is_granted("ROLE_AGENCY_STAFF") or is_granted("ROLE_CLIENT_STAFF")'
        ),
        new Delete(
            uriTemplate: '/notifications/{id}',
            security: 'is_granted("ROLE_SYSTEM_ADMIN") or is_granted("ROLE_AGENCY_ADMIN")'
        )
    ]
)]
class Notification
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['notification:read'])]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    #[Groups(['notification:read', 'notification:write'])]
    private string $title;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['notification:read', 'notification:write'])]
    private ?string $message = null;

    #[ORM\Column(type: 'string', length: 50)]
    #[Assert\NotBlank]
    #[Assert\Choice(choices: ['info', 'success', 'warning', 'error'])]
    #[Groups(['notification:read', 'notification:write'])]
    private string $type = 'info';

    #[ORM\Column(type: 'boolean')]
    #[Groups(['notification:read', 'notification:write'])]
    private bool $isRead = false;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    #[Groups(['notification:read', 'notification:write'])]
    private ?string $actionUrl = null;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    #[Groups(['notification:read', 'notification:write'])]
    private ?string $actionLabel = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['notification:read', 'notification:write'])]
    private User $user;

    #[ORM\Column(type: 'datetime_immutable')]
    #[Groups(['notification:read'])]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    #[Groups(['notification:read'])]
    private ?\DateTimeImmutable $readAt = null;

    #[ORM\Column(type: 'json', nullable: true)]
    #[Groups(['notification:read', 'notification:write'])]
    private ?array $metadata = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    #[ORM\PrePersist]
    public function setCreatedAtValue(): void
    {
        if ($this->createdAt === null) {
            $this->createdAt = new \DateTimeImmutable();
        }
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(?string $message): self
    {
        $this->message = $message;
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

    public function isRead(): bool
    {
        return $this->isRead;
    }

    public function setIsRead(bool $isRead): self
    {
        $this->isRead = $isRead;
        if ($isRead && $this->readAt === null) {
            $this->readAt = new \DateTimeImmutable();
        } elseif (!$isRead) {
            $this->readAt = null;
        }
        return $this;
    }

    public function getActionUrl(): ?string
    {
        return $this->actionUrl;
    }

    public function setActionUrl(?string $actionUrl): self
    {
        $this->actionUrl = $actionUrl;
        return $this;
    }

    public function getActionLabel(): ?string
    {
        return $this->actionLabel;
    }

    public function setActionLabel(?string $actionLabel): self
    {
        $this->actionLabel = $actionLabel;
        return $this;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getReadAt(): ?\DateTimeImmutable
    {
        return $this->readAt;
    }

    public function setReadAt(?\DateTimeImmutable $readAt): self
    {
        $this->readAt = $readAt;
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
}
