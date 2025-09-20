<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Patch;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity]
#[ORM\Table(name: 'documents')]
#[ORM\HasLifecycleCallbacks]
#[ApiResource(
    operations: [
        new GetCollection(
            security: "is_granted('ROLE_AGENCY_ADMIN') or is_granted('ROLE_CLIENT_ADMIN')",
            normalizationContext: ['groups' => ['document:read']]
        ),
        new Get(
            security: "is_granted('ROLE_AGENCY_ADMIN') or (is_granted('ROLE_CLIENT_ADMIN') and object.client == user.clientId) or (is_granted('ROLE_CLIENT_USER') and object.client == user.clientId and object.status == 'ready_for_signature')",
            normalizationContext: ['groups' => ['document:read', 'document:detail']]
        ),
        new Post(
            security: "is_granted('ROLE_AGENCY_ADMIN')",
            denormalizationContext: ['groups' => ['document:write']]
        ),
        new Put(
            security: "is_granted('ROLE_AGENCY_ADMIN')",
            denormalizationContext: ['groups' => ['document:write']]
        ),
        new Patch(
            security: "is_granted('ROLE_AGENCY_ADMIN') or (is_granted('ROLE_CLIENT_ADMIN') and object.client == user.clientId)",
            denormalizationContext: ['groups' => ['document:update']]
        ),
        new Delete(
            security: "is_granted('ROLE_AGENCY_ADMIN')"
        )
    ]
)]
class Document
{
    use Timestamps;

    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    #[Groups(['document:read', 'document:detail'])]
    private string $id;

    #[ORM\ManyToOne(targetEntity: Client::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank]
    #[Groups(['document:read', 'document:detail', 'document:write'])]
    private Client $client;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank]
    #[Groups(['document:detail', 'document:write'])]
    private User $createdBy;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank]
    #[Groups(['document:read', 'document:detail', 'document:write', 'document:update'])]
    private string $title;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['document:read', 'document:detail', 'document:write', 'document:update'])]
    private ?string $description = null;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['document:read', 'document:detail', 'document:write', 'document:update'])]
    private ?string $content = null;

    #[ORM\ManyToOne(targetEntity: MediaAsset::class)]
    #[ORM\JoinColumn(nullable: true)]
    #[Groups(['document:read', 'document:detail', 'document:write', 'document:update'])]
    private ?MediaAsset $file = null;

    #[ORM\ManyToOne(targetEntity: DocumentTemplate::class)]
    #[ORM\JoinColumn(nullable: true)]
    #[Groups(['document:detail', 'document:write'])]
    private ?DocumentTemplate $template = null;

    #[ORM\Column(type: 'string', length: 50, options: ['default' => 'draft'])]
    #[Assert\Choice(['draft', 'ready_for_signature', 'signed', 'archived', 'cancelled'])]
    #[Groups(['document:read', 'document:detail', 'document:write', 'document:update'])]
    private string $status = 'draft';

    #[ORM\Column(type: 'string', length: 50)]
    #[Assert\Choice(['contract', 'agreement', 'proposal', 'invoice', 'report', 'other'])]
    #[Groups(['document:read', 'document:detail', 'document:write', 'document:update'])]
    private string $type = 'contract';

    #[ORM\Column(type: 'jsonb', nullable: true)]
    #[Groups(['document:read', 'document:detail', 'document:write', 'document:update'])]
    private ?array $metadata = [];

    #[ORM\Column(type: 'jsonb', nullable: true)]
    #[Groups(['document:read', 'document:detail', 'document:write', 'document:update'])]
    private ?array $signatureFields = [];

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    #[Groups(['document:read', 'document:detail'])]
    private ?\DateTimeImmutable $sentForSignatureAt = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    #[Groups(['document:read', 'document:detail'])]
    private ?\DateTimeImmutable $signedAt = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    #[Groups(['document:read', 'document:detail', 'document:write', 'document:update'])]
    private ?\DateTimeImmutable $expiresAt = null;

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    #[Groups(['document:read', 'document:detail', 'document:write', 'document:update'])]
    private bool $requiresSignature = true;

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    #[Groups(['document:read', 'document:detail', 'document:write', 'document:update'])]
    private bool $isTemplate = false;

    /** @var Collection<int,DocumentSignature> */
    #[ORM\OneToMany(mappedBy: 'document', targetEntity: DocumentSignature::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    #[Groups(['document:detail'])]
    private Collection $signatures;

    /** @var Collection<int,DocumentVersion> */
    #[ORM\OneToMany(mappedBy: 'document', targetEntity: DocumentVersion::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    #[Groups(['document:detail'])]
    private Collection $versions;

    public function __construct()
    {
        $this->id = Uuid::v4()->toRfc4122();
        $this->signatures = new ArrayCollection();
        $this->versions = new ArrayCollection();
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

    public function getCreatedBy(): User
    {
        return $this->createdBy;
    }

    public function setCreatedBy(User $createdBy): self
    {
        $this->createdBy = $createdBy;
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;
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

    public function getTemplate(): ?DocumentTemplate
    {
        return $this->template;
    }

    public function setTemplate(?DocumentTemplate $template): self
    {
        $this->template = $template;
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

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;
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

    public function getSignatureFields(): ?array
    {
        return $this->signatureFields;
    }

    public function setSignatureFields(?array $signatureFields): self
    {
        $this->signatureFields = $signatureFields;
        return $this;
    }

    public function getSentForSignatureAt(): ?\DateTimeImmutable
    {
        return $this->sentForSignatureAt;
    }

    public function setSentForSignatureAt(?\DateTimeImmutable $sentForSignatureAt): self
    {
        $this->sentForSignatureAt = $sentForSignatureAt;
        return $this;
    }

    public function getSignedAt(): ?\DateTimeImmutable
    {
        return $this->signedAt;
    }

    public function setSignedAt(?\DateTimeImmutable $signedAt): self
    {
        $this->signedAt = $signedAt;
        return $this;
    }

    public function getExpiresAt(): ?\DateTimeImmutable
    {
        return $this->expiresAt;
    }

    public function setExpiresAt(?\DateTimeImmutable $expiresAt): self
    {
        $this->expiresAt = $expiresAt;
        return $this;
    }

    public function isRequiresSignature(): bool
    {
        return $this->requiresSignature;
    }

    public function setRequiresSignature(bool $requiresSignature): self
    {
        $this->requiresSignature = $requiresSignature;
        return $this;
    }

    public function isTemplate(): bool
    {
        return $this->isTemplate;
    }

    public function setIsTemplate(bool $isTemplate): self
    {
        $this->isTemplate = $isTemplate;
        return $this;
    }

    /**
     * @return Collection<int,DocumentSignature>
     */
    public function getSignatures(): Collection
    {
        return $this->signatures;
    }

    public function addSignature(DocumentSignature $signature): self
    {
        if (!$this->signatures->contains($signature)) {
            $this->signatures->add($signature);
            $signature->setDocument($this);
        }
        return $this;
    }

    public function removeSignature(DocumentSignature $signature): self
    {
        if ($this->signatures->removeElement($signature)) {
            if ($signature->getDocument() === $this) {
                $signature->setDocument($this);
            }
        }
        return $this;
    }

    /**
     * @return Collection<int,DocumentVersion>
     */
    public function getVersions(): Collection
    {
        return $this->versions;
    }

    public function addVersion(DocumentVersion $version): self
    {
        if (!$this->versions->contains($version)) {
            $this->versions->add($version);
            $version->setDocument($this);
        }
        return $this;
    }

    public function removeVersion(DocumentVersion $version): self
    {
        if ($this->versions->removeElement($version)) {
            if ($version->getDocument() === $this) {
                $version->setDocument($this);
            }
        }
        return $this;
    }

    // Helper methods
    public function isReadyForSignature(): bool
    {
        return $this->status === 'ready_for_signature';
    }

    public function isSigned(): bool
    {
        return $this->status === 'signed';
    }

    public function isExpired(): bool
    {
        return $this->expiresAt && $this->expiresAt < new \DateTimeImmutable();
    }

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

    public function getSignatureCount(): int
    {
        return $this->signatures->count();
    }

    public function getLatestVersion(): ?DocumentVersion
    {
        $latestVersion = null;
        foreach ($this->versions as $version) {
            if ($latestVersion === null || $version->getCreatedAt() > $latestVersion->getCreatedAt()) {
                $latestVersion = $version;
            }
        }
        return $latestVersion;
    }
}
