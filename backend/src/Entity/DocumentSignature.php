<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Delete;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity]
#[ORM\Table(name: 'document_signatures')]
#[ORM\HasLifecycleCallbacks]
#[ApiResource(
    operations: [
        new GetCollection(
            security: "is_granted('ROLE_AGENCY_ADMIN') or (is_granted('ROLE_CLIENT_ADMIN') and object.document.client == user.clientId)",
            normalizationContext: ['groups' => ['signature:read']]
        ),
        new Get(
            security: "is_granted('ROLE_AGENCY_ADMIN') or (is_granted('ROLE_CLIENT_ADMIN') and object.document.client == user.clientId) or (is_granted('ROLE_CLIENT_USER') and object.document.client == user.clientId and object.signedBy == user)",
            normalizationContext: ['groups' => ['signature:read', 'signature:detail']]
        ),
        new Post(
            security: "is_granted('ROLE_CLIENT_USER') or is_granted('ROLE_CLIENT_ADMIN')",
            denormalizationContext: ['groups' => ['signature:write']]
        ),
        new Put(
            security: "is_granted('ROLE_AGENCY_ADMIN')",
            denormalizationContext: ['groups' => ['signature:write']]
        ),
        new Delete(
            security: "is_granted('ROLE_AGENCY_ADMIN')"
        )
    ]
)]
class DocumentSignature
{
    use Timestamps;

    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    #[Groups(['signature:read', 'signature:detail'])]
    private string $id;

    #[ORM\ManyToOne(targetEntity: Document::class, inversedBy: 'signatures')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank]
    #[Groups(['signature:read', 'signature:detail', 'signature:write'])]
    private Document $document;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank]
    #[Groups(['signature:read', 'signature:detail', 'signature:write'])]
    private User $signedBy;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['signature:read', 'signature:detail', 'signature:write'])]
    private ?string $signatureImage = null;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['signature:read', 'signature:detail', 'signature:write'])]
    private ?string $signatureData = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['signature:read', 'signature:detail', 'signature:write'])]
    private ?string $ipAddress = null;

    #[ORM\Column(type: 'string', length: 500, nullable: true)]
    #[Groups(['signature:read', 'signature:detail', 'signature:write'])]
    private ?string $userAgent = null;

    #[ORM\Column(type: 'datetime_immutable')]
    #[Groups(['signature:read', 'signature:detail'])]
    private \DateTimeImmutable $signedAt;

    #[ORM\Column(type: 'jsonb', nullable: true)]
    #[Groups(['signature:read', 'signature:detail', 'signature:write'])]
    private ?array $metadata = [];

    #[ORM\Column(type: 'string', length: 50, options: ['default' => 'pending'])]
    #[Assert\Choice(['pending', 'signed', 'rejected', 'cancelled'])]
    #[Groups(['signature:read', 'signature:detail', 'signature:write'])]
    private string $status = 'pending';

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['signature:read', 'signature:detail', 'signature:write'])]
    private ?string $comments = null;

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    #[Groups(['signature:read', 'signature:detail', 'signature:write'])]
    private bool $isDigitalSignature = true;

    public function __construct()
    {
        $this->id = Uuid::v4()->toRfc4122();
        $this->signedAt = new \DateTimeImmutable();
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

    public function getSignedBy(): User
    {
        return $this->signedBy;
    }

    public function setSignedBy(User $signedBy): self
    {
        $this->signedBy = $signedBy;
        return $this;
    }

    public function getSignatureImage(): ?string
    {
        return $this->signatureImage;
    }

    public function setSignatureImage(?string $signatureImage): self
    {
        $this->signatureImage = $signatureImage;
        return $this;
    }

    public function getSignatureData(): ?string
    {
        return $this->signatureData;
    }

    public function setSignatureData(?string $signatureData): self
    {
        $this->signatureData = $signatureData;
        return $this;
    }

    public function getIpAddress(): ?string
    {
        return $this->ipAddress;
    }

    public function setIpAddress(?string $ipAddress): self
    {
        $this->ipAddress = $ipAddress;
        return $this;
    }

    public function getUserAgent(): ?string
    {
        return $this->userAgent;
    }

    public function setUserAgent(?string $userAgent): self
    {
        $this->userAgent = $userAgent;
        return $this;
    }

    public function getSignedAt(): \DateTimeImmutable
    {
        return $this->signedAt;
    }

    public function setSignedAt(\DateTimeImmutable $signedAt): self
    {
        $this->signedAt = $signedAt;
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

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function getComments(): ?string
    {
        return $this->comments;
    }

    public function setComments(?string $comments): self
    {
        $this->comments = $comments;
        return $this;
    }

    public function isDigitalSignature(): bool
    {
        return $this->isDigitalSignature;
    }

    public function setIsDigitalSignature(bool $isDigitalSignature): self
    {
        $this->isDigitalSignature = $isDigitalSignature;
        return $this;
    }

    // Helper methods
    public function isSigned(): bool
    {
        return $this->status === 'signed';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
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

    public function getSignatureType(): string
    {
        return $this->isDigitalSignature ? 'digital' : 'wet';
    }

    public function getSignatureSummary(): array
    {
        return [
            'id' => $this->id,
            'signed_by' => [
                'id' => $this->signedBy->getId(),
                'name' => $this->signedBy->getFirstName() . ' ' . $this->signedBy->getLastName(),
                'email' => $this->signedBy->getEmail(),
            ],
            'status' => $this->status,
            'signed_at' => $this->signedAt->format('c'),
            'signature_type' => $this->getSignatureType(),
            'ip_address' => $this->ipAddress,
            'has_image' => !empty($this->signatureImage),
            'has_data' => !empty($this->signatureData),
        ];
    }
}
