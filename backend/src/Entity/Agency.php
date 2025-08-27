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
#[ORM\Table(name: 'agencies')]
#[ORM\HasLifecycleCallbacks]
#[ApiResource(
    operations: [
        new Get(security: "is_granted('ROLE_SYSTEM_ADMIN') or (is_granted('ROLE_AGENCY_ADMIN') and object == user.agency)"),
        new GetCollection(security: "is_granted('ROLE_SYSTEM_ADMIN') or is_granted('ROLE_AGENCY_ADMIN')"),
        new Post(security: "is_granted('ROLE_SYSTEM_ADMIN')"),
        new Put(security: "is_granted('ROLE_SYSTEM_ADMIN') or (is_granted('ROLE_AGENCY_ADMIN') and object == user.agency)"),
        new Delete(security: "is_granted('ROLE_SYSTEM_ADMIN')")
    ]
)]
class Agency
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

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $websiteUrl = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $phone = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $email = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $address = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $city = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $state = null;

    #[ORM\Column(type: 'string', length: 10, nullable: true)]
    private ?string $postalCode = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $country = null;

    #[ORM\Column(type: 'string', options: ['default' => 'active'])]
    private string $status = 'active';

    #[ORM\Column(type: 'jsonb', nullable: true)]
    private ?array $metadata = [];

    /** @var Collection<int,User> */
    #[ORM\OneToMany(mappedBy: 'agency', targetEntity: User::class, cascade: ['persist'], orphanRemoval: true, inversedBy: 'agency')]
    private Collection $users;

    /** @var Collection<int,Client> */
    #[ORM\OneToMany(mappedBy: 'agency', targetEntity: Client::class, cascade: ['persist'], orphanRemoval: true)]
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;
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

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): self
    {
        $this->address = $address;
        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): self
    {
        $this->city = $city;
        return $this;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(?string $state): self
    {
        $this->state = $state;
        return $this;
    }

    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    public function setPostalCode(?string $postalCode): self
    {
        $this->postalCode = $postalCode;
        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): self
    {
        $this->country = $country;
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

    public function getMetadata(): ?array
    {
        return $this->metadata;
    }

    public function setMetadata(?array $metadata): self
    {
        $this->metadata = $metadata;
        return $this;
    }

    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->setAgency($this);
        }
        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->removeElement($user)) {
            if ($user->getAgency() === $this) {
                $user->setAgency(null);
            }
        }
        return $this;
    }

    public function getClients(): Collection
    {
        return $this->clients;
    }

    public function addClient(Client $client): self
    {
        if (!$this->clients->contains($client)) {
            $this->clients->add($client);
            $client->setAgency($this);
        }
        return $this;
    }

    public function removeClient(Client $client): self
    {
        if ($this->clients->removeElement($client)) {
            if ($client->getAgency() === $this) {
                $client->setAgency(null);
            }
        }
        return $this;
    }
}
