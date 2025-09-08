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
#[ORM\Table(name: 'findings')]
#[ORM\HasLifecycleCallbacks]
#[ApiResource(
    operations: [
        new Get(security: "is_granted('ROLE_USER')"),
        new GetCollection(security: "is_granted('ROLE_USER')"),
        new Post(security: "is_granted('ROLE_ANALYST')"),
        new Put(security: "is_granted('ROLE_ANALYST')")
    ],
    normalizationContext: ['groups' => ['finding:read']],
    denormalizationContext: ['groups' => ['finding:write']]
)]
class Finding
{
    public const SEVERITY_LOW = 'low';
    public const SEVERITY_MEDIUM = 'medium';
    public const SEVERITY_HIGH = 'high';
    public const SEVERITY_CRITICAL = 'critical';

    public const CATEGORY_TECHNICAL = 'technical';
    public const CATEGORY_ONPAGE = 'onpage';
    public const CATEGORY_LOCAL = 'local';
    public const CATEGORY_PERFORMANCE = 'performance';
    public const CATEGORY_ACCESSIBILITY = 'accessibility';

    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    #[Groups(['finding:read'])]
    private string $id;

    #[ORM\ManyToOne(targetEntity: AuditRun::class, inversedBy: 'findings')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['finding:read'])]
    private AuditRun $auditRun;

    #[ORM\ManyToOne(targetEntity: Page::class)]
    #[ORM\JoinColumn(nullable: true)]
    #[Groups(['finding:read'])]
    private ?Page $page = null;

    #[ORM\Column(type: 'string', length: 50)]
    #[Assert\Choice([self::CATEGORY_TECHNICAL, self::CATEGORY_ONPAGE, self::CATEGORY_LOCAL, self::CATEGORY_PERFORMANCE, self::CATEGORY_ACCESSIBILITY])]
    #[Groups(['finding:read', 'finding:write'])]
    private string $category;

    #[ORM\Column(type: 'string', length: 50)]
    #[Assert\Choice([self::SEVERITY_LOW, self::SEVERITY_MEDIUM, self::SEVERITY_HIGH, self::SEVERITY_CRITICAL])]
    #[Groups(['finding:read', 'finding:write'])]
    private string $severity;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank]
    #[Groups(['finding:read', 'finding:write'])]
    private string $title;

    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank]
    #[Groups(['finding:read', 'finding:write'])]
    private string $description;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['finding:read', 'finding:write'])]
    private ?string $recommendation = null;

    #[ORM\Column(type: 'json')]
    #[Groups(['finding:read'])]
    private array $evidence = [];

    #[ORM\Column(type: 'integer')]
    #[Groups(['finding:read'])]
    private int $affectedPagesCount = 0;

    #[ORM\Column(type: 'float')]
    #[Groups(['finding:read'])]
    private float $impactScore = 0.0;

    #[ORM\Column(type: 'string', length: 20)]
    #[Assert\Choice(['small', 'medium', 'large'])]
    #[Groups(['finding:read'])]
    private string $effort = 'medium';

    #[ORM\Column(type: 'string', length: 50)]
    #[Groups(['finding:read', 'finding:write'])]
    private string $checkKey;

    #[ORM\Column(name: 'created_at', type: 'datetime_immutable')]
    #[Groups(['finding:read'])]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(name: 'updated_at', type: 'datetime_immutable')]
    #[Groups(['finding:read'])]
    private \DateTimeImmutable $updatedAt;

    public function __construct()
    {
        $this->id = Uuid::v4()->toRfc4122();
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

    public function getPage(): ?Page
    {
        return $this->page;
    }

    public function setPage(?Page $page): self
    {
        $this->page = $page;
        return $this;
    }

    public function getCategory(): string
    {
        return $this->category;
    }

    public function setCategory(string $category): self
    {
        $this->category = $category;
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

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getRecommendation(): ?string
    {
        return $this->recommendation;
    }

    public function setRecommendation(?string $recommendation): self
    {
        $this->recommendation = $recommendation;
        return $this;
    }

    public function getEvidence(): array
    {
        return $this->evidence;
    }

    public function setEvidence(array $evidence): self
    {
        $this->evidence = $evidence;
        return $this;
    }

    public function getAffectedPagesCount(): int
    {
        return $this->affectedPagesCount;
    }

    public function setAffectedPagesCount(int $affectedPagesCount): self
    {
        $this->affectedPagesCount = $affectedPagesCount;
        return $this;
    }

    public function getImpactScore(): float
    {
        return $this->impactScore;
    }

    public function setImpactScore(float $impactScore): self
    {
        $this->impactScore = $impactScore;
        return $this;
    }

    public function getEffort(): string
    {
        return $this->effort;
    }

    public function setEffort(string $effort): self
    {
        $this->effort = $effort;
        return $this;
    }

    public function getCheckKey(): string
    {
        return $this->checkKey;
    }

    public function setCheckKey(string $checkKey): self
    {
        $this->checkKey = $checkKey;
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

    public function isCritical(): bool
    {
        return $this->severity === self::SEVERITY_CRITICAL;
    }

    public function isHigh(): bool
    {
        return $this->severity === self::SEVERITY_HIGH;
    }

    public function isMedium(): bool
    {
        return $this->severity === self::SEVERITY_MEDIUM;
    }

    public function isLow(): bool
    {
        return $this->severity === self::SEVERITY_LOW;
    }
}
