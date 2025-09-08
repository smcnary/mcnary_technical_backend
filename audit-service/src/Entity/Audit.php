<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
#[ORM\Table(name: 'audits')]
#[ORM\HasLifecycleCallbacks]
#[ApiResource(
    operations: [
        new Get(security: "is_granted('ROLE_USER')"),
        new GetCollection(security: "is_granted('ROLE_USER')"),
        new Post(security: "is_granted('ROLE_ANALYST')"),
        new Put(security: "is_granted('ROLE_ANALYST')")
    ],
    normalizationContext: ['groups' => ['audit:read']],
    denormalizationContext: ['groups' => ['audit:write']]
)]
class Audit
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    #[Groups(['audit:read'])]
    private string $id;

    #[ORM\ManyToOne(targetEntity: Tenant::class, inversedBy: 'audits')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['audit:read'])]
    private Tenant $tenant;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank]
    #[Groups(['audit:read', 'audit:write'])]
    private string $label;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['audit:read', 'audit:write'])]
    private ?string $scheduleCron = null;

    #[ORM\Column(type: 'json')]
    #[Groups(['audit:read', 'audit:write'])]
    private array $categoriesWeight = [
        'technical' => 40,
        'onpage' => 35,
        'local' => 25
    ];

    #[ORM\Column(name: 'created_at', type: 'datetime_immutable')]
    #[Groups(['audit:read'])]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(name: 'updated_at', type: 'datetime_immutable')]
    #[Groups(['audit:read'])]
    private \DateTimeImmutable $updatedAt;

    #[ORM\OneToMany(mappedBy: 'audit', targetEntity: AuditRun::class)]
    private Collection $runs;

    public function __construct()
    {
        $this->id = Uuid::v4()->toRfc4122();
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
        $this->runs = new ArrayCollection();
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

    public function getTenant(): Tenant
    {
        return $this->tenant;
    }

    public function setTenant(Tenant $tenant): self
    {
        $this->tenant = $tenant;
        return $this;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;
        return $this;
    }

    public function getScheduleCron(): ?string
    {
        return $this->scheduleCron;
    }

    public function setScheduleCron(?string $scheduleCron): self
    {
        $this->scheduleCron = $scheduleCron;
        return $this;
    }

    public function getCategoriesWeight(): array
    {
        return $this->categoriesWeight;
    }

    public function setCategoriesWeight(array $categoriesWeight): self
    {
        $this->categoriesWeight = $categoriesWeight;
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

    public function getRuns(): Collection
    {
        return $this->runs;
    }
}
