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

#[ORM\Entity]
#[ORM\Table(name: 'audit_run')]
#[ORM\HasLifecycleCallbacks]
#[ApiResource(
    operations: [
        new Get(security: "is_granted('ROLE_USER')"),
        new GetCollection(security: "is_granted('ROLE_USER')"),
        new Post(security: "is_granted('ROLE_ANALYST')"),
        new Put(security: "is_granted('ROLE_ANALYST')")
    ],
    normalizationContext: ['groups' => ['audit_run:read']],
    denormalizationContext: ['groups' => ['audit_run:write']]
)]
class AuditRun
{
    public const STATE_DRAFT = 'DRAFT';
    public const STATE_QUEUED = 'QUEUED';
    public const STATE_RUNNING = 'RUNNING';
    public const STATE_FAILED = 'FAILED';
    public const STATE_CANCELED = 'CANCELED';
    public const STATE_COMPLETED = 'COMPLETED';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'uuid')]
    #[Groups(['audit_run:read'])]
    private string $id;

    #[ORM\ManyToOne(targetEntity: Tenant::class, inversedBy: 'auditRuns')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['audit_run:read'])]
    private Tenant $tenant;

    #[ORM\ManyToOne(targetEntity: Audit::class, inversedBy: 'runs')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['audit_run:read', 'audit_run:write'])]
    private Audit $audit;

    #[ORM\Column(type: 'string', enumType: AuditRunState::class)]
    #[Groups(['audit_run:read'])]
    private AuditRunState $state;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    #[Groups(['audit_run:read'])]
    private ?\DateTimeImmutable $startedAt = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    #[Groups(['audit_run:read'])]
    private ?\DateTimeImmutable $finishedAt = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['audit_run:read'])]
    private User $requestedBy;

    #[ORM\Column(type: 'json')]
    #[Groups(['audit_run:read', 'audit_run:write'])]
    private array $seedUrls = [];

    #[ORM\Column(type: 'json')]
    #[Groups(['audit_run:read', 'audit_run:write'])]
    private array $config = [];

    #[ORM\Column(type: 'json')]
    #[Groups(['audit_run:read'])]
    private array $totals = [];

    #[ORM\Column(type: 'string', length: 20)]
    #[Groups(['audit_run:read'])]
    private string $versionSemver = '1.0.0';

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['audit_run:read'])]
    private ?string $error = null;

    #[ORM\OneToMany(mappedBy: 'auditRun', targetEntity: Page::class)]
    private Collection $pages;

    #[ORM\OneToMany(mappedBy: 'auditRun', targetEntity: LighthouseRun::class)]
    private Collection $lighthouseRuns;

    #[ORM\OneToMany(mappedBy: 'auditRun', targetEntity: Finding::class)]
    private Collection $findings;

    #[ORM\OneToMany(mappedBy: 'auditRun', targetEntity: Metric::class)]
    private Collection $metrics;

    #[ORM\OneToMany(mappedBy: 'auditRun', targetEntity: Report::class)]
    private Collection $reports;

    public function __construct()
    {
        $this->state = AuditRunState::DRAFT;
        $this->createdAt = new \DateTimeImmutable();
        $this->pages = new ArrayCollection();
        $this->lighthouseRuns = new ArrayCollection();
        $this->findings = new ArrayCollection();
        $this->metrics = new ArrayCollection();
        $this->reports = new ArrayCollection();
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

    public function getAudit(): Audit
    {
        return $this->audit;
    }

    public function setAudit(Audit $audit): self
    {
        $this->audit = $audit;
        return $this;
    }

    public function getState(): AuditRunState
    {
        return $this->state;
    }

    public function setState(AuditRunState $state): self
    {
        $this->state = $state;
        return $this;
    }

    public function getStartedAt(): ?\DateTimeImmutable
    {
        return $this->startedAt;
    }

    public function setStartedAt(?\DateTimeImmutable $startedAt): self
    {
        $this->startedAt = $startedAt;
        return $this;
    }

    public function getFinishedAt(): ?\DateTimeImmutable
    {
        return $this->finishedAt;
    }

    public function setFinishedAt(?\DateTimeImmutable $finishedAt): self
    {
        $this->finishedAt = $finishedAt;
        return $this;
    }

    public function getRequestedBy(): User
    {
        return $this->requestedBy;
    }

    public function setRequestedBy(User $requestedBy): self
    {
        $this->requestedBy = $requestedBy;
        return $this;
    }

    public function getSeedUrls(): array
    {
        return $this->seedUrls;
    }

    public function setSeedUrls(array $seedUrls): self
    {
        $this->seedUrls = $seedUrls;
        return $this;
    }

    public function getConfig(): array
    {
        return $this->config;
    }

    public function setConfig(array $config): self
    {
        $this->config = $config;
        return $this;
    }

    public function getTotals(): array
    {
        return $this->totals;
    }

    public function setTotals(array $totals): self
    {
        $this->totals = $totals;
        return $this;
    }

    public function getVersionSemver(): string
    {
        return $this->versionSemver;
    }

    public function setVersionSemver(string $versionSemver): self
    {
        $this->versionSemver = $versionSemver;
        return $this;
    }

    public function getError(): ?string
    {
        return $this->error;
    }

    public function setError(?string $error): self
    {
        $this->error = $error;
        return $this;
    }

    public function getPages(): Collection
    {
        return $this->pages;
    }

    public function getLighthouseRuns(): Collection
    {
        return $this->lighthouseRuns;
    }

    public function getFindings(): Collection
    {
        return $this->findings;
    }

    public function getMetrics(): Collection
    {
        return $this->metrics;
    }

    public function getReports(): Collection
    {
        return $this->reports;
    }

    public function isRunning(): bool
    {
        return $this->state === AuditRunState::RUNNING;
    }

    public function isCompleted(): bool
    {
        return $this->state === AuditRunState::COMPLETED;
    }

    public function isFailed(): bool
    {
        return $this->state === AuditRunState::FAILED;
    }

    public function isCanceled(): bool
    {
        return $this->state === AuditRunState::CANCELED;
    }
}
