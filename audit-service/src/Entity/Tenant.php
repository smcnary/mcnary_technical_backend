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
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\Table(name: 'tenant')]
#[ORM\HasLifecycleCallbacks]
#[ApiResource(
    operations: [
        new Get(security: "is_granted('ROLE_SUPER_ADMIN')"),
        new GetCollection(security: "is_granted('ROLE_SUPER_ADMIN')"),
        new Post(security: "is_granted('ROLE_SUPER_ADMIN')"),
        new Put(security: "is_granted('ROLE_SUPER_ADMIN')")
    ]
)]
class Tenant
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'uuid')]
    private string $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private string $name;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    #[ORM\OneToMany(mappedBy: 'tenant', targetEntity: User::class)]
    private Collection $users;

    #[ORM\OneToMany(mappedBy: 'tenant', targetEntity: Client::class)]
    private Collection $clients;

    #[ORM\OneToMany(mappedBy: 'tenant', targetEntity: Project::class)]
    private Collection $projects;

    #[ORM\OneToMany(mappedBy: 'tenant', targetEntity: Audit::class)]
    private Collection $audits;

    #[ORM\OneToMany(mappedBy: 'tenant', targetEntity: Credential::class)]
    private Collection $credentials;

    #[ORM\OneToMany(mappedBy: 'tenant', targetEntity: AuditRun::class)]
    private Collection $auditRuns;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->users = new ArrayCollection();
        $this->clients = new ArrayCollection();
        $this->projects = new ArrayCollection();
        $this->audits = new ArrayCollection();
        $this->credentials = new ArrayCollection();
        $this->auditRuns = new ArrayCollection();
    }

    #[ORM\PrePersist]
    public function setCreatedAtValue(): void
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): string
    {
        return $this->id;
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

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->setTenant($this);
        }
        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->removeElement($user)) {
            if ($user->getTenant() === $this) {
                $user->setTenant(null);
            }
        }
        return $this;
    }

    public function getClients(): Collection
    {
        return $this->clients;
    }

    public function getProjects(): Collection
    {
        return $this->projects;
    }

    public function getAudits(): Collection
    {
        return $this->audits;
    }

    public function getCredentials(): Collection
    {
        return $this->credentials;
    }

    public function getAuditRuns(): Collection
    {
        return $this->auditRuns;
    }
}
