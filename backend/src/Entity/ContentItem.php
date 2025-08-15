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
#[ORM\Table(name: 'content_items')]
#[ORM\HasLifecycleCallbacks]
#[ApiResource(
    operations: [
        new Get(security: "is_granted('ROLE_AGENCY_ADMIN') or is_granted('ROLE_AGENCY_STAFF') or (is_granted('ROLE_CLIENT_ADMIN') and object.getClientId() == user.getClientId())"),
        new GetCollection(security: "is_granted('ROLE_AGENCY_ADMIN') or is_granted('ROLE_AGENCY_STAFF') or is_granted('ROLE_CLIENT_ADMIN')"),
        new Post(security: "is_granted('ROLE_AGENCY_ADMIN') or is_granted('ROLE_CLIENT_ADMIN')"),
        new Put(security: "is_granted('ROLE_AGENCY_ADMIN') or (is_granted('ROLE_CLIENT_ADMIN') and object.getClientId() == user.getClientId())"),
        new Delete(security: "is_granted('ROLE_AGENCY_ADMIN')")
    ],
    normalizationContext: ['groups' => ['content_item:read']],
    denormalizationContext: ['groups' => ['content_item:write']]
)]
#[ApiFilter(SearchFilter::class, properties: [
    'title' => 'partial',
    'type' => 'exact',
    'status' => 'exact',
    'clientId' => 'exact',
    'authorId' => 'exact'
])]
#[ApiFilter(OrderFilter::class, properties: ['createdAt' => 'DESC', 'publishedAt' => 'DESC', 'title' => 'ASC'])]
#[ApiFilter(DateFilter::class, properties: ['createdAt', 'publishedAt', 'updatedAt'])]
class ContentItem
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    #[ApiProperty(identifier: true)]
    #[Groups(['content_item:read'])]
    private string $id;

    #[ORM\Column(name: 'tenant_id', type: 'uuid', nullable: true)]
    private ?string $tenantId = null;

    #[ORM\Column(name: 'client_id', type: 'uuid')]
    #[Groups(['content_item:read', 'content_item:write'])]
    private string $clientId;

    #[ORM\Column(name: 'author_id', type: 'uuid', nullable: true)]
    #[Groups(['content_item:read', 'content_item:write'])]
    private ?string $authorId = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank]
    #[Groups(['content_item:read', 'content_item:write'])]
    private string $title;

    #[ORM\Column(type: 'string', length: 255, unique: true)]
    #[Assert\NotBlank]
    #[Groups(['content_item:read', 'content_item:write'])]
    private string $slug;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['content_item:read', 'content_item:write'])]
    private ?string $excerpt = null;

    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank]
    #[Groups(['content_item:read', 'content_item:write'])]
    private string $content;

    #[ORM\Column(type: 'string', length: 50)]
    #[Assert\Choice(['blog', 'article', 'case_study', 'landing_page', 'service_page', 'other'])]
    #[Groups(['content_item:read', 'content_item:write'])]
    private string $type = 'blog';

    #[ORM\Column(type: 'string', length: 50)]
    #[Assert\Choice(['draft', 'review', 'published', 'archived'])]
    #[Groups(['content_item:read', 'content_item:write'])]
    private string $status = 'draft';

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['content_item:read', 'content_item:write'])]
    private ?string $metaTitle = null;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['content_item:read', 'content_item:write'])]
    private ?string $metaDescription = null;

    #[ORM\Column(type: 'jsonb', nullable: true)]
    #[Groups(['content_item:read', 'content_item:write'])]
    private ?array $metaKeywords = [];

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['content_item:read', 'content_item:write'])]
    private ?string $featuredImage = null;

    #[ORM\Column(type: 'jsonb', nullable: true)]
    #[Groups(['content_item:read', 'content_item:write'])]
    private ?array $tags = [];

    #[ORM\Column(type: 'jsonb', nullable: true)]
    #[Groups(['content_item:read', 'content_item:write'])]
    private ?array $categories = [];

    #[ORM\Column(type: 'integer', options: ['default' => 0])]
    #[Groups(['content_item:read', 'content_item:write'])]
    private int $wordCount = 0;

    #[ORM\Column(type: 'integer', options: ['default' => 0])]
    #[Groups(['content_item:read', 'content_item:write'])]
    private int $readTime = 0; // Estimated reading time in minutes

    #[ORM\Column(name: 'published_at', type: 'datetime_immutable', nullable: true)]
    #[Groups(['content_item:read', 'content_item:write'])]
    private ?\DateTimeImmutable $publishedAt = null;

    #[ORM\Column(type: 'jsonb', nullable: true)]
    #[Groups(['content_item:read', 'content_item:write'])]
    private ?array $seoSettings = [];

    #[ORM\Column(type: 'jsonb', nullable: true)]
    #[Groups(['content_item:read', 'content_item:write'])]
    private ?array $metadata = [];

    #[ORM\Column(name: 'created_at', type: 'datetime_immutable')]
    #[Groups(['content_item:read'])]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(name: 'updated_at', type: 'datetime_immutable')]
    #[Groups(['content_item:read'])]
    private \DateTimeImmutable $updatedAt;

    public function __construct()
    {
        $this->id = Uuid::v4()->toRfc4122();
        $now = new \DateTimeImmutable();
        $this->createdAt = $now;
        $this->updatedAt = $now;
        $this->metaKeywords = [];
        $this->tags = [];
        $this->categories = [];
        $this->seoSettings = [];
        $this->metadata = [];
    }

    #[ORM\PreUpdate]
    public function setUpdatedAt(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function calculateWordCount(): void
    {
        $this->wordCount = str_word_count(strip_tags($this->content));
        $this->readTime = max(1, ceil($this->wordCount / 200)); // Average reading speed: 200 words per minute
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

    public function getAuthorId(): ?string
    {
        return $this->authorId;
    }

    public function setAuthorId(?string $authorId): self
    {
        $this->authorId = $authorId;
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

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;
        return $this;
    }

    public function getExcerpt(): ?string
    {
        return $this->excerpt;
    }

    public function setExcerpt(?string $excerpt): self
    {
        $this->excerpt = $excerpt;
        return $this;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;
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

    public function getMetaTitle(): ?string
    {
        return $this->metaTitle;
    }

    public function setMetaTitle(?string $metaTitle): self
    {
        $this->metaTitle = $metaTitle;
        return $this;
    }

    public function getMetaDescription(): ?string
    {
        return $this->metaDescription;
    }

    public function setMetaDescription(?string $metaDescription): self
    {
        $this->metaDescription = $metaDescription;
        return $this;
    }

    public function getMetaKeywords(): array
    {
        return $this->metaKeywords;
    }

    public function setMetaKeywords(array $metaKeywords): self
    {
        $this->metaKeywords = $metaKeywords;
        return $this;
    }

    public function getFeaturedImage(): ?string
    {
        return $this->featuredImage;
    }

    public function setFeaturedImage(?string $featuredImage): self
    {
        $this->featuredImage = $featuredImage;
        return $this;
    }

    public function getTags(): array
    {
        return $this->tags;
    }

    public function setTags(array $tags): self
    {
        $this->tags = $tags;
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

    public function getWordCount(): int
    {
        return $this->wordCount;
    }

    public function getReadTime(): int
    {
        return $this->readTime;
    }

    public function getPublishedAt(): ?\DateTimeImmutable
    {
        return $this->publishedAt;
    }

    public function setPublishedAt(?\DateTimeImmutable $publishedAt): self
    {
        $this->publishedAt = $publishedAt;
        return $this;
    }

    public function getSeoSettings(): array
    {
        return $this->seoSettings;
    }

    public function setSeoSettings(array $seoSettings): self
    {
        $this->seoSettings = $seoSettings;
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
