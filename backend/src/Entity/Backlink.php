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
#[ORM\Table(name: 'backlinks')]
#[ORM\HasLifecycleCallbacks]
#[ApiResource(
    operations: [
        new Get(security: "is_granted('ROLE_AGENCY_ADMIN') or is_granted('ROLE_AGENCY_STAFF') or (is_granted('ROLE_CLIENT_ADMIN') and object.getClientId() == user.getClientId())"),
        new GetCollection(security: "is_granted('ROLE_AGENCY_ADMIN') or is_granted('ROLE_AGENCY_STAFF') or is_granted('ROLE_CLIENT_ADMIN')"),
        new Post(security: "is_granted('ROLE_AGENCY_ADMIN') or is_granted('ROLE_CLIENT_ADMIN')"),
        new Put(security: "is_granted('ROLE_AGENCY_ADMIN') or (is_granted('ROLE_CLIENT_ADMIN') and object.getClientId() == user.getClientId())"),
        new Delete(security: "is_granted('ROLE_AGENCY_ADMIN')")
    ],
    normalizationContext: ['groups' => ['backlink:read']],
    denormalizationContext: ['groups' => ['backlink:write']]
)]
#[ApiFilter(SearchFilter::class, properties: [
    'sourceUrl' => 'partial',
    'targetUrl' => 'partial',
    'anchorText' => 'partial',
    'status' => 'exact',
    'clientId' => 'exact'
])]
#[ApiFilter(OrderFilter::class, properties: ['createdAt' => 'DESC', 'domainAuthority' => 'DESC', 'spamScore' => 'ASC'])]
#[ApiFilter(DateFilter::class, properties: ['createdAt', 'updatedAt', 'firstSeen'])]
#[ApiFilter(RangeFilter::class, properties: ['domainAuthority', 'spamScore'])]
class Backlink
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    #[ApiProperty(identifier: true)]
    #[Groups(['backlink:read'])]
    private string $id;

    #[ORM\Column(name: 'tenant_id', type: 'uuid', nullable: true)]
    private ?string $tenantId = null;

    #[ORM\Column(name: 'client_id', type: 'uuid')]
    #[Groups(['backlink:read', 'backlink:write'])]
    private string $clientId;

    #[ORM\Column(type: 'string', length: 500)]
    #[Assert\NotBlank]
    #[Assert\Url]
    #[Groups(['backlink:read', 'backlink:write'])]
    private string $sourceUrl;

    #[ORM\Column(type: 'string', length: 500)]
    #[Assert\NotBlank]
    #[Assert\Url]
    #[Groups(['backlink:read', 'backlink:write'])]
    private string $targetUrl;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['backlink:read', 'backlink:write'])]
    private ?string $anchorText = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['backlink:read', 'backlink:write'])]
    private ?string $sourceDomain = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['backlink:read', 'backlink:write'])]
    private ?string $targetDomain = null;

    #[ORM\Column(type: 'string', length: 50)]
    #[Assert\Choice(['active', 'lost', 'pending', 'discovered'])]
    #[Groups(['backlink:read', 'backlink:write'])]
    private string $status = 'discovered';

    #[ORM\Column(type: 'string', length: 20)]
    #[Assert\Choice(['dofollow', 'nofollow', 'sponsored', 'ugc'])]
    #[Groups(['backlink:read', 'backlink:write'])]
    private string $linkType = 'dofollow';

    #[ORM\Column(type: 'integer', nullable: true)]
    #[Groups(['backlink:read', 'backlink:write'])]
    private ?int $domainAuthority = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    #[Groups(['backlink:read', 'backlink:write'])]
    private ?int $spamScore = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    #[Groups(['backlink:read', 'backlink:write'])]
    private ?int $pageAuthority = null;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    #[Groups(['backlink:read', 'backlink:write'])]
    private ?string $country = null;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    #[Groups(['backlink:read', 'backlink:write'])]
    private ?string $language = null;

    #[ORM\Column(name: 'first_seen', type: 'datetime_immutable', nullable: true)]
    #[Groups(['backlink:read', 'backlink:write'])]
    private ?\DateTimeImmutable $firstSeen = null;

    #[ORM\Column(name: 'last_seen', type: 'datetime_immutable', nullable: true)]
    #[Groups(['backlink:read', 'backlink:write'])]
    private ?\DateTimeImmutable $lastSeen = null;

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    #[Groups(['backlink:read', 'backlink:write'])]
    private bool $isSocial = false;

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    #[Groups(['backlink:read', 'backlink:write'])]
    private bool $isNews = false;

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    #[Groups(['backlink:read', 'backlink:write'])]
    private bool $isBlog = false;

    #[ORM\Column(type: 'jsonb', nullable: true)]
    #[Groups(['backlink:read', 'backlink:write'])]
    private ?array $context = [];

    #[ORM\Column(type: 'jsonb', nullable: true)]
    #[Groups(['backlink:read', 'backlink:write'])]
    private ?array $metadata = [];

    #[ORM\Column(name: 'created_at', type: 'datetime_immutable')]
    #[Groups(['backlink:read'])]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(name: 'updated_at', type: 'datetime_immutable')]
    #[Groups(['backlink:read'])]
    private \DateTimeImmutable $updatedAt;

    public function __construct()
    {
        $this->id = Uuid::v4()->toRfc4122();
        $now = new \DateTimeImmutable();
        $this->createdAt = $now;
        $this->updatedAt = $now;
        $this->context = [];
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

    public function getSourceUrl(): string
    {
        return $this->sourceUrl;
    }

    public function setSourceUrl(string $sourceUrl): self
    {
        $this->sourceUrl = $sourceUrl;
        return $this;
    }

    public function getTargetUrl(): string
    {
        return $this->targetUrl;
    }

    public function setTargetUrl(string $targetUrl): self
    {
        $this->targetUrl = $targetUrl;
        return $this;
    }

    public function getAnchorText(): ?string
    {
        return $this->anchorText;
    }

    public function setAnchorText(?string $anchorText): self
    {
        $this->anchorText = $anchorText;
        return $this;
    }

    public function getSourceDomain(): ?string
    {
        return $this->sourceDomain;
    }

    public function setSourceDomain(?string $sourceDomain): self
    {
        $this->sourceDomain = $sourceDomain;
        return $this;
    }

    public function getTargetDomain(): ?string
    {
        return $this->targetDomain;
    }

    public function setTargetDomain(?string $targetDomain): self
    {
        $this->targetDomain = $targetDomain;
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

    public function getLinkType(): string
    {
        return $this->linkType;
    }

    public function setLinkType(string $linkType): self
    {
        $this->linkType = $linkType;
        return $this;
    }

    public function getDomainAuthority(): ?int
    {
        return $this->domainAuthority;
    }

    public function setDomainAuthority(?int $domainAuthority): self
    {
        $this->domainAuthority = $domainAuthority;
        return $this;
    }

    public function getSpamScore(): ?int
    {
        return $this->spamScore;
    }

    public function setSpamScore(?int $spamScore): self
    {
        $this->spamScore = $spamScore;
        return $this;
    }

    public function getPageAuthority(): ?int
    {
        return $this->pageAuthority;
    }

    public function setPageAuthority(?int $pageAuthority): self
    {
        $this->pageAuthority = $pageAuthority;
        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): self
    {
        $this->country = $country;
        return $this;
    }

    public function getLanguage(): ?string
    {
        return $this->language;
    }

    public function setLanguage(?string $language): self
    {
        $this->language = $language;
        return $this;
    }

    public function getFirstSeen(): ?\DateTimeImmutable
    {
        return $this->firstSeen;
    }

    public function setFirstSeen(?\DateTimeImmutable $firstSeen): self
    {
        $this->firstSeen = $firstSeen;
        return $this;
    }

    public function getLastSeen(): ?\DateTimeImmutable
    {
        return $this->lastSeen;
    }

    public function setLastSeen(?\DateTimeImmutable $lastSeen): self
    {
        $this->lastSeen = $lastSeen;
        return $this;
    }

    public function getIsSocial(): bool
    {
        return $this->isSocial;
    }

    public function setIsSocial(bool $isSocial): self
    {
        $this->isSocial = $isSocial;
        return $this;
    }

    public function getIsNews(): bool
    {
        return $this->isNews;
    }

    public function setIsNews(bool $isNews): self
    {
        $this->isNews = $isNews;
        return $this;
    }

    public function getIsBlog(): bool
    {
        return $this->isBlog;
    }

    public function setIsBlog(bool $isBlog): self
    {
        $this->isBlog = $isBlog;
        return $this;
    }

    public function getContext(): array
    {
        return $this->context;
    }

    public function setContext(array $context): self
    {
        $this->context = $context;
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
