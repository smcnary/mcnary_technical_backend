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
#[ORM\Table(name: 'reviews')]
#[ORM\HasLifecycleCallbacks]
#[ApiResource(
    operations: [
        new Get(security: "is_granted('ROLE_AGENCY_ADMIN') or is_granted('ROLE_AGENCY_STAFF') or (is_granted('ROLE_CLIENT_ADMIN') and object.getClientId() == user.getClientId())"),
        new GetCollection(security: "is_granted('ROLE_AGENCY_ADMIN') or is_granted('ROLE_AGENCY_STAFF') or is_granted('ROLE_CLIENT_ADMIN')"),
        new Post(security: "is_granted('ROLE_AGENCY_ADMIN') or is_granted('ROLE_CLIENT_ADMIN')"),
        new Put(security: "is_granted('ROLE_AGENCY_ADMIN') or (is_granted('ROLE_CLIENT_ADMIN') and object.getClientId() == user.getClientId())"),
        new Delete(security: "is_granted('ROLE_AGENCY_ADMIN')")
    ],
    normalizationContext: ['groups' => ['review:read']],
    denormalizationContext: ['groups' => ['review:write']]
)]
#[ApiFilter(SearchFilter::class, properties: [
    'clientId' => 'exact',
    'platform' => 'exact',
    'status' => 'exact',
    'reviewerName' => 'partial'
])]
#[ApiFilter(OrderFilter::class, properties: ['createdAt' => 'DESC', 'rating' => 'DESC', 'reviewDate' => 'DESC'])]
#[ApiFilter(DateFilter::class, properties: ['reviewDate', 'createdAt'])]
#[ApiFilter(RangeFilter::class, properties: ['rating'])]
class Review
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    #[ApiProperty(identifier: true)]
    #[Groups(['review:read'])]
    private string $id;

    #[ORM\Column(name: 'tenant_id', type: 'uuid', nullable: true)]
    private ?string $tenantId = null;

    #[ORM\Column(name: 'client_id', type: 'uuid')]
    #[Groups(['review:read', 'review:write'])]
    private string $clientId;

    #[ORM\Column(type: 'string', length: 100)]
    #[Assert\Choice(['google', 'facebook', 'yelp', 'avvo', 'lawyers', 'bbb', 'other'])]
    #[Groups(['review:read', 'review:write'])]
    private string $platform;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['review:read', 'review:write'])]
    private ?string $reviewId = null; // External review ID from platform

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank]
    #[Groups(['review:read', 'review:write'])]
    private string $reviewerName;

    #[ORM\Column(type: 'integer')]
    #[Assert\Range(min: 1, max: 5)]
    #[Groups(['review:read', 'review:write'])]
    private int $rating;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['review:read', 'review:write'])]
    private ?string $title = null;

    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank]
    #[Groups(['review:read', 'review:write'])]
    private string $content;

    #[ORM\Column(type: 'string', length: 50)]
    #[Assert\Choice(['pending', 'approved', 'rejected', 'flagged'])]
    #[Groups(['review:read', 'review:write'])]
    private string $status = 'pending';

    #[ORM\Column(name: 'review_date', type: 'datetime_immutable', nullable: true)]
    #[Groups(['review:read', 'review:write'])]
    private ?\DateTimeImmutable $reviewDate = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['review:read', 'review:write'])]
    private ?string $reviewerImage = null;

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    #[Groups(['review:read', 'review:write'])]
    private bool $isVerified = false;

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    #[Groups(['review:read', 'review:write'])]
    private bool $isResponse = false;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['review:read', 'review:write'])]
    private ?string $response = null;

    #[ORM\Column(name: 'response_date', type: 'datetime_immutable', nullable: true)]
    #[Groups(['review:read', 'review:write'])]
    private ?\DateTimeImmutable $responseDate = null;

    #[ORM\Column(type: 'jsonb', nullable: true)]
    #[Groups(['review:read', 'review:write'])]
    private ?array $metadata = [];

    #[ORM\Column(name: 'created_at', type: 'datetime_immutable')]
    #[Groups(['review:read'])]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(name: 'updated_at', type: 'datetime_immutable')]
    #[Groups(['review:read'])]
    private \DateTimeImmutable $updatedAt;

    public function __construct()
    {
        $this->id = Uuid::v4()->toRfc4122();
        $now = new \DateTimeImmutable();
        $this->createdAt = $now;
        $this->updatedAt = $now;
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

    public function getReviewId(): ?string
    {
        return $this->reviewId;
    }

    public function setReviewId(?string $reviewId): self
    {
        $this->reviewId = $reviewId;
        return $this;
    }

    public function getReviewerName(): string
    {
        return $this->reviewerName;
    }

    public function setReviewerName(string $reviewerName): self
    {
        $this->reviewerName = $reviewerName;
        return $this;
    }

    public function getRating(): int
    {
        return $this->rating;
    }

    public function setRating(int $rating): self
    {
        $this->rating = $rating;
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

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;
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

    public function getReviewDate(): ?\DateTimeImmutable
    {
        return $this->reviewDate;
    }

    public function setReviewDate(?\DateTimeImmutable $reviewDate): self
    {
        $this->reviewDate = $reviewDate;
        return $this;
    }

    public function getReviewerImage(): ?string
    {
        return $this->reviewerImage;
    }

    public function setReviewerImage(?string $reviewerImage): self
    {
        $this->reviewerImage = $reviewerImage;
        return $this;
    }

    public function getIsVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;
        return $this;
    }

    public function getIsResponse(): bool
    {
        return $this->isResponse;
    }

    public function setIsResponse(bool $isResponse): self
    {
        $this->isResponse = $isResponse;
        return $this;
    }

    public function getResponse(): ?string
    {
        return $this->response;
    }

    public function setResponse(?string $response): self
    {
        $this->response = $response;
        return $this;
    }

    public function getResponseDate(): ?\DateTimeImmutable
    {
        return $this->responseDate;
    }

    public function setResponseDate(?\DateTimeImmutable $responseDate): self
    {
        $this->responseDate = $responseDate;
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
