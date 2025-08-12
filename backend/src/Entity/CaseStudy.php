<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use App\Repository\CaseStudyRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CaseStudyRepository::class)]
#[ORM\Table(name: 'case_studies')]
#[ORM\UniqueConstraint(name: 'uq_case_studies_tenant_slug', columns: ['tenant_id', 'slug'])]
#[ORM\HasLifecycleCallbacks]
#[ApiResource(
    operations: [
        new GetCollection(normalizationContext: ['groups' => ['cs:read']]),
        new Get(normalizationContext: ['groups' => ['cs:read']]),
        new Post(denormalizationContext: ['groups' => ['cs:write']], security: "is_granted('ROLE_ADMIN')"),
        new Put(denormalizationContext: ['groups' => ['cs:write']], security: "is_granted('ROLE_ADMIN')"),
        new Patch(denormalizationContext: ['groups' => ['cs:write']], security: "is_granted('ROLE_ADMIN')"),
        new Delete(security: "is_granted('ROLE_ADMIN')"),
    ],
    paginationItemsPerPage: 20
)]
#[ApiFilter(SearchFilter::class, properties: [
    'isActive' => 'exact',
    'practiceArea' => 'partial',
    'slug' => 'exact',
    'title' => 'partial',
])]
#[ApiFilter(OrderFilter::class, properties: ['sort' => 'ASC', 'createdAt' => 'DESC'])]
class CaseStudy
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ApiProperty(identifier: true)]
    #[Groups(['cs:read'])]
    private Uuid $id;

    #[ORM\ManyToOne(targetEntity: Tenant::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Tenant $tenant;

    #[ORM\Column(type: Types::STRING)]
    #[Assert\NotBlank]
    #[Groups(['cs:read','cs:write'])]
    private string $title;

    #[ORM\Column(type: Types::STRING)]
    #[Assert\NotBlank]
    #[Groups(['cs:read','cs:write'])]
    private string $slug;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['cs:read','cs:write'])]
    private ?string $summary = null;

    #[ORM\Column(name: 'metrics_json', type: Types::JSON, options: ['jsonb' => true])]
    #[Groups(['cs:read','cs:write'])]
    private array $metricsJson = [];

    #[ORM\Column(type: Types::STRING, nullable: true)]
    #[Groups(['cs:read','cs:write'])]
    private ?string $heroImage = null;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    #[Groups(['cs:read','cs:write'])]
    private ?string $practiceArea = null;

    #[ORM\Column(name: 'is_active', type: Types::BOOLEAN, options: ['default' => true])]
    #[Groups(['cs:read','cs:write'])]
    private bool $isActive = true;

    #[ORM\Column(type: Types::INTEGER, options: ['default' => 0])]
    #[Groups(['cs:read','cs:write'])]
    private int $sort = 0;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    #[Groups(['cs:read'])]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    #[Groups(['cs:read'])]
    private \DateTimeImmutable $updatedAt;

    public function __construct(Tenant $tenant)
    {
        $this->id = Uuid::v4();
        $this->tenant = $tenant;
        $this->createdAt = new \DateTimeImmutable('now');
        $this->updatedAt = new \DateTimeImmutable('now');
    }

    #[ORM\PreUpdate]
    public function onPreUpdate(): void
    {
        $this->updatedAt = new \DateTimeImmutable('now');
    }

    // getters/setters ...

    public function getId(): Uuid { return $this->id; }

    public function getTenant(): Tenant { return $this->tenant; }
    public function setTenant(Tenant $tenant): self { $this->tenant = $tenant; return $this; }

    public function getTitle(): string { return $this->title; }
    public function setTitle(string $title): self { $this->title = $title; return $this; }

    public function getSlug(): string { return $this->slug; }
    public function setSlug(string $slug): self { $this->slug = $slug; return $this; }

    public function getSummary(): ?string { return $this->summary; }
    public function setSummary(?string $summary): self { $this->summary = $summary; return $this; }

    public function getMetricsJson(): array { return $this->metricsJson; }
    public function setMetricsJson(array $metricsJson): self { $this->metricsJson = $metricsJson; return $this; }

    public function getHeroImage(): ?string { return $this->heroImage; }
    public function setHeroImage(?string $heroImage): self { $this->heroImage = $heroImage; return $this; }

    public function getPracticeArea(): ?string { return $this->practiceArea; }
    public function setPracticeArea(?string $practiceArea): self { $this->practiceArea = $practiceArea; return $this; }

    public function isActive(): bool { return $this->isActive; }
    public function setIsActive(bool $active): self { $this->isActive = $active; return $this; }

    public function getSort(): int { return $this->sort; }
    public function setSort(int $sort): self { $this->sort = $sort; return $this; }

    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }
    public function setCreatedAt(\DateTimeImmutable $dt): self { $this->createdAt = $dt; return $this; }

    public function getUpdatedAt(): \DateTimeImmutable { return $this->updatedAt; }
    public function setUpdatedAt(\DateTimeImmutable $dt): self { $this->updatedAt = $dt; return $this; }
}
