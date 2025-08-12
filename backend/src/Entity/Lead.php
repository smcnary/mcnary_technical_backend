<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use App\Repository\LeadRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: LeadRepository::class)]
#[ORM\Table(name: 'leads')]
#[ORM\HasLifecycleCallbacks]
#[ApiResource(
    operations: [
        new Post(
            normalizationContext: ['groups' => ['lead:read']],
            denormalizationContext: ['groups' => ['lead:write']],
            security: "is_granted('PUBLIC_ACCESS')",
            validationContext: ['groups' => ['Default']]
        ),
        new Get(
            normalizationContext: ['groups' => ['lead:admin:read']],
            security: "is_granted('ROLE_ADMIN')"
        ),
        new GetCollection(
            normalizationContext: ['groups' => ['lead:admin:read']],
            security: "is_granted('ROLE_ADMIN')"
        ),
    ],
    paginationItemsPerPage: 25
)]
#[ApiFilter(SearchFilter::class, properties: [
    'status' => 'exact',
    'city' => 'partial',
    'state' => 'exact',
    'firm' => 'partial',
    'email' => 'partial',
])]
#[ApiFilter(DateFilter::class, properties: ['createdAt'])]
#[ApiFilter(OrderFilter::class, properties: ['createdAt' => 'DESC'], arguments: ['orderParameterName' => 'order'])]
class Lead
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[Groups(['lead:read', 'lead:admin:read'])]
    #[ApiProperty(identifier: true)]
    private Uuid $id;

    #[ORM\ManyToOne(targetEntity: Tenant::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Tenant $tenant;

    #[ORM\Column(type: Types::STRING)]
    #[Assert\NotBlank]
    #[Groups(['lead:read','lead:write','lead:admin:read'])]
    private string $name;

    #[ORM\Column(type: Types::STRING)]
    #[Assert\NotBlank]
    #[Assert\Email]
    #[Groups(['lead:read','lead:write','lead:admin:read'])]
    private string $email;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    #[Groups(['lead:read','lead:write','lead:admin:read'])]
    private ?string $phone = null;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    #[Groups(['lead:read','lead:write','lead:admin:read'])]
    private ?string $firm = null;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    #[Assert\Url(protocols: ['http', 'https'])]
    #[Groups(['lead:read','lead:write','lead:admin:read'])]
    private ?string $website = null;

    // Doctrine maps TEXT[] as "simple_array" (comma string) by default; we need real TEXT[]
    // Use "json" at PHP level for portability, or a custom type. We'll keep PostgreSQL TEXT[] via columnDefinition.
    #[ORM\Column(type: Types::ARRAY, options: ['default' => '{}'])]
    #[Groups(['lead:read','lead:write','lead:admin:read'])]
    private array $practiceAreas = [];

    #[ORM\Column(type: Types::STRING, nullable: true)]
    #[Groups(['lead:read','lead:write','lead:admin:read'])]
    private ?string $city = null;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    #[Groups(['lead:read','lead:write','lead:admin:read'])]
    private ?string $state = null;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    #[Groups(['lead:read','lead:write','lead:admin:read'])]
    private ?string $budget = null;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    #[Groups(['lead:read','lead:write','lead:admin:read'])]
    private ?string $timeline = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['lead:read','lead:write','lead:admin:read'])]
    private ?string $notes = null;

    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => false])]
    #[Assert\NotNull]
    #[Groups(['lead:write','lead:admin:read'])] // consent isn't echoed to public read by default
    private bool $consent = false;

    #[ORM\Column(type: Types::STRING, options: ['default' => 'pending'])]
    #[Assert\Choice(['pending','contacted','qualified','disqualified'])]
    #[Groups(['lead:admin:read'])]
    private string $status = 'pending';

    #[ORM\Column(type: Types::JSON, options: ['jsonb' => true])]
    #[Groups(['lead:admin:read'])]
    private array $utm = [];

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['lead:admin:read'])]
    private ?string $userAgent = null;

    #[ORM\Column(name: 'ip_address', type: Types::STRING, nullable: true)]
    #[Groups(['lead:admin:read'])]
    private ?string $ipAddress = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    #[Groups(['lead:admin:read'])]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    #[Groups(['lead:admin:read'])]
    private \DateTimeImmutable $updatedAt;

    public function __construct(Tenant $tenant)
    {
        $this->id = Uuid::v4();
        $this->tenant = $tenant;
        $this->createdAt = new \DateTimeImmutable('now');
        $this->updatedAt = new \DateTimeImmutable('now');
        $this->utm = [];
        $this->practiceAreas = [];
    }

    #[ORM\PreUpdate]
    public function onPreUpdate(): void
    {
        $this->updatedAt = new \DateTimeImmutable('now');
    }

    // --- getters/setters ---

    public function getId(): Uuid { return $this->id; }

    public function getTenant(): Tenant { return $this->tenant; }
    public function setTenant(Tenant $tenant): self { $this->tenant = $tenant; return $this; }

    public function getName(): string { return $this->name; }
    public function setName(string $name): self { $this->name = $name; return $this; }

    public function getEmail(): string { return $this->email; }
    public function setEmail(string $email): self { $this->email = $email; return $this; }

    public function getPhone(): ?string { return $this->phone; }
    public function setPhone(?string $phone): self { $this->phone = $phone; return $this; }

    public function getFirm(): ?string { return $this->firm; }
    public function setFirm(?string $firm): self { $this->firm = $firm; return $this; }

    public function getWebsite(): ?string { return $this->website; }
    public function setWebsite(?string $website): self { $this->website = $website; return $this; }

    public function getPracticeAreas(): array { return $this->practiceAreas; }
    public function setPracticeAreas(array $practiceAreas): self { $this->practiceAreas = $practiceAreas; return $this; }

    public function getCity(): ?string { return $this->city; }
    public function setCity(?string $city): self { $this->city = $city; return $this; }

    public function getState(): ?string { return $this->state; }
    public function setState(?string $state): self { $this->state = $state; return $this; }

    public function getBudget(): ?string { return $this->budget; }
    public function setBudget(?string $budget): self { $this->budget = $budget; return $this; }

    public function getTimeline(): ?string { return $this->timeline; }
    public function setTimeline(?string $timeline): self { $this->timeline = $timeline; return $this; }

    public function getNotes(): ?string { return $this->notes; }
    public function setNotes(?string $notes): self { $this->notes = $notes; return $this; }

    public function getConsent(): bool { return $this->consent; }
    public function setConsent(bool $consent): self { $this->consent = $consent; return $this; }

    public function getStatus(): string { return $this->status; }
    public function setStatus(string $status): self { $this->status = $status; return $this; }

    public function getUtm(): array { return $this->utm; }
    public function setUtm(array $utm): self { $this->utm = $utm; return $this; }

    public function getUserAgent(): ?string { return $this->userAgent; }
    public function setUserAgent(?string $ua): self { $this->userAgent = $ua; return $this; }

    public function getIpAddress(): ?string { return $this->ipAddress; }
    public function setIpAddress(?string $ip): self { $this->ipAddress = $ip; return $this; }

    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }
    public function setCreatedAt(\DateTimeImmutable $dt): self { $this->createdAt = $dt; return $this; }

    public function getUpdatedAt(): \DateTimeImmutable { return $this->updatedAt; }
    public function setUpdatedAt(\DateTimeImmutable $dt): self { $this->updatedAt = $dt; return $this; }
}
