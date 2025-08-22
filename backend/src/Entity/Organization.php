<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Delete;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\Table(name: 'organizations')]
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
class Organization
{
    use Timestamps;

    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private string $id;

    #[ORM\Column]
    #[Assert\NotBlank]
    private string $name;

    #[ORM\Column(nullable: true)]
    private ?string $domain = null;

    #[ORM\Column(type: 'jsonb', nullable: true)]
    private ?array $metadata = [];

    #[ORM\Column(type: 'string', options: ['default' => 'active'])]
    private string $status = 'active';

    // Note: Organization relationships are not currently implemented
    // These collections are kept for future use but not mapped to avoid Doctrine errors
    private Collection $users;

    private Collection $clients;

    public function __construct(string $name)
    {
        $this->id = Uuid::v4()->toRfc4122();
        $this->name = $name;
        $this->users = new ArrayCollection();
        $this->clients = new ArrayCollection();
        $this->metadata = [];
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
    
    public function getDomain(): ?string 
    { 
        return $this->domain; 
    }
    
    public function setDomain(?string $domain): self 
    { 
        $this->domain = $domain; 
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

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;
        return $this;
    }
    
    public function getUsers(): Collection 
    { 
        return $this->users; 
    }
    
    public function getClients(): Collection 
    { 
        return $this->clients; 
    }

    // Note: Organization relationship methods are not currently implemented
    // These methods are kept for future use but don't perform actual operations
    public function addUser(User $user): self
    {
        // TODO: Implement when organization-user relationship is properly mapped
        return $this;
    }

    public function removeUser(User $user): self
    {
        // TODO: Implement when organization-user relationship is properly mapped
        return $this;
    }

    public function addClient(Client $client): self
    {
        // TODO: Implement when organization-client relationship is properly mapped
        return $this;
    }

    public function removeClient(Client $client): self
    {
        // TODO: Implement when organization-client relationship is properly mapped
        return $this;
    }
}
