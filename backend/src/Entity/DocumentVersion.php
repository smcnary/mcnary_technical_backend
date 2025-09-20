<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity]
#[ORM\Table(name: 'document_versions')]
#[ORM\HasLifecycleCallbacks]
#[ApiResource(
    operations: [
        new GetCollection(
            security: "is_granted('ROLE_AGENCY_ADMIN') or (is_granted('ROLE_CLIENT_ADMIN') and object.document.client == user.clientId)",
            normalizationContext: ['groups' => ['version:read']]
        ),
        new Get(
            security: "is_granted('ROLE_AGENCY_ADMIN') or (is_granted('ROLE_CLIENT_ADMIN') and object.document.client == user.clientId)",
            normalizationContext: ['groups' => ['version:read', 'version:detail']]
        ),
        new Post(
            security: "is_granted('ROLE_AGENCY_ADMIN')",
            denormalizationContext: ['groups' => ['version:write']]
        )
    ]
)]
class DocumentVersion
{
    use Timestamps;

    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    #[Groups(['version:read', 'version:detail'])]
    private string $id;

    #[ORM\ManyToOne(targetEntity: Document::class, inversedBy: 'versions')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank]
    #[Groups(['version:read', 'version:detail', 'version:write'])]
    private Document $document;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank]
    #[Groups(['version:read', 'version:detail', 'version:write'])]
    private User $createdBy;

    #[ORM\Column(type: 'integer')]
    #[Assert\Positive]
    #[Groups(['version:read', 'version:detail', 'version:write'])]
    private int $versionNumber;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['version:read', 'version:detail', 'version:write'])]
    private ?string $content = null;

    #[ORM\ManyToOne(targetEntity: MediaAsset::class)]
    #[ORM\JoinColumn(nullable: true)]
    #[Groups(['version:read', 'version:detail', 'version:write'])]
    private ?MediaAsset $file = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['version:read', 'version:detail', 'version:write'])]
    private ?string $title = null;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['version:read', 'version:detail', 'version:write'])]
    private ?string $description = null;

    #[ORM\Column(type: 'jsonb', nullable: true)]
    #[Groups(['version:read', 'version:detail', 'version:write'])]
    private ?array $metadata = [];

    #[ORM\Column(type: 'jsonb', nullable: true)]
    #[Groups(['version:read', 'version:detail', 'version:write'])]
    private ?array $changes = [];

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    #[Groups(['version:read', 'version:detail'])]
    private bool $isCurrent = false;

    public function __construct()
    {
        $this->id = Uuid::v4()->toRfc4122();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getDocument(): Document
    {
        return $this->document;
    }

    public function setDocument(Document $document): self
    {
        $this->document = $document;
        return $this;
    }

    public function getCreatedBy(): User
    {
        return $this->createdBy;
    }

    public function setCreatedBy(User $createdBy): self
    {
        $this->createdBy = $createdBy;
        return $this;
    }

    public function getVersionNumber(): int
    {
        return $this->versionNumber;
    }

    public function setVersionNumber(int $versionNumber): self
    {
        $this->versionNumber = $versionNumber;
        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): self
    {
        $this->content = $content;
        return $this;
    }

    public function getFile(): ?MediaAsset
    {
        return $this->file;
    }

    public function setFile(?MediaAsset $file): self
    {
        $this->file = $file;
        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;
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

    public function getMetadata(): ?array
    {
        return $this->metadata;
    }

    public function setMetadata(?array $metadata): self
    {
        $this->metadata = $metadata;
        return $this;
    }

    public function getChanges(): ?array
    {
        return $this->changes;
    }

    public function setChanges(?array $changes): self
    {
        $this->changes = $changes;
        return $this;
    }

    public function isCurrent(): bool
    {
        return $this->isCurrent;
    }

    public function setIsCurrent(bool $isCurrent): self
    {
        $this->isCurrent = $isCurrent;
        return $this;
    }

    // Helper methods
    public function getMetaValue(string $key, $default = null)
    {
        return $this->metadata[$key] ?? $default;
    }

    public function setMetaValue(string $key, $value): self
    {
        if ($this->metadata === null) {
            $this->metadata = [];
        }
        $this->metadata[$key] = $value;
        return $this;
    }

    public function getChangeSummary(): string
    {
        if (empty($this->changes)) {
            return 'Initial version';
        }

        $summary = [];
        foreach ($this->changes as $field => $change) {
            $summary[] = ucfirst($field) . ' modified';
        }

        return implode(', ', $summary);
    }

    public function getVersionLabel(): string
    {
        return "v{$this->versionNumber}";
    }

    public function getFormattedCreatedAt(): string
    {
        return $this->getCreatedAt()->format('M j, Y g:i A');
    }

    public function getCreatedByDisplayName(): string
    {
        $user = $this->createdBy;
        if ($user->getFirstName() && $user->getLastName()) {
            return $user->getFirstName() . ' ' . $user->getLastName();
        }
        return $user->getEmail();
    }
}
