<?php

namespace App\Entity;

use ApiPlatform\Metadata as API;
use App\Repository\AuditIntakeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AuditIntakeRepository::class)]
#[ORM\Table(name: 'audit_intake')]
#[API\ApiResource(
    operations: [
        new API\GetCollection(uriTemplate: '/v1/audits/intakes'),
        new API\Post(uriTemplate: '/v1/audits/intakes'),
        new API\Get(uriTemplate: '/v1/audits/intakes/{id}'),
        new API\Patch(uriTemplate: '/v1/audits/intakes/{id}'),
    ],
    security: "is_granted('ROLE_AGENCY_STAFF') or is_granted('ROLE_CLIENT_ADMIN')"
)]
class AuditIntake
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    private Uuid $id;

    #[ORM\ManyToOne(targetEntity: Client::class, inversedBy: 'auditIntakes')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    #[Assert\NotNull]
    private Client $client;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'requestedAuditIntakes')]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    private ?User $requestedBy = null;

    // Contact
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $contactName = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Email]
    private ?string $contactEmail = null;

    #[ORM\Column(length: 32, nullable: true)]
    private ?string $contactPhone = null;

    // Web properties
    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private string $websiteUrl;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    /** @var string[]|null */
    private ?array $subdomains = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $stagingUrl = null;

    // Stack
    #[ORM\Column(length: 64, options: ['comment' => 'wordpress|shopify|webflow|custom|other'])]
    private string $cms = 'custom';

    #[ORM\Column(length: 64, nullable: true)]
    private ?string $cmsVersion = null;

    #[ORM\Column(length: 128, nullable: true)]
    private ?string $hostingProvider = null;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    /** e.g. {"framework":"Symfony 7","php":"8.3","db":"PostgreSQL 16"} */
    private ?array $techStack = null;

    // Access flags (no secrets stored)
    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => false])]
    private bool $hasGoogleAnalytics = false;

    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => false])]
    private bool $hasSearchConsole = false;

    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => false])]
    private bool $hasGoogleBusinessProfile = false;

    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => false])]
    private bool $hasTagManager = false;

    // Property identifiers (non‑secret)
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $gaPropertyId = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $gscProperty = null;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    /** @var string[]|null GBP location IDs */
    private ?array $gbpLocationIds = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $gtmContainerId = null;

    // Markets & services
    #[ORM\Column(type: Types::JSON, nullable: true)]
    /** @var string[]|null */
    private ?array $markets = null; // cities/zip codes

    #[ORM\Column(type: Types::JSON, nullable: true)]
    /** @var string[]|null */
    private ?array $primaryServices = null;

    // ICP / Business context
    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $targetAudience = null;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $paidChannels = null; // e.g. {"google_ads": true, "meta": false}

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $notes = null;

    #[ORM\Column(length: 24, options: ['comment' => 'draft|submitted|approved'])]
    private string $status = 'draft';

    // URLs helpful for crawling
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $robotsTxtUrl = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $sitemapXmlUrl = null;

    // Relations
    /** @var Collection<int, AuditConversionGoal> */
    #[ORM\OneToMany(mappedBy: 'intake', targetEntity: AuditConversionGoal::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $goals;

    /** @var Collection<int, AuditCompetitor> */
    #[ORM\OneToMany(mappedBy: 'intake', targetEntity: AuditCompetitor::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $competitors;

    /** @var Collection<int, AuditKeyword> */
    #[ORM\OneToMany(mappedBy: 'intake', targetEntity: AuditKeyword::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $keywords;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private \DateTimeImmutable $updatedAt;

    public function __construct()
    {
        $this->id = Uuid::v7();
        $this->goals = new ArrayCollection();
        $this->competitors = new ArrayCollection();
        $this->keywords = new ArrayCollection();
        $now = new \DateTimeImmutable('now');
        $this->createdAt = $now;
        $this->updatedAt = $now;
    }

    #[ORM\PreUpdate]
    public function touch(): void { $this->updatedAt = new \DateTimeImmutable('now'); }

    // Getters and Setters
    public function getId(): Uuid
    {
        return $this->id;
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

    public function getRequestedBy(): ?User
    {
        return $this->requestedBy;
    }

    public function setRequestedBy(?User $requestedBy): self
    {
        $this->requestedBy = $requestedBy;
        return $this;
    }

    public function getContactName(): ?string
    {
        return $this->contactName;
    }

    public function setContactName(?string $contactName): self
    {
        $this->contactName = $contactName;
        return $this;
    }

    public function getContactEmail(): ?string
    {
        return $this->contactEmail;
    }

    public function setContactEmail(?string $contactEmail): self
    {
        $this->contactEmail = $contactEmail;
        return $this;
    }

    public function getContactPhone(): ?string
    {
        return $this->contactPhone;
    }

    public function setContactPhone(?string $contactPhone): self
    {
        $this->contactPhone = $contactPhone;
        return $this;
    }

    public function getWebsiteUrl(): string
    {
        return $this->websiteUrl;
    }

    public function setWebsiteUrl(string $websiteUrl): self
    {
        $this->websiteUrl = $websiteUrl;
        return $this;
    }

    public function getSubdomains(): ?array
    {
        return $this->subdomains;
    }

    public function setSubdomains(?array $subdomains): self
    {
        $this->subdomains = $subdomains;
        return $this;
    }

    public function getStagingUrl(): ?string
    {
        return $this->stagingUrl;
    }

    public function setStagingUrl(?string $stagingUrl): self
    {
        $this->stagingUrl = $stagingUrl;
        return $this;
    }

    public function getCms(): string
    {
        return $this->cms;
    }

    public function setCms(string $cms): self
    {
        $this->cms = $cms;
        return $this;
    }

    public function getCmsVersion(): ?string
    {
        return $this->cmsVersion;
    }

    public function setCmsVersion(?string $cmsVersion): self
    {
        $this->cmsVersion = $cmsVersion;
        return $this;
    }

    public function getHostingProvider(): ?string
    {
        return $this->hostingProvider;
    }

    public function setHostingProvider(?string $hostingProvider): self
    {
        $this->hostingProvider = $hostingProvider;
        return $this;
    }

    public function getTechStack(): ?array
    {
        return $this->techStack;
    }

    public function setTechStack(?array $techStack): self
    {
        $this->techStack = $techStack;
        return $this;
    }

    public function getHasGoogleAnalytics(): bool
    {
        return $this->hasGoogleAnalytics;
    }

    public function setHasGoogleAnalytics(bool $hasGoogleAnalytics): self
    {
        $this->hasGoogleAnalytics = $hasGoogleAnalytics;
        return $this;
    }

    public function getHasSearchConsole(): bool
    {
        return $this->hasSearchConsole;
    }

    public function setHasSearchConsole(bool $hasSearchConsole): self
    {
        $this->hasSearchConsole = $hasSearchConsole;
        return $this;
    }

    public function getHasGoogleBusinessProfile(): bool
    {
        return $this->hasGoogleBusinessProfile;
    }

    public function setHasGoogleBusinessProfile(bool $hasGoogleBusinessProfile): self
    {
        $this->hasGoogleBusinessProfile = $hasGoogleBusinessProfile;
        return $this;
    }

    public function getHasTagManager(): bool
    {
        return $this->hasTagManager;
    }

    public function setHasTagManager(bool $hasTagManager): self
    {
        $this->hasTagManager = $hasTagManager;
        return $this;
    }

    public function getGaPropertyId(): ?string
    {
        return $this->gaPropertyId;
    }

    public function setGaPropertyId(?string $gaPropertyId): self
    {
        $this->gaPropertyId = $gaPropertyId;
        return $this;
    }

    public function getGscProperty(): ?string
    {
        return $this->gscProperty;
    }

    public function setGscProperty(?string $gscProperty): self
    {
        $this->gscProperty = $gscProperty;
        return $this;
    }

    public function getGbpLocationIds(): ?array
    {
        return $this->gbpLocationIds;
    }

    public function setGbpLocationIds(?array $gbpLocationIds): self
    {
        $this->gbpLocationIds = $gbpLocationIds;
        return $this;
    }

    public function getGtmContainerId(): ?string
    {
        return $this->gtmContainerId;
    }

    public function setGtmContainerId(?string $gtmContainerId): self
    {
        $this->gtmContainerId = $gtmContainerId;
        return $this;
    }

    public function getMarkets(): ?array
    {
        return $this->markets;
    }

    public function setMarkets(?array $markets): self
    {
        $this->markets = $markets;
        return $this;
    }

    public function getPrimaryServices(): ?array
    {
        return $this->primaryServices;
    }

    public function setPrimaryServices(?array $primaryServices): self
    {
        $this->primaryServices = $primaryServices;
        return $this;
    }

    public function getTargetAudience(): ?array
    {
        return $this->targetAudience;
    }

    public function setTargetAudience(?array $targetAudience): self
    {
        $this->targetAudience = $targetAudience;
        return $this;
    }

    public function getPaidChannels(): ?array
    {
        return $this->paidChannels;
    }

    public function setPaidChannels(?array $paidChannels): self
    {
        $this->paidChannels = $paidChannels;
        return $this;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(?string $notes): self
    {
        $this->notes = $notes;
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

    public function getRobotsTxtUrl(): ?string
    {
        return $this->robotsTxtUrl;
    }

    public function setRobotsTxtUrl(?string $robotsTxtUrl): self
    {
        $this->robotsTxtUrl = $robotsTxtUrl;
        return $this;
    }

    public function getSitemapXmlUrl(): ?string
    {
        return $this->sitemapXmlUrl;
    }

    public function setSitemapXmlUrl(?string $sitemapXmlUrl): self
    {
        $this->sitemapXmlUrl = $sitemapXmlUrl;
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

    // Collection methods for goals
    public function getGoals(): Collection
    {
        return $this->goals;
    }

    public function addGoal(AuditConversionGoal $goal): self
    {
        if (!$this->goals->contains($goal)) {
            $this->goals->add($goal);
            $goal->setIntake($this);
        }
        return $this;
    }

    public function removeGoal(AuditConversionGoal $goal): self
    {
        if ($this->goals->removeElement($goal)) {
            if ($goal->getIntake() === $this) {
                $goal->setIntake(null);
            }
        }
        return $this;
    }

    // Collection methods for competitors
    public function getCompetitors(): Collection
    {
        return $this->competitors;
    }

    public function addCompetitor(AuditCompetitor $competitor): self
    {
        if (!$this->competitors->contains($competitor)) {
            $this->competitors->add($competitor);
            $competitor->setIntake($this);
        }
        return $this;
    }

    public function removeCompetitor(AuditCompetitor $competitor): self
    {
        if ($this->competitors->removeElement($competitor)) {
            if ($competitor->getIntake() === $this) {
                $competitor->setIntake(null);
            }
        }
        return $this;
    }

    // Collection methods for keywords
    public function getKeywords(): Collection
    {
        return $this->keywords;
    }

    public function addKeyword(AuditKeyword $keyword): self
    {
        if (!$this->keywords->contains($keyword)) {
            $this->keywords->add($keyword);
            $keyword->setIntake($this);
        }
        return $this;
    }

    public function removeKeyword(AuditKeyword $keyword): self
    {
        if ($this->keywords->removeElement($keyword)) {
            if ($keyword->getIntake() === $this) {
                $keyword->setIntake(null);
            }
        }
        return $this;
    }
}
