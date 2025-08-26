<?php

namespace App\Entity;

use ApiPlatform\Metadata as API;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

/**
 * ==========================
 * AUDIT COMPETITOR
 * ==========================
 */
#[ORM\Entity(repositoryClass: \App\Repository\AuditCompetitorRepository::class)]
#[ORM\Table(name: 'audit_competitor')]
#[API\ApiResource(
    operations: [
        new API\GetCollection(uriTemplate: '/v1/audits/competitors'),
        new API\Post(uriTemplate: '/v1/audits/competitors'),
        new API\Get(uriTemplate: '/v1/audits/competitors/{id}'),
        new API\Patch(uriTemplate: '/v1/audits/competitors/{id}')
    ],
    security: "is_granted('ROLE_AGENCY_STAFF') or is_granted('ROLE_CLIENT_ADMIN')"
)]
class AuditCompetitor
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    private Uuid $id;

    #[ORM\ManyToOne(targetEntity: AuditIntake::class, inversedBy: 'competitors')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private AuditIntake $intake;

    #[ORM\Column(length: 255)]
    private string $name;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $websiteUrl = null;

    public function __construct() 
    { 
        $this->id = Uuid::v7(); 
    }

    // Getters and Setters
    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getIntake(): AuditIntake
    {
        return $this->intake;
    }

    public function setIntake(AuditIntake $intake): self
    {
        $this->intake = $intake;
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

    public function getWebsiteUrl(): ?string
    {
        return $this->websiteUrl;
    }

    public function setWebsiteUrl(?string $websiteUrl): self
    {
        $this->websiteUrl = $websiteUrl;
        return $this;
    }
}
