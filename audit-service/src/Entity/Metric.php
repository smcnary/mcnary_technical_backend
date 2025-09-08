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
#[ORM\Table(name: 'metrics')]
#[ORM\HasLifecycleCallbacks]
#[ApiResource(
    operations: [
        new Get(security: "is_granted('ROLE_USER')"),
        new GetCollection(security: "is_granted('ROLE_USER')"),
        new Post(security: "is_granted('ROLE_ANALYST')"),
        new Put(security: "is_granted('ROLE_ANALYST')")
    ],
    normalizationContext: ['groups' => ['metric:read']],
    denormalizationContext: ['groups' => ['metric:write']]
)]
class Metric
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    #[Groups(['metric:read'])]
    private string $id;

    #[ORM\ManyToOne(targetEntity: AuditRun::class, inversedBy: 'metrics')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['metric:read'])]
    private AuditRun $auditRun;

    #[ORM\Column(type: 'string', length: 100)]
    #[Assert\NotBlank]
    #[Groups(['metric:read', 'metric:write'])]
    private string $name;

    #[ORM\Column(type: 'string', length: 50)]
    #[Assert\NotBlank]
    #[Groups(['metric:read', 'metric:write'])]
    private string $category;

    #[ORM\Column(type: 'float')]
    #[Groups(['metric:read', 'metric:write'])]
    private float $value;

    #[ORM\Column(type: 'string', length: 20, nullable: true)]
    #[Groups(['metric:read', 'metric:write'])]
    private ?string $unit = null;

    #[ORM\Column(type: 'json', nullable: true)]
    #[Groups(['metric:read'])]
    private ?array $metadata = null;

    #[ORM\Column(name: 'created_at', type: 'datetime_immutable')]
    #[Groups(['metric:read'])]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(name: 'updated_at', type: 'datetime_immutable')]
    #[Groups(['metric:read'])]
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

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
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

    public function getValue(): float
    {
        return $this->value;
    }

    public function setValue(float $value): self
    {
        $this->value = $value;
        return $this;
    }

    public function getUnit(): ?string
    {
        return $this->unit;
    }

    public function setUnit(?string $unit): self
    {
        $this->unit = $unit;
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

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }
}
