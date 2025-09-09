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
use App\Entity\Organization;
use App\Entity\Agency;

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
    public const ROLE_AGENCY_STAFF = 'ROLE_AGENCY_STAFF';
    public const ROLE_CLIENT_ADMIN = 'ROLE_CLIENT_ADMIN';
    public const ROLE_CLIENT_STAFF = 'ROLE_CLIENT_STAFF';
    public const ROLE_CLIENT_USER = 'ROLE_CLIENT_USER';
    public const ROLE_READ_ONLY = 'ROLE_READ_ONLY';

    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private string $id;

    #[ORM\ManyToOne(targetEntity: Organization::class)]
    #[ORM\JoinColumn(name: 'organization_id', nullable: false)]
    private Organization $organization;

    #[ORM\ManyToOne(targetEntity: Agency::class, inversedBy: 'users')]
    #[ORM\JoinColumn(name: 'agency_id', nullable: true)]
    private ?Agency $agency = null;

    #[ORM\ManyToOne(targetEntity: Tenant::class)]
    #[ORM\JoinColumn(name: 'tenant_id', nullable: true)]
    private ?Tenant $tenant = null;

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

    /** @var Collection<int,AuditIntake> */
    #[ORM\OneToMany(mappedBy: 'requestedBy', targetEntity: AuditIntake::class)]
    private Collection $requestedAuditIntakes;

    /** @var Collection<int,AuditRun> */
    #[ORM\OneToMany(mappedBy: 'initiatedBy', targetEntity: AuditRun::class)]
    private Collection $initiatedAuditRuns;

    /** @var Collection<int,OAuthConnection> */
    #[ORM\OneToMany(mappedBy: 'user', targetEntity: OAuthConnection::class, cascade: ['persist'], orphanRemoval: true)]
    private Collection $oauthConnections;

    /** @var Collection<int,OAuthToken> */
    #[ORM\OneToMany(mappedBy: 'user', targetEntity: OAuthToken::class, cascade: ['persist'], orphanRemoval: true)]
    private Collection $tokens;

    /** @var Collection<int,UserClientAccess> */
    #[ORM\OneToMany(mappedBy: 'user', targetEntity: UserClientAccess::class, cascade: ['persist'], orphanRemoval: true)]
    private Collection $userAccess;

    #[ORM\Column(type: 'jsonb', nullable: true)]
    private ?array $metadata = [];

    public function __construct(Organization $organization, string $email, string $hash, string $role)
    {
        $this->id = Uuid::v4()->toRfc4122();
        $this->organization = $organization;
        $this->email = $email;
        $this->passwordHash = $hash;
        $this->role = $role;
        $this->requestedAuditIntakes = new ArrayCollection();
        $this->initiatedAuditRuns = new ArrayCollection();
        $this->oauthConnections = new ArrayCollection();
        $this->tokens = new ArrayCollection();
        $this->userAccess = new ArrayCollection();
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

    public function getOrganization(): Organization
    {
        return $this->organization;
    }

    public function setOrganization(Organization $organization): self
    {
        $this->organization = $organization;
        return $this;
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

    public function getTenant(): ?Tenant
    {
        return $this->tenant;
    }

    public function setTenant(?Tenant $tenant): self
    {
        $this->tenant = $tenant;
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

    public function getUserAccess(): Collection
    {
        return $this->userAccess;
    }

    public function addUserAccess(UserClientAccess $userAccess): self
    {
        if (!$this->userAccess->contains($userAccess)) {
            $this->userAccess->add($userAccess);
            $userAccess->setUser($this);
        }
        return $this;
    }

    public function removeUserAccess(UserClientAccess $userAccess): self
    {
        if ($this->userAccess->removeElement($userAccess)) {
            if ($userAccess->getUser() === $this) {
                $userAccess->setUser(null);
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

    // Audit-related methods
    public function getRequestedAuditIntakes(): Collection
    {
        return $this->requestedAuditIntakes;
    }

    public function addRequestedAuditIntake(AuditIntake $auditIntake): self
    {
        if (!$this->requestedAuditIntakes->contains($auditIntake)) {
            $this->requestedAuditIntakes->add($auditIntake);
            $auditIntake->setRequestedBy($this);
        }
        return $this;
    }

    public function removeRequestedAuditIntake(AuditIntake $auditIntake): self
    {
        if ($this->requestedAuditIntakes->removeElement($auditIntake)) {
            if ($auditIntake->getRequestedBy() === $this) {
                $auditIntake->setRequestedBy(null);
            }
        }
        return $this;
    }

    public function getInitiatedAuditRuns(): Collection
    {
        return $this->initiatedAuditRuns;
    }

    public function addInitiatedAuditRun(AuditRun $auditRun): self
    {
        if (!$this->initiatedAuditRuns->contains($auditRun)) {
            $this->initiatedAuditRuns->add($auditRun);
            $auditRun->setInitiatedBy($this);
        }
        return $this;
    }

    public function removeInitiatedAuditRun(AuditRun $auditRun): self
    {
        if ($this->initiatedAuditRuns->removeElement($auditRun)) {
            if ($auditRun->getInitiatedBy() === $this) {
                $auditRun->setInitiatedBy(null);
            }
        }
        return $this;
    }
}
