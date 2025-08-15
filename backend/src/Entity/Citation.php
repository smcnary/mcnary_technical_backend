<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\DateFilter;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\Table(name: 'citations')]
#[ORM\HasLifecycleCallbacks]
#[ApiResource(
    operations: [
        new Get(security: "is_granted('ROLE_AGENCY_ADMIN') or is_granted('ROLE_AGENCY_STAFF') or (is_granted('ROLE_CLIENT_ADMIN') and object.getClientId() == user.getClientId())"),
        new GetCollection(security: "is_granted('ROLE_AGENCY_ADMIN') or is_granted('ROLE_AGENCY_STAFF') or is_granted('ROLE_CLIENT_ADMIN')"),
        new Post(security: "is_granted('ROLE_AGENCY_ADMIN') or is_granted('ROLE_CLIENT_ADMIN')"),
        new Put(security: "is_granted('ROLE_AGENCY_ADMIN') or (is_granted('ROLE_CLIENT_ADMIN') and object.getClientId() == user.getClientId())"),
        new Delete(security: "is_granted('ROLE_AGENCY_ADMIN')")
    ],
    normalizationContext: ['groups' => ['citation:read']],
    denormalizationContext: ['groups' => ['citation:write']]
)]
#[ApiFilter(SearchFilter::class, properties: [
    'clientId' => 'exact',
    'platform' => 'exact',
    'status' => 'exact',
    'businessName' => 'partial',
    'url' => 'partial'
])]
#[ApiFilter(OrderFilter::class, properties: ['createdAt' => 'DESC', 'businessName' => 'ASC'])]
#[ApiFilter(DateFilter::class, properties: ['createdAt', 'updatedAt'])]
class Citation
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    #[ApiProperty(identifier: true)]
    #[Groups(['citation:read'])]
    private string $id;

    #[ORM\Column(name: 'tenant_id', type: 'uuid', nullable: true)]
    private ?string $tenantId = null;

    #[ORM\Column(name: 'client_id', type: 'uuid')]
    #[Groups(['citation:read', 'citation:write'])]
    private string $clientId;

    #[ORM\Column(type: 'string', length: 100)]
    #[Assert\Choice(['google', 'facebook', 'yelp', 'yellowpages', 'whitepages', 'superpages', 'foursquare', 'other'])]
    #[Groups(['citation:read', 'citation:write'])]
    private string $platform;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank]
    #[Groups(['citation:read', 'citation:write'])]
    private string $businessName;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['citation:read', 'citation:write'])]
    private ?string $url = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['citation:read', 'citation:write'])]
    private ?string $phone = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['citation:read', 'citation:write'])]
    private ?string $address = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['citation:read', 'citation:write'])]
    private ?string $city = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['citation:read', 'citation:write'])]
    private ?string $state = null;

    #[ORM\Column(type: 'string', length: 20, nullable: true)]
    #[Groups(['citation:read', 'citation:write'])]
    private ?string $zipCode = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['citation:read', 'citation:write'])]
    private ?string $website = null;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['citation:read', 'citation:write'])]
    private ?string $description = null;

    #[ORM\Column(type: 'string', length: 50)]
    #[Assert\Choice(['pending', 'claimed', 'verified', 'unclaimed', 'duplicate', 'removed'])]
    #[Groups(['citation:read', 'citation:write'])]
    private string $status = 'pending';

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    #[Groups(['citation:read', 'citation:write'])]
    private ?string $claimStatus = null; // claimed, unclaimed, pending

    #[ORM\Column(name: 'claim_date', type: 'datetime_immutable', nullable: true)]
    #[Groups(['citation:read', 'citation:write'])]
    private ?\DateTimeImmutable $claimDate = null;

    #[ORM\Column(type: 'jsonb', nullable: true)]
    #[Groups(['citation:read', 'citation:write'])]
    private ?array $businessHours = [];

    #[ORM\Column(type: 'jsonb', nullable: true)]
    #[Groups(['citation:read', 'citation:write'])]
    private ?array $categories = [];

    #[ORM\Column(type: 'jsonb', nullable: true)]
    #[Groups(['citation:read', 'citation:write'])]
    private ?array $metadata = [];

    #[ORM\Column(name: 'created_at', type: 'datetime_immutable')]
    #[Groups(['citation:read'])]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(name: 'updated_at', type: 'datetime_immutable')]
    #[Groups(['citation:read'])]
    private \DateTimeImmutable $updatedAt;

    public function __construct()
    {
        $this->id = Uuid::v4()->toRfc4122();
        $now = new \DateTimeImmutable();
        $this->createdAt = $now;
        $this->updatedAt = $now;
        $this->businessHours = [];
        $this->categories = [];
        $this->metadata = [];
    }

    #[ORM\PreUpdate]
    public function setUpdatedAt(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }

    // Getters and Setters
    public function getId(): string
    {
        return $this->id;
    }

    public function getTenantId(): ?string
    {
        return $this->tenantId;
    }

    public function setTenantId(?string $tenantId): self
    {
        $this->tenantId = $tenantId;
        return $this;
    }

    public function getClientId(): string
    {
        return $this->clientId;
    }

    public function setClientId(string $clientId): self
    {
        $this->clientId = $clientId;
        return $this;
    }

    public function getPlatform(): string
    {
        return $this->platform;
    }

    public function setPlatform(string $platform): self
    {
        $this->platform = $platform;
        return $this;
    }

    public function getBusinessName(): string
    {
        return $this->businessName;
    }

    public function setBusinessName(string $businessName): self
    {
        $this->businessName = $businessName;
        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): self
    {
        $this->url = $url;
        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;
        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): self
    {
        $this->address = $address;
        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): self
    {
        $this->city = $city;
        return $this;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(?string $state): self
    {
        $this->state = $state;
        return $this;
    }

    public function getZipCode(): ?string
    {
        return $this->zipCode;
    }

    public function setZipCode(?string $zipCode): self
    {
        $this->zipCode = $zipCode;
        return $this;
    }

    public function getWebsite(): ?string
    {
        return $this->website;
    }

    public function setWebsite(?string $website): self
    {
        $this->website = $website;
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

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function getClaimStatus(): ?string
    {
        return $this->claimStatus;
    }

    public function setClaimStatus(?string $claimStatus): self
    {
        $this->claimStatus = $claimStatus;
        return $this;
    }

    public function getClaimDate(): ?\DateTimeImmutable
    {
        return $this->claimDate;
    }

    public function setClaimDate(?\DateTimeImmutable $claimDate): self
    {
        $this->claimDate = $claimDate;
        return $this;
    }

    public function getBusinessHours(): array
    {
        return $this->businessHours;
    }

    public function setBusinessHours(array $businessHours): self
    {
        $this->businessHours = $businessHours;
        return $this;
    }

    public function getCategories(): array
    {
        return $this->categories;
    }

    public function setCategories(array $categories): self
    {
        $this->categories = $categories;
        return $this;
    }

    public function getMetadata(): array
    {
        return $this->metadata;
    }

    public function setMetadata(array $metadata): self
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
