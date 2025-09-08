<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
#[ORM\Table(name: 'pages')]
#[ORM\HasLifecycleCallbacks]
#[ApiResource(
    operations: [
        new Get(security: "is_granted('ROLE_USER')"),
        new GetCollection(security: "is_granted('ROLE_USER')"),
        new Post(security: "is_granted('ROLE_ANALYST')"),
        new Put(security: "is_granted('ROLE_ANALYST')")
    ],
    normalizationContext: ['groups' => ['page:read']],
    denormalizationContext: ['groups' => ['page:write']]
)]
class Page
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    #[Groups(['page:read'])]
    private string $id;

    #[ORM\ManyToOne(targetEntity: AuditRun::class, inversedBy: 'pages')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['page:read'])]
    private AuditRun $auditRun;

    #[ORM\Column(type: 'string', length: 2048)]
    #[Assert\NotBlank]
    #[Assert\Url]
    #[Groups(['page:read', 'page:write'])]
    private string $url;

    #[ORM\Column(type: 'integer')]
    #[Groups(['page:read'])]
    private int $statusCode;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['page:read'])]
    private string $contentType;

    #[ORM\Column(type: 'integer')]
    #[Groups(['page:read'])]
    private int $contentLength;

    #[ORM\Column(type: 'float')]
    #[Groups(['page:read'])]
    private float $responseTime;

    #[ORM\Column(type: 'json')]
    #[Groups(['page:read'])]
    private array $headers = [];

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['page:read'])]
    private ?string $title = null;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['page:read'])]
    private ?string $metaDescription = null;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['page:read'])]
    private ?string $canonicalUrl = null;

    #[ORM\Column(type: 'json')]
    #[Groups(['page:read'])]
    private array $robotsDirectives = [];

    #[ORM\Column(type: 'boolean')]
    #[Groups(['page:read'])]
    private bool $isIndexable = true;

    #[ORM\Column(type: 'integer', nullable: true)]
    #[Groups(['page:read'])]
    private ?int $wordCount = null;

    #[ORM\Column(type: 'string', length: 64, nullable: true)]
    #[Groups(['page:read'])]
    private ?string $bodyHash = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['page:read'])]
    private ?string $htmlPath = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['page:read'])]
    private ?string $screenshotPath = null;

    #[ORM\Column(name: 'crawled_at', type: 'datetime_immutable')]
    #[Groups(['page:read'])]
    private \DateTimeImmutable $crawledAt;

    #[ORM\Column(name: 'created_at', type: 'datetime_immutable')]
    #[Groups(['page:read'])]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(name: 'updated_at', type: 'datetime_immutable')]
    #[Groups(['page:read'])]
    private \DateTimeImmutable $updatedAt;

    public function __construct()
    {
        $this->id = Uuid::v4()->toRfc4122();
        $this->crawledAt = new \DateTimeImmutable();
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

    public function getAuditRun(): AuditRun
    {
        return $this->auditRun;
    }

    public function setAuditRun(AuditRun $auditRun): self
    {
        $this->auditRun = $auditRun;
        return $this;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;
        return $this;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function setStatusCode(int $statusCode): self
    {
        $this->statusCode = $statusCode;
        return $this;
    }

    public function getContentType(): string
    {
        return $this->contentType;
    }

    public function setContentType(string $contentType): self
    {
        $this->contentType = $contentType;
        return $this;
    }

    public function getContentLength(): int
    {
        return $this->contentLength;
    }

    public function setContentLength(int $contentLength): self
    {
        $this->contentLength = $contentLength;
        return $this;
    }

    public function getResponseTime(): float
    {
        return $this->responseTime;
    }

    public function setResponseTime(float $responseTime): self
    {
        $this->responseTime = $responseTime;
        return $this;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function setHeaders(array $headers): self
    {
        $this->headers = $headers;
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

    public function getRobotsDirectives(): array
    {
        return $this->robotsDirectives;
    }

    public function setRobotsDirectives(array $robotsDirectives): self
    {
        $this->robotsDirectives = $robotsDirectives;
        return $this;
    }

    public function isIndexable(): bool
    {
        return $this->isIndexable;
    }

    public function setIsIndexable(bool $isIndexable): self
    {
        $this->isIndexable = $isIndexable;
        return $this;
    }

    public function getWordCount(): ?int
    {
        return $this->wordCount;
    }

    public function setWordCount(?int $wordCount): self
    {
        $this->wordCount = $wordCount;
        return $this;
    }

    public function getBodyHash(): ?string
    {
        return $this->bodyHash;
    }

    public function setBodyHash(?string $bodyHash): self
    {
        $this->bodyHash = $bodyHash;
        return $this;
    }

    public function getHtmlPath(): ?string
    {
        return $this->htmlPath;
    }

    public function setHtmlPath(?string $htmlPath): self
    {
        $this->htmlPath = $htmlPath;
        return $this;
    }

    public function getScreenshotPath(): ?string
    {
        return $this->screenshotPath;
    }

    public function setScreenshotPath(?string $screenshotPath): self
    {
        $this->screenshotPath = $screenshotPath;
        return $this;
    }

    public function getCrawledAt(): \DateTimeImmutable
    {
        return $this->crawledAt;
    }

    public function setCrawledAt(\DateTimeImmutable $crawledAt): self
    {
        $this->crawledAt = $crawledAt;
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

    public function isSuccessful(): bool
    {
        return $this->statusCode >= 200 && $this->statusCode < 300;
    }

    public function isRedirect(): bool
    {
        return $this->statusCode >= 300 && $this->statusCode < 400;
    }

    public function isClientError(): bool
    {
        return $this->statusCode >= 400 && $this->statusCode < 500;
    }

    public function isServerError(): bool
    {
        return $this->statusCode >= 500;
    }

    public function isHtml(): bool
    {
        return str_contains($this->contentType, 'text/html');
    }
}
