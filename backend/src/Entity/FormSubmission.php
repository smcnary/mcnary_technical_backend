<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
#[ORM\Table(name: 'form_submissions')]
#[ApiResource(security: "is_granted('ROLE_USER')")]
class FormSubmission
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    private string $id;

    #[ORM\Column(name: 'tenant_id', type: 'uuid')]
    public string $tenantId;

    #[ORM\Column(name: 'form_id', type: 'uuid')]
    public string $formId;

    #[ORM\Column(type: 'json')]
    public array $data = [];

    #[ORM\Column(name: 'ip_address', type: 'string', nullable: true)]
    public ?string $ipAddress = null;

    #[ORM\Column(name: 'user_agent', type: 'string', nullable: true)]
    public ?string $userAgent = null;

    #[ORM\Column(name: 'created_at', type: 'datetimetz_immutable')]
    public \DateTimeImmutable $createdAt;

    public function __construct()
    {
        $this->id = Uuid::v4()->toRfc4122();
        $this->createdAt = new \DateTimeImmutable();
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

    public function getFormId(): string
    {
        return $this->formId;
    }

    public function setFormId(string $formId): self
    {
        $this->formId = $formId;
        return $this;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function setData(array $data): self
    {
        $this->data = $data;
        return $this;
    }

    public function getIpAddress(): ?string
    {
        return $this->ipAddress;
    }

    public function setIpAddress(?string $ipAddress): self
    {
        $this->ipAddress = $ipAddress;
        return $this;
    }

    public function getUserAgent(): ?string
    {
        return $this->userAgent;
    }

    public function setUserAgent(?string $userAgent): self
    {
        $this->userAgent = $userAgent;
        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }
}
