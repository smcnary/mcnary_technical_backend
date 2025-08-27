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
#[ORM\Table(name: 'client_locations')]
#[ORM\HasLifecycleCallbacks]
#[ApiResource(
    operations: [
        new Get(security: "is_granted('ROLE_AGENCY_ADMIN') or is_granted('ROLE_AGENCY_STAFF') or (is_granted('ROLE_CLIENT_ADMIN') and object.getClient().getClientId() == user.getClientId())"),
        new GetCollection(security: "is_granted('ROLE_AGENCY_ADMIN') or is_granted('ROLE_AGENCY_STAFF')"),
        new Post(security: "is_granted('ROLE_AGENCY_ADMIN')"),
        new Put(security: "is_granted('ROLE_AGENCY_ADMIN') or (is_granted('ROLE_CLIENT_ADMIN') and object.getClient().getClientId() == user.getClientId())"),
        new Delete(security: "is_granted('ROLE_AGENCY_ADMIN')")
    ]
)]
class ClientLocation
{
    use Timestamps;

    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private string $id;

    #[ORM\ManyToOne(inversedBy: 'locations')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Client $client = null;

    #[ORM\Column]
    private string $label;

    #[ORM\Column(nullable: true)]
    private ?string $street = null;

    #[ORM\Column(nullable: true)]
    private ?string $city = null;

    #[ORM\Column(nullable: true)]
    private ?string $region = null;

    #[ORM\Column(nullable: true)]
    private ?string $postalCode = null;

    #[ORM\Column(nullable: true)]
    private ?string $country = null;

    #[ORM\Column(nullable: true)]
    private ?string $phone = null;

    #[ORM\Column(nullable: true)]
    private ?string $email = null;

    #[ORM\Column(options: ['default' => false])]
    private bool $isPrimary = false;

    #[ORM\Column(type: 'jsonb', nullable: true)]
    private ?array $metadata = [];

    #[ORM\Column(type: 'string', options: ['default' => 'active'])]
    private string $status = 'active';

    public function __construct(Client $client, string $label)
    {
        $this->id = Uuid::v4()->toRfc4122();
        $this->client = $client;
        $this->label = $label;
        $this->metadata = [];
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): self
    {
        $this->client = $client;
        return $this;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;
        return $this;
    }

    public function getStreet(): ?string
    {
        return $this->street;
    }

    public function setStreet(?string $street): self
    {
        $this->street = $street;
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

    public function getRegion(): ?string
    {
        return $this->region;
    }

    public function setRegion(?string $region): self
    {
        $this->region = $region;
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

    public function isPrimary(): bool
    {
        return $this->isPrimary;
    }

    public function setIsPrimary(bool $isPrimary): self
    {
        $this->isPrimary = $isPrimary;
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

    public function getFullAddress(): string
    {
        $parts = array_filter([
            $this->street,
            $this->city,
            $this->region,
            $this->postalCode,
            $this->country
        ]);
        
        return implode(', ', $parts);
    }
}
