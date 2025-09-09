<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Delete;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
#[ORM\Table(name: 'user_client_access', uniqueConstraints: [
    new ORM\UniqueConstraint(name: 'uniq_user_client', columns: ['user_id','client_id'])
])]
#[ORM\HasLifecycleCallbacks]
#[ApiResource(
    operations: [
        new Get(security: "is_granted('ROLE_AGENCY_ADMIN') or is_granted('ROLE_AGENCY_STAFF')"),
        new GetCollection(security: "is_granted('ROLE_AGENCY_ADMIN') or is_granted('ROLE_AGENCY_STAFF')"),
        new Post(security: "is_granted('ROLE_AGENCY_ADMIN')"),
        new Put(security: "is_granted('ROLE_AGENCY_ADMIN')"),
        new Delete(security: "is_granted('ROLE_AGENCY_ADMIN')")
    ]
)]
class UserClientAccess
{
    use Timestamps;

    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private string $id;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'userAccess')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private User $user;

    #[ORM\ManyToOne(targetEntity: Client::class, inversedBy: 'userAccess')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Client $client;

    #[ORM\Column(type: 'jsonb', nullable: true)]
    private ?array $permissions = [];

    #[ORM\Column(type: 'string', options: ['default' => 'active'])]
    private string $status = 'active';

    public function __construct(User $user, Client $client)
    {
        $this->id = Uuid::v4()->toRfc4122();
        $this->user = $user;
        $this->client = $client;
        $this->permissions = [];
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function getClient(): Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): self
    {
        $this->client = $client;
        return $this;
    }

    public function getPermissions(): ?array
    {
        return $this->permissions;
    }

    public function setPermissions(?array $permissions): self
    {
        $this->permissions = $permissions;
        return $this;
    }

    public function hasPermission(string $permission): bool
    {
        return in_array($permission, $this->permissions ?? [], true);
    }

    public function addPermission(string $permission): self
    {
        if (!$this->hasPermission($permission)) {
            $this->permissions[] = $permission;
        }
        return $this;
    }

    public function removePermission(string $permission): self
    {
        $this->permissions = array_filter($this->permissions ?? [], fn($p) => $p !== $permission);
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
}
