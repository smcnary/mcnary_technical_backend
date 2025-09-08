<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
#[ORM\Table(name: 'reports')]
#[ORM\HasLifecycleCallbacks]
#[ApiResource(
    operations: [
        new Get(security: "is_granted('ROLE_USER')"),
        new GetCollection(security: "is_granted('ROLE_USER')"),
        new Post(security: "is_granted('ROLE_ANALYST')"),
        new Put(security: "is_granted('ROLE_ANALYST')")
    ],
    normalizationContext: ['groups' => ['report:read']],
    denormalizationContext: ['groups' => ['report:write']]
)]
class Report
{
    public const TYPE_HTML = 'html';
    public const TYPE_PDF = 'pdf';
    public const TYPE_CSV = 'csv';
    public const TYPE_JSON = 'json';

    public const STATUS_PENDING = 'pending';
    public const STATUS_GENERATING = 'generating';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_FAILED = 'failed';

    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    #[Groups(['report:read'])]
    private string $id;

    #[ORM\ManyToOne(targetEntity: AuditRun::class, inversedBy: 'reports')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['report:read'])]
    private AuditRun $auditRun;

    #[ORM\Column(type: 'string', length: 50)]
    #[Assert\Choice([self::TYPE_HTML, self::TYPE_PDF, self::TYPE_CSV, self::TYPE_JSON])]
    #[Groups(['report:read', 'report:write'])]
    private string $type;

    #[ORM\Column(type: 'string', length: 50)]
    #[Assert\Choice([self::STATUS_PENDING, self::STATUS_GENERATING, self::STATUS_COMPLETED, self::STATUS_FAILED])]
    #[Groups(['report:read'])]
    private string $status;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['report:read'])]
    private ?string $filePath = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    #[Groups(['report:read'])]
    private ?int $fileSize = null;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['report:read'])]
    private ?string $error = null;

    #[ORM\Column(name: 'generated_at', type: 'datetime_immutable', nullable: true)]
    #[Groups(['report:read'])]
    private ?\DateTimeImmutable $generatedAt = null;

    #[ORM\Column(name: 'created_at', type: 'datetime_immutable')]
    #[Groups(['report:read'])]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(name: 'updated_at', type: 'datetime_immutable')]
    #[Groups(['report:read'])]
    private \DateTimeImmutable $updatedAt;

    public function __construct()
    {
        $this->id = Uuid::v4()->toRfc4122();
        $this->status = self::STATUS_PENDING;
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    #[ORM\PreUpdate]
    public function setUpdatedAt(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getAuditRun(): AuditRun
    {
        return $this->auditRun;
    }

    public function setAuditRun(AuditRun $auditRun): self
    {
        $this->auditRun = $auditRun;
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

    public function getFilePath(): ?string
    {
        return $this->filePath;
    }

    public function setFilePath(?string $filePath): self
    {
        $this->filePath = $filePath;
        return $this;
    }

    public function getFileSize(): ?int
    {
        return $this->fileSize;
    }

    public function setFileSize(?int $fileSize): self
    {
        $this->fileSize = $fileSize;
        return $this;
    }

    public function getError(): ?string
    {
        return $this->error;
    }

    public function setError(?string $error): self
    {
        $this->error = $error;
        return $this;
    }

    public function getGeneratedAt(): ?\DateTimeImmutable
    {
        return $this->generatedAt;
    }

    public function setGeneratedAt(?\DateTimeImmutable $generatedAt): self
    {
        $this->generatedAt = $generatedAt;
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

    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    public function isFailed(): bool
    {
        return $this->status === self::STATUS_FAILED;
    }

    public function isGenerating(): bool
    {
        return $this->status === self::STATUS_GENERATING;
    }
}
