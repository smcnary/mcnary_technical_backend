<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Delete;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\Table(name: 'rankings_daily', uniqueConstraints: [
    new ORM\UniqueConstraint(name: 'uniq_keyword_date', columns: ['keyword_id','date'])
])]
#[ORM\HasLifecycleCallbacks]
#[ApiResource(
    operations: [
        new Get(security: "is_granted('ROLE_AGENCY_ADMIN') or is_granted('ROLE_AGENCY_STAFF') or (is_granted('ROLE_CLIENT_ADMIN') and object.getKeyword().getClient().getClientId() == user.getClientId())"),
        new GetCollection(security: "is_granted('ROLE_AGENCY_ADMIN') or is_granted('ROLE_AGENCY_STAFF')"),
        new Post(security: "is_granted('ROLE_AGENCY_ADMIN') or is_granted('ROLE_AGENCY_STAFF')"),
        new Put(security: "is_granted('ROLE_AGENCY_ADMIN') or is_granted('ROLE_AGENCY_STAFF')"),
        new Delete(security: "is_granted('ROLE_AGENCY_ADMIN')")
    ]
)]
class RankingDaily
{
    use Timestamps;

    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private string $id;

    #[ORM\ManyToOne(inversedBy: 'rankings')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Keyword $keyword;

    #[ORM\Column(type: 'date_mutable')]
    private \DateTimeInterface $date;

    #[ORM\Column(nullable: true)]
    private ?int $serpPosition = null;

    #[ORM\Column(nullable: true)]
    private ?string $url = null;

    #[ORM\Column(nullable: true)]
    private ?int $impressions = null;

    #[ORM\Column(nullable: true)]
    private ?int $clicks = null;

    #[ORM\Column(nullable: true)]
    private ?float $ctr = null;

    #[ORM\Column(nullable: true)]
    private ?float $avgPosition = null;

    #[ORM\Column(type: 'jsonb', nullable: true)]
    private ?array $serpFeatures = [];

    #[ORM\Column(type: 'jsonb', nullable: true)]
    private ?array $metadata = [];

    public function __construct(Keyword $keyword, \DateTimeInterface $date)
    {
        $this->id = Uuid::v4()->toRfc4122();
        $this->keyword = $keyword;
        $this->date = $date;
        $this->serpFeatures = [];
        $this->metadata = [];
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getKeyword(): Keyword
    {
        return $this->keyword;
    }

    public function setKeyword(?Keyword $keyword): self
    {
        $this->keyword = $keyword;
        return $this;
    }

    public function getDate(): \DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;
        return $this;
    }

    public function getSerpPosition(): ?int
    {
        return $this->serpPosition;
    }

    public function setSerpPosition(?int $serpPosition): self
    {
        $this->serpPosition = $serpPosition;
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

    public function getImpressions(): ?int
    {
        return $this->impressions;
    }

    public function setImpressions(?int $impressions): self
    {
        $this->impressions = $impressions;
        return $this;
    }

    public function getClicks(): ?int
    {
        return $this->clicks;
    }

    public function setClicks(?int $clicks): self
    {
        $this->clicks = $clicks;
        return $this;
    }

    public function getCtr(): ?float
    {
        return $this->ctr;
    }

    public function setCtr(?float $ctr): self
    {
        $this->ctr = $ctr;
        return $this;
    }

    public function getAvgPosition(): ?float
    {
        return $this->avgPosition;
    }

    public function setAvgPosition(?float $avgPosition): self
    {
        $this->avgPosition = $avgPosition;
        return $this;
    }

    public function getSerpFeatures(): ?array
    {
        return $this->serpFeatures;
    }

    public function setSerpFeatures(?array $serpFeatures): self
    {
        $this->serpFeatures = $serpFeatures;
        return $this;
    }

    public function getMetadata(): ?array
    {
        return $this->metadata;
    }

    public function setMetadata(?array $metadata): self
    {
        $this->metadata = $metadata;
        return $this;
    }

    public function isRanked(): bool
    {
        return $this->serpPosition !== null && $this->serpPosition > 0;
    }

    public function isTop10(): bool
    {
        return $this->isRanked() && $this->serpPosition <= 10;
    }

    public function isTop3(): bool
    {
        return $this->isRanked() && $this->serpPosition <= 3;
    }

    public function isFirstPage(): bool
    {
        return $this->isRanked() && $this->serpPosition <= 10;
    }

    public function getPositionChange(RankingDaily $previousRanking): ?int
    {
        if ($this->serpPosition === null || $previousRanking->getSerpPosition() === null) {
            return null;
        }
        
        return $previousRanking->getSerpPosition() - $this->serpPosition;
    }

    public function getPositionChangeLabel(RankingDaily $previousRanking): ?string
    {
        $change = $this->getPositionChange($previousRanking);
        
        if ($change === null) {
            return null;
        }
        
        if ($change > 0) {
            return "↑ +{$change}";
        } elseif ($change < 0) {
            return "↓ " . abs($change);
        } else {
            return "→ 0";
        }
    }

    public function hasSerpFeature(string $feature): bool
    {
        return in_array($feature, $this->serpFeatures ?? [], true);
    }

    public function getSerpFeatureValue(string $feature, $default = null)
    {
        return $this->serpFeatures[$feature] ?? $default;
    }

    public function setSerpFeatureValue(string $feature, $value): self
    {
        $this->serpFeatures[$feature] = $value;
        return $this;
    }

    public function getMetadataValue(string $key, $default = null)
    {
        return $this->metadata[$key] ?? $default;
    }

    public function setMetadataValue(string $key, $value): self
    {
        $this->metadata[$key] = $value;
        return $this;
    }
}
