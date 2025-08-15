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
use ApiPlatform\Doctrine\Orm\Filter\RangeFilter;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\Table(name: 'rankings')]
#[ORM\HasLifecycleCallbacks]
#[ApiResource(
    operations: [
        new Get(security: "is_granted('ROLE_AGENCY_ADMIN') or is_granted('ROLE_AGENCY_STAFF') or (is_granted('ROLE_CLIENT_ADMIN') and object.getClientId() == user.getClientId())"),
        new GetCollection(security: "is_granted('ROLE_AGENCY_ADMIN') or is_granted('ROLE_AGENCY_STAFF') or is_granted('ROLE_CLIENT_ADMIN')"),
        new Post(security: "is_granted('ROLE_AGENCY_ADMIN') or is_granted('ROLE_CLIENT_ADMIN')"),
        new Put(security: "is_granted('ROLE_AGENCY_ADMIN') or (is_granted('ROLE_CLIENT_ADMIN') and object.getClientId() == user.getClientId())"),
        new Delete(security: "is_granted('ROLE_AGENCY_ADMIN')")
    ],
    normalizationContext: ['groups' => ['ranking:read']],
    denormalizationContext: ['groups' => ['ranking:write']]
)]
#[ApiFilter(SearchFilter::class, properties: [
    'keywordId' => 'exact',
    'clientId' => 'exact',
    'searchEngine' => 'exact',
    'location' => 'exact'
])]
#[ApiFilter(OrderFilter::class, properties: ['date' => 'DESC', 'position' => 'ASC'])]
#[ApiFilter(DateFilter::class, properties: ['date'])]
#[ApiFilter(RangeFilter::class, properties: ['position'])]
class Ranking
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    #[ApiProperty(identifier: true)]
    #[Groups(['ranking:read'])]
    private string $id;

    #[ORM\Column(name: 'tenant_id', type: 'uuid', nullable: true)]
    private ?string $tenantId = null;

    #[ORM\Column(name: 'client_id', type: 'uuid')]
    #[Groups(['ranking:read', 'ranking:write'])]
    private string $clientId;

    #[ORM\Column(name: 'keyword_id', type: 'uuid')]
    #[Groups(['ranking:read', 'ranking:write'])]
    private string $keywordId;

    #[ORM\Column(type: 'date')]
    #[Groups(['ranking:read', 'ranking:write'])]
    private \DateTimeImmutable $date;

    #[ORM\Column(type: 'string', length: 50)]
    #[Assert\Choice(['google', 'bing', 'yahoo', 'duckduckgo'])]
    #[Groups(['ranking:read', 'ranking:write'])]
    private string $searchEngine = 'google';

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    #[Groups(['ranking:read', 'ranking:write'])]
    private ?string $location = null; // City, State, Country

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    #[Groups(['ranking:read', 'ranking:write'])]
    private ?string $device = null; // desktop, mobile, tablet

    #[ORM\Column(type: 'integer')]
    #[Assert\Range(min: 1, max: 100)]
    #[Groups(['ranking:read', 'ranking:write'])]
    private int $position;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['ranking:read', 'ranking:write'])]
    private ?string $url = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['ranking:read', 'ranking:write'])]
    private ?string $title = null;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['ranking:read', 'ranking:write'])]
    private ?string $snippet = null;

    #[ORM\Column(type: 'jsonb', nullable: true)]
    #[Groups(['ranking:read', 'ranking:write'])]
    private ?array $features = []; // featured snippets, knowledge panels, etc.

    #[ORM\Column(type: 'jsonb', nullable: true)]
    #[Groups(['ranking:read', 'ranking:write'])]
    private ?array $metadata = [];

    #[ORM\Column(name: 'created_at', type: 'datetime_immutable')]
    #[Groups(['ranking:read'])]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(name: 'updated_at', type: 'datetime_immutable')]
    #[Groups(['ranking:read'])]
    private \DateTimeImmutable $updatedAt;

    public function __construct()
    {
        $this->id = Uuid::v4()->toRfc4122();
        $now = new \DateTimeImmutable();
        $this->createdAt = $now;
        $this->updatedAt = $now;
        $this->features = [];
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

    public function getKeywordId(): string
    {
        return $this->keywordId;
    }

    public function setKeywordId(string $keywordId): self
    {
        $this->keywordId = $keywordId;
        return $this;
    }

    public function getDate(): \DateTimeImmutable
    {
        return $this->date;
    }

    public function setDate(\DateTimeImmutable $date): self
    {
        $this->date = $date;
        return $this;
    }

    public function getSearchEngine(): string
    {
        return $this->searchEngine;
    }

    public function setSearchEngine(string $searchEngine): self
    {
        $this->searchEngine = $searchEngine;
        return $this;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(?string $location): self
    {
        $this->location = $location;
        return $this;
    }

    public function getDevice(): ?string
    {
        return $this->device;
    }

    public function setDevice(?string $device): self
    {
        $this->device = $device;
        return $this;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function setPosition(int $position): self
    {
        $this->position = $position;
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

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;
        return $this;
    }

    public function getSnippet(): ?string
    {
        return $this->snippet;
    }

    public function setSnippet(?string $snippet): self
    {
        $this->snippet = $snippet;
        return $this;
    }

    public function getFeatures(): array
    {
        return $this->features;
    }

    public function setFeatures(array $features): self
    {
        $this->features = $features;
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
