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
#[ORM\Table(name: 'media_assets')]
#[ORM\HasLifecycleCallbacks]
#[ApiResource(
    operations: [
        new Get(security: "PUBLIC_ACCESS"), // Public media access
        new GetCollection(security: "is_granted('ROLE_AGENCY_ADMIN') or is_granted('ROLE_AGENCY_STAFF') or is_granted('ROLE_CLIENT_ADMIN')"),
        new Post(security: "is_granted('ROLE_AGENCY_ADMIN') or is_granted('ROLE_CLIENT_ADMIN')"),
        new Put(security: "is_granted('ROLE_AGENCY_ADMIN') or (is_granted('ROLE_CLIENT_ADMIN') and object.getClientId() == user.getClientId())"),
        new Delete(security: "is_granted('ROLE_AGENCY_ADMIN') or (is_granted('ROLE_CLIENT_ADMIN') and object.getClientId() == user.getClientId())")
    ]
)]
class MediaAsset
{
    use Timestamps;

    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private string $id;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?Client $ownerClient = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank]
    private string $filename;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank]
    private string $originalFilename;

    #[ORM\Column(type: 'string', length: 100)]
    #[Assert\NotBlank]
    private string $mimeType;

    #[ORM\Column(type: 'bigint')]
    private int $fileSize;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank]
    private string $storageKey; // S3 path or local path

    #[ORM\Column(type: 'string', length: 50)]
    #[Assert\NotBlank]
    private string $storageProvider = 's3'; // s3, local, etc.

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $title = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $altText = null;

    #[ORM\Column(type: 'string', length: 50)]
    private string $type = 'image'; // image, video, document, audio

    #[ORM\Column(type: 'jsonb', nullable: true)]
    private ?array $dimensions = []; // width, height for images/videos

    #[ORM\Column(type: 'jsonb', nullable: true)]
    private ?array $metadata = [];

    #[ORM\Column(type: 'jsonb', nullable: true)]
    private ?array $processingStatus = []; // upload, processing, ready, error

    #[ORM\Column(type: 'string', options: ['default' => 'active'])]
    private string $status = 'active'; // active, archived, deleted

    #[ORM\Column(name: 'uploaded_at', type: 'datetime_immutable')]
    private \DateTimeImmutable $uploadedAt;

    public function __construct(string $storageKey)
    {
        $this->id = Uuid::v4()->toRfc4122();
        $this->storageKey = $storageKey;
        $this->uploadedAt = new \DateTimeImmutable();
        $this->dimensions = [];
        $this->metadata = [];
        $this->processingStatus = [];
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getOwnerClient(): ?Client
    {
        return $this->ownerClient;
    }

    public function setOwnerClient(?Client $ownerClient): self
    {
        $this->ownerClient = $ownerClient;
        return $this;
    }

    public function getFilename(): string
    {
        return $this->filename;
    }

    public function setFilename(string $filename): self
    {
        $this->filename = $filename;
        return $this;
    }

    public function getOriginalFilename(): string
    {
        return $this->originalFilename;
    }

    public function setOriginalFilename(string $originalFilename): self
    {
        $this->originalFilename = $originalFilename;
        return $this;
    }

    public function getMimeType(): string
    {
        return $this->mimeType;
    }

    public function setMimeType(string $mimeType): self
    {
        $this->mimeType = $mimeType;
        return $this;
    }

    public function getFileSize(): int
    {
        return $this->fileSize;
    }

    public function setFileSize(int $fileSize): self
    {
        $this->fileSize = $fileSize;
        return $this;
    }

    public function getStorageKey(): string
    {
        return $this->storageKey;
    }

    public function setStorageKey(string $storageKey): self
    {
        $this->storageKey = $storageKey;
        return $this;
    }

    // Legacy getter for backward compatibility
    public function getStoragePath(): string
    {
        return $this->storageKey;
    }

    public function setStoragePath(string $storagePath): self
    {
        $this->storageKey = $storagePath;
        return $this;
    }

    public function getStorageProvider(): string
    {
        return $this->storageProvider;
    }

    public function setStorageProvider(string $storageProvider): self
    {
        $this->storageProvider = $storageProvider;
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

    public function getAltText(): ?string
    {
        return $this->altText;
    }

    public function setAltText(?string $altText): self
    {
        $this->altText = $altText;
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

    public function getDimensions(): ?array
    {
        return $this->dimensions;
    }

    public function setDimensions(?array $dimensions): self
    {
        $this->dimensions = $dimensions;
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

    public function getProcessingStatus(): ?array
    {
        return $this->processingStatus;
    }

    public function setProcessingStatus(?array $processingStatus): self
    {
        $this->processingStatus = $processingStatus;
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

    public function getUploadedAt(): \DateTimeImmutable
    {
        return $this->uploadedAt;
    }

    public function setUploadedAt(\DateTimeImmutable $uploadedAt): self
    {
        $this->uploadedAt = $uploadedAt;
        return $this;
    }

    // Helper methods
    public function getFileSizeFormatted(): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $size = $this->fileSize;
        $unit = 0;
        
        while ($size >= 1024 && $unit < count($units) - 1) {
            $size /= 1024;
            $unit++;
        }
        
        return round($size, 2) . ' ' . $units[$unit];
    }

    public function getWidth(): ?int
    {
        return $this->dimensions['width'] ?? null;
    }

    public function getHeight(): ?int
    {
        return $this->dimensions['height'] ?? null;
    }

    public function isImage(): bool
    {
        return $this->type === 'image';
    }

    public function isVideo(): bool
    {
        return $this->type === 'video';
    }

    public function isDocument(): bool
    {
        return $this->type === 'document';
    }

    public function isAudio(): bool
    {
        return $this->type === 'audio';
    }

    public function getStreamUrl(): ?string
    {
        if ($this->storageProvider === 's3') {
            return 'https://your-s3-bucket.s3.amazonaws.com/' . $this->storageKey;
        }
        
        return '/media/' . $this->storageKey;
    }

    public function getThumbnailUrl(): ?string
    {
        if ($this->isImage()) {
            return $this->getStreamUrl() . '?thumb=true';
        }
        
        return null;
    }

    public function getMetaValue(string $key, $default = null)
    {
        return $this->metadata[$key] ?? $default;
    }

    public function setMetaValue(string $key, $value): self
    {
        $this->metadata[$key] = $value;
        return $this;
    }

    // Legacy getters for backward compatibility
    public function getClientId(): ?string
    {
        return $this->ownerClient?->getId();
    }

    public function setClientId(?string $clientId): self
    {
        // This method is kept for backward compatibility but should not be used
        // Use setOwnerClient() instead
        return $this;
    }

    public function getTenantId(): ?string
    {
        // This method is kept for backward compatibility but should not be used
        return null;
    }

    public function setTenantId(?string $tenantId): self
    {
        // This method is kept for backward compatibility but should not be used
        return $this;
    }
}
