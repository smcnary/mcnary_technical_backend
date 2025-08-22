<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Uid\Uuid;
use App\Entity\Tenant;

#[ORM\Entity]
#[ORM\Table(name: 'users')]
#[ORM\HasLifecycleCallbacks]
#[ApiResource(
    security: "is_granted('ROLE_SYSTEM_ADMIN') or (is_granted('ROLE_AGENCY_ADMIN') and object.agency == user.agency) or (is_granted('ROLE_CLIENT_USER') and object.clientId == user.clientId) or object == user"
)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    use Timestamps;

    // Role constants for the new access control system
    public const ROLE_SYSTEM_ADMIN = 'ROLE_SYSTEM_ADMIN';
    public const ROLE_AGENCY_ADMIN = 'ROLE_AGENCY_ADMIN';
    public const ROLE_CLIENT_USER = 'ROLE_CLIENT_USER';
    public const ROLE_READ_ONLY = 'ROLE_READ_ONLY';

    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private string $id;

    #[ORM\ManyToOne(targetEntity: Agency::class, inversedBy: 'users')]
    #[ORM\JoinColumn(name: 'agency_id', nullable: true, onDelete: 'SET NULL')]
    private ?Agency $agency = null;

    #[ORM\Column(name: 'client_id', type: 'uuid', nullable: true)]
    private ?string $clientId = null;

    #[ORM\Column(type: 'string')]
    #[Assert\Email]
    private string $email;

    #[ORM\Column(name: 'password_hash', type: 'string', nullable: true)]
    private ?string $passwordHash = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $firstName = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $lastName = null;

    #[ORM\Column(type: 'string', options: ['default' => 'invited'])]
    private string $status = 'invited';

    #[ORM\Column(name: 'last_login_at', type: 'datetimetz_immutable', nullable: true)]
    private ?\DateTimeImmutable $lastLoginAt = null;

    #[ORM\Column(type: 'string', length: 32)]
    private string $role; // 'AGENCY_ADMIN','AGENCY_STAFF','CLIENT_ADMIN','CLIENT_STAFF'

    /** @var Collection<int,UserClientAccess> */
    #[ORM\OneToMany(mappedBy: 'user', targetEntity: UserClientAccess::class, cascade: ['persist'], orphanRemoval: true)]
    private Collection $clientAccess;

    #[ORM\Column(type: 'jsonb', nullable: true)]
    private ?array $metadata = [];

    public function __construct(?Agency $agency, string $email, string $hash, string $role)
    {
        $this->id = Uuid::v4()->toRfc4122();
        $this->agency = $agency;
        $this->email = $email;
        $this->passwordHash = $hash;
        $this->role = $role;
        $this->clientAccess = new ArrayCollection();
        $this->metadata = [];
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
        return array_unique(array_merge(['ROLE_USER'], [$this->role]));
    }

    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    public function isSystemAdmin(): bool
    {
        return $this->hasRole(self::ROLE_SYSTEM_ADMIN);
    }

    public function isAgencyAdmin(): bool
    {
        return $this->hasRole(self::ROLE_AGENCY_ADMIN);
    }

    public function isClientUser(): bool
    {
        return $this->hasRole(self::ROLE_CLIENT_USER);
    }

    public function isReadOnly(): bool
    {
        return $this->hasRole(self::ROLE_READ_ONLY);
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getAgency(): ?Agency
    {
        return $this->agency;
    }

    public function setAgency(?Agency $agency): self
    {
        $this->agency = $agency;
        return $this;
    }

    public function getClientId(): ?string
    {
        return $this->clientId;
    }

    public function setClientId(?string $clientId): self
    {
        $this->clientId = $clientId;
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

    public function getPasswordHash(): ?string
    {
        return $this->passwordHash;
    }

    public function setPasswordHash(string $passwordHash): self
    {
        $this->passwordHash = $passwordHash;
        return $this;
    }

    public function getName(): ?string
    {
        if ($this->firstName && $this->lastName) {
            return $this->firstName . ' ' . $this->lastName;
        }
        return $this->firstName ?: $this->lastName;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): self
    {
        $this->firstName = $firstName;
        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): self
    {
        $this->lastName = $lastName;
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

    public function getRole(): string
    {
        return $this->role;
    }

    public function setRole(string $role): self
    {
        $this->role = $role;
        return $this;
    }

    public function getClientAccess(): Collection
    {
        return $this->clientAccess;
    }

    public function addClientAccess(UserClientAccess $clientAccess): self
    {
        if (!$this->clientAccess->contains($clientAccess)) {
            $this->clientAccess->add($clientAccess);
            $clientAccess->setUser($this);
        }
        return $this;
    }

    public function removeClientAccess(UserClientAccess $clientAccess): self
    {
        if ($this->clientAccess->removeElement($clientAccess)) {
            if ($clientAccess->getUser() === $this) {
                $clientAccess->setUser(null);
            }
        }
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
}
