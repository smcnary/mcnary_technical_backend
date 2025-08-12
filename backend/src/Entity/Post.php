<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
#[ORM\Table(name: 'posts')]
#[ORM\Index(columns: ['tenant_id', 'status', 'published_at'], name: 'idx_posts_tenant_status_pub')]
#[ORM\UniqueConstraint(columns: ['tenant_id', 'site_id', 'slug'])]
#[ApiResource(security: "is_granted('ROLE_USER')")]
class Post
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    private string $id;

    #[ORM\Column(name: 'tenant_id', type: 'uuid')]
    public string $tenantId;

    #[ORM\Column(name: 'site_id', type: 'uuid')]
    public string $siteId;

    #[ORM\Column(name: 'author_id', type: 'uuid', nullable: true)]
    public ?string $authorId = null;

    #[ORM\Column(type: 'string')]
    public string $title;

    #[ORM\Column(type: 'string')]
    public string $slug;

    #[ORM\Column(type: 'string', options: ['default' => 'draft'])]
    public string $status = 'draft';

    #[ORM\Column(type: 'text', nullable: true)]
    public ?string $excerpt = null;

    #[ORM\Column(name: 'published_at', type: 'datetimetz_immutable', nullable: true)]
    public ?\DateTimeImmutable $publishedAt = null;

    #[ORM\Column(name: 'created_at', type: 'datetimetz_immutable')]
    public \DateTimeImmutable $createdAt;

    #[ORM\Column(name: 'updated_at', type: 'datetimetz_immutable')]
    public \DateTimeImmutable $updatedAt;

    public function __construct()
    {
        $this->id = Uuid::v4()->toRfc4122();
        $now = new \DateTimeImmutable();
        $this->createdAt = $now;
        $this->updatedAt = $now;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getTenantId(): string
    {
        return $this->tenantId;
    }

    public function setTenantId(string $tenantId): self
    {
        $this->tenantId = $tenantId;
        return $this;
    }

    public function getSiteId(): string
    {
        return $this->siteId;
    }

    public function setSiteId(string $siteId): self
    {
        $this->siteId = $siteId;
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

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;
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

    public function getPublishedAt(): ?\DateTimeImmutable
    {
        return $this->publishedAt;
    }

    public function setPublishedAt(?\DateTimeImmutable $publishedAt): self
    {
        $this->publishedAt = $publishedAt;
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

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }
}
