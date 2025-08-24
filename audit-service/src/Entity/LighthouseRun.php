<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'lighthouse_run')]
class LighthouseRun
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'lighthouseRuns')]
    #[ORM\JoinColumn(nullable: false)]
    private AuditRun $auditRun;

    #[ORM\Column(length: 2048)]
    private string $url;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $jsonKey = null;

    #[ORM\Column(type: 'decimal', precision: 5, scale: 2, nullable: true)]
    private ?float $performance = null;

    #[ORM\Column(type: 'decimal', precision: 5, scale: 2, nullable: true)]
    private ?float $accessibility = null;

    #[ORM\Column(type: 'decimal', precision: 5, scale: 2, nullable: true)]
    private ?float $bestPractices = null;

    #[ORM\Column(type: 'decimal', precision: 5, scale: 2, nullable: true)]
    private ?float $seo = null;

    #[ORM\Column(type: 'decimal', precision: 5, scale: 2, nullable: true)]
    private ?float $pwa = null;

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAuditRun(): AuditRun
    {
        return $this->auditRun;
    }

    public function setAuditRun(AuditRun $auditRun): static
    {
        $this->auditRun = $auditRun;
        return $this;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function setUrl(string $url): static
    {
        $this->url = $url;
        return $this;
    }

    public function getJsonKey(): ?string
    {
        return $this->jsonKey;
    }

    public function setJsonKey(?string $jsonKey): static
    {
        $this->jsonKey = $jsonKey;
        return $this;
    }

    public function getPerformance(): ?float
    {
        return $this->performance;
    }

    public function setPerformance(?float $performance): static
    {
        $this->performance = $performance;
        return $this;
    }

    public function getAccessibility(): ?float
    {
        return $this->accessibility;
    }

    public function setAccessibility(?float $accessibility): static
    {
        $this->accessibility = $accessibility;
        return $this;
    }

    public function getBestPractices(): ?float
    {
        return $this->bestPractices;
    }

    public function setBestPractices(?float $bestPractices): static
    {
        $this->bestPractices = $bestPractices;
        return $this;
    }

    public function getSeo(): ?float
    {
        return $this->seo;
    }

    public function setSeo(?float $seo): static
    {
        $this->seo = $seo;
        return $this;
    }

    public function getPwa(): ?float
    {
        return $this->pwa;
    }

    public function setPwa(?float $pwa): static
    {
        $this->pwa = $pwa;
        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }
}
