<?php

namespace App\Entity;

use ApiPlatform\Metadata as API;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

/**
 * ==========================
 * AUDIT CONVERSION GOAL
 * ==========================
 */
#[ORM\Entity(repositoryClass: \App\Repository\AuditConversionGoalRepository::class)]
#[ORM\Table(name: 'audit_conversion_goal')]
#[API\ApiResource(
    operations: [
        new API\GetCollection(uriTemplate: '/v1/audits/conversion-goals'),
        new API\Post(uriTemplate: '/v1/audits/conversion-goals'),
        new API\Get(uriTemplate: '/v1/audits/conversion-goals/{id}'),
        new API\Patch(uriTemplate: '/v1/audits/conversion-goals/{id}')
    ],
    security: "is_granted('ROLE_AGENCY_STAFF') or is_granted('ROLE_CLIENT_ADMIN')"
)]
class AuditConversionGoal
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    private Uuid $id;

    #[ORM\ManyToOne(targetEntity: AuditIntake::class, inversedBy: 'goals')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private AuditIntake $intake;

    #[ORM\Column(length: 32)]
    private string $type = 'form';

    #[ORM\Column(length: 128)]
    private string $kpi;

    #[ORM\Column(type: Types::FLOAT, nullable: true)]
    private ?float $baseline = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    private ?string $valuePerConversion = null;

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

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function getKpi(): string
    {
        return $this->kpi;
    }

    public function setKpi(string $kpi): self
    {
        $this->kpi = $kpi;
        return $this;
    }

    public function getBaseline(): ?float
    {
        return $this->baseline;
    }

    public function setBaseline(?float $baseline): self
    {
        $this->baseline = $baseline;
        return $this;
    }

    public function getValuePerConversion(): ?string
    {
        return $this->valuePerConversion;
    }

    public function setValuePerConversion(?string $valuePerConversion): self
    {
        $this->valuePerConversion = $valuePerConversion;
        return $this;
    }
}
