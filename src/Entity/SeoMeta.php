<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
#[ORM\Table(name: 'seo_meta')]
#[ORM\UniqueConstraint(columns: ['tenant_id', 'entity_type', 'entity_id'])]
#[ApiResource(security: "is_granted('ROLE_USER')")]
class SeoMeta
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    private string $id;

    #[ORM\Column(name: 'tenant_id', type: 'uuid')]
    public string $tenantId;

    #[ORM\Column(name: 'entity_type', type: 'string')]
    public string $entityType; // e.g., 'page' or 'post'

    #[ORM\Column(name: 'entity_id', type: 'uuid')]
    public string $entityId;

    #[ORM\Column(type: 'string', nullable: true)]
    public ?string $title = null;

    #[ORM\Column(name: 'meta_description', type: 'text', nullable: true)]
    public ?string $metaDescription = null;

    #[ORM\Column(name: 'canonical_url', type: 'string', nullable: true)]
    public ?string $canonicalUrl = null;

    #[ORM\Column(type: 'string', nullable: true)]
    public ?string $robots = null;

    #[ORM\Column(name: 'open_graph', type: 'json', nullable: true)]
    public ?array $openGraph = null;

    #[ORM\Column(name: 'twitter_card', type: 'json', nullable: true)]
    public ?array $twitterCard = null;

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

    public function getEntityType(): string
    {
        return $this->entityType;
    }

    public function setEntityType(string $entityType): self
    {
        $this->entityType = $entityType;
        return $this;
    }

    public function getEntityId(): string
    {
        return $this->entityId;
    }

    public function setEntityId(string $entityId): self
    {
        $this->entityId = $entityId;
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

    public function getMetaDescription(): ?string
    {
        return $this->metaDescription;
    }

    public function setMetaDescription(?string $metaDescription): self
    {
        $this->metaDescription = $metaDescription;
        return $this;
    }

    public function getCanonicalUrl(): ?string
    {
        return $this->canonicalUrl;
    }

    public function setCanonicalUrl(?string $canonicalUrl): self
    {
        $this->canonicalUrl = $canonicalUrl;
        return $this;
    }

    public function getRobots(): ?string
    {
        return $this->robots;
    }

    public function setRobots(?string $robots): self
    {
        $this->robots = $robots;
        return $this;
    }

    public function getOpenGraph(): ?array
    {
        return $this->openGraph;
    }

    public function setOpenGraph(?array $openGraph): self
    {
        $this->openGraph = $openGraph;
        return $this;
    }

    public function getTwitterCard(): ?array
    {
        return $this->twitterCard;
    }

    public function setTwitterCard(?array $twitterCard): self
    {
        $this->twitterCard = $twitterCard;
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
