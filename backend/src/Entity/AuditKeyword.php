<?php

namespace App\Entity;

use ApiPlatform\Metadata as API;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

/**
 * ==========================
 * AUDIT KEYWORD
 * ==========================
 */
#[ORM\Entity(repositoryClass: \App\Repository\AuditKeywordRepository::class)]
#[ORM\Table(name: 'audit_keyword')]
#[API\ApiResource(
    operations: [
        new API\GetCollection(uriTemplate: '/v1/audits/keywords'),
        new API\Post(uriTemplate: '/v1/audits/keywords'),
        new API\Get(uriTemplate: '/v1/audits/keywords/{id}'),
        new API\Patch(uriTemplate: '/v1/audits/keywords/{id}')
    ],
    security: "is_granted('ROLE_AGENCY_STAFF') or is_granted('ROLE_CLIENT_ADMIN')"
)]
class AuditKeyword
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    private Uuid $id;

    #[ORM\ManyToOne(targetEntity: AuditIntake::class, inversedBy: 'keywords')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?AuditIntake $intake = null;

    #[ORM\Column(length: 255)]
    private string $phrase;

    #[ORM\Column(length: 16)]
    private string $intent = 'local';

    #[ORM\Column(type: 'smallint', options: ['default' => 3])]
    private int $priority = 3;

    public function __construct() 
    { 
        $this->id = Uuid::v7(); 
    }

    // Getters and Setters
    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getIntake(): ?AuditIntake
    {
        return $this->intake;
    }

    public function setIntake(?AuditIntake $intake): self
    {
        $this->intake = $intake;
        return $this;
    }

    public function getPhrase(): string
    {
        return $this->phrase;
    }

    public function setPhrase(string $phrase): self
    {
        $this->phrase = $phrase;
        return $this;
    }

    public function getIntent(): string
    {
        return $this->intent;
    }

    public function setIntent(string $intent): self
    {
        $this->intent = $intent;
        return $this;
    }

    public function getPriority(): int
    {
        return $this->priority;
    }

    public function setPriority(int $priority): self
    {
        $this->priority = $priority;
        return $this;
    }
}
