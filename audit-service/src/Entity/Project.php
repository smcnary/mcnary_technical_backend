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
#[ORM\Table(name: 'projects')]
#[ORM\HasLifecycleCallbacks]
#[ApiResource(
    operations: [
        new Get(security: "is_granted('ROLE_USER')"),
        new GetCollection(security: "is_granted('ROLE_USER')"),
        new Post(security: "is_granted('ROLE_ANALYST')"),
        new Put(security: "is_granted('ROLE_ANALYST')")
    ],
    normalizationContext: ['groups' => ['project:read']],
    denormalizationContext: ['groups' => ['project:write']]
)]
class Project
{
    public const CRAWL_SCOPE_DOMAIN = 'domain';
    public const CRAWL_SCOPE_SUBPATH = 'subpath';
    public const CRAWL_SCOPE_LIST = 'list';

    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    #[Groups(['project:read'])]
    private string $id;

    #[ORM\ManyToOne(targetEntity: Tenant::class, inversedBy: 'projects')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['project:read'])]
    private Tenant $tenant;

    #[ORM\ManyToOne(targetEntity: Client::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['project:read', 'project:write'])]
    private Client $client;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank]
    #[Groups(['project:read', 'project:write'])]
    private string $name;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank]
    #[Assert\Url]
    #[Groups(['project:read', 'project:write'])]
    private string $primaryDomain;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank]
    #[Assert\Url]
    #[Groups(['project:read', 'project:write'])]
    private string $startUrl;

    #[ORM\Column(type: 'string', length: 20)]
    #[Assert\Choice([self::CRAWL_SCOPE_DOMAIN, self::CRAWL_SCOPE_SUBPATH, self::CRAWL_SCOPE_LIST])]
    #[Groups(['project:read', 'project:write'])]
    private string $crawlScope = self::CRAWL_SCOPE_DOMAIN;

    #[ORM\Column(type: 'json', nullable: true)]
    #[Groups(['project:read', 'project:write'])]
    private ?array $allowedPaths = null;

    #[ORM\Column(type: 'json', nullable: true)]
    #[Groups(['project:read', 'project:write'])]
    private ?array $blockedPaths = null;

    #[ORM\Column(type: 'integer')]
    #[Groups(['project:read', 'project:write'])]
    private int $maxPages = 200;

    #[ORM\Column(name: 'created_at', type: 'datetime_immutable')]
    #[Groups(['project:read'])]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(name: 'updated_at', type: 'datetime_immutable')]
    #[Groups(['project:read'])]
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

    public function getTenant(): Tenant
    {
        return $this->tenant;
    }

    public function setTenant(Tenant $tenant): self
    {
        $this->tenant = $tenant;
        return $this;
    }

    public function getClient(): Client
    {
        return $this->client;
    }

    public function setClient(Client $client): self
    {
        $this->client = $client;
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

    public function getPrimaryDomain(): string
    {
        return $this->primaryDomain;
    }

    public function setPrimaryDomain(string $primaryDomain): self
    {
        $this->primaryDomain = $primaryDomain;
        return $this;
    }

    public function getStartUrl(): string
    {
        return $this->startUrl;
    }

    public function setStartUrl(string $startUrl): self
    {
        $this->startUrl = $startUrl;
        return $this;
    }

    public function getCrawlScope(): string
    {
        return $this->crawlScope;
    }

    public function setCrawlScope(string $crawlScope): self
    {
        $this->crawlScope = $crawlScope;
        return $this;
    }

    public function getAllowedPaths(): ?array
    {
        return $this->allowedPaths;
    }

    public function setAllowedPaths(?array $allowedPaths): self
    {
        $this->allowedPaths = $allowedPaths;
        return $this;
    }

    public function getBlockedPaths(): ?array
    {
        return $this->blockedPaths;
    }

    public function setBlockedPaths(?array $blockedPaths): self
    {
        $this->blockedPaths = $blockedPaths;
        return $this;
    }

    public function getMaxPages(): int
    {
        return $this->maxPages;
    }

    public function setMaxPages(int $maxPages): self
    {
        $this->maxPages = $maxPages;
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
