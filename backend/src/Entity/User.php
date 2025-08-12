<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
#[ORM\Table(name: 'users')]
#[ORM\UniqueConstraint(columns: ['tenant_id', 'email'])]
#[ApiResource(security: "is_granted('ROLE_ADMIN') or object == user")]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    private string $id;

    #[ORM\Column(name: 'tenant_id', type: 'uuid', nullable: true)]
    private ?string $tenantId = null;

    #[ORM\Column(type: 'string')]
    #[Assert\Email]
    private string $email;

    #[ORM\Column(name: 'password_hash', type: 'string', nullable: true)]
    private ?string $passwordHash = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $name = null;

    #[ORM\Column(type: 'string', options: ['default' => 'invited'])]
    private string $status = 'invited';

    #[ORM\Column(name: 'last_login_at', type: 'datetimetz_immutable', nullable: true)]
    private ?\DateTimeImmutable $lastLoginAt = null;

    #[ORM\Column(name: 'created_at', type: 'datetimetz_immutable')]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(name: 'updated_at', type: 'datetimetz_immutable')]
    private \DateTimeImmutable $updatedAt;

    #[ORM\Column(type: 'json')]
    private array $roles = [];

    public function __construct()
    {
        $this->id = Uuid::v4()->toRfc4122();
        $now = new \DateTimeImmutable();
        $this->createdAt = $now;
        $this->updatedAt = $now;
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function getPassword(): ?string
    {
        return $this->passwordHash;
    }

    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
    }

    public function getRoles(): array
    {
        return array_unique(array_merge(['ROLE_USER'], $this->roles));
    }

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

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function setPasswordHash(?string $passwordHash): self
    {
        $this->passwordHash = $passwordHash;
        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;
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

    public function getLastLoginAt(): ?\DateTimeImmutable
    {
        return $this->lastLoginAt;
    }

    public function setLastLoginAt(?\DateTimeImmutable $lastLoginAt): self
    {
        $this->lastLoginAt = $lastLoginAt;
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

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;
        return $this;
    }
}
