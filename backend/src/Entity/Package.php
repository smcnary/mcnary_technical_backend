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
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\Table(name: 'packages')]
#[ORM\HasLifecycleCallbacks]
#[ApiResource(
    operations: [
        new Get(security: "is_granted('ROLE_AGENCY_ADMIN') or is_granted('ROLE_AGENCY_STAFF') or (is_granted('ROLE_CLIENT_ADMIN') and object.getClientId() == user.getClientId())"),
        new GetCollection(security: "is_granted('ROLE_AGENCY_ADMIN') or is_granted('ROLE_AGENCY_STAFF') or is_granted('ROLE_CLIENT_ADMIN') or is_granted('ROLE_CLIENT_STAFF')"),
        new Post(security: "is_granted('ROLE_AGENCY_ADMIN')"),
        new Put(security: "is_granted('ROLE_AGENCY_ADMIN') or (is_granted('ROLE_CLIENT_ADMIN') and object.getClientId() == user.getClientId())"),
        new Delete(security: "is_granted('ROLE_AGENCY_ADMIN')")
    ]
)]
class Package
{
    use Timestamps;

    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private string $id;

    #[ORM\Column(name: 'client_id', type: 'uuid', nullable: true)]
    private ?string $clientId = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank]
    private string $name;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $slug = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2, nullable: true)]
    private ?string $price = null;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private ?string $billingCycle = null; // monthly, quarterly, annually

    #[ORM\Column(type: 'jsonb')]
    private array $features = [];

    #[ORM\Column(type: 'jsonb')]
    private array $includedServices = [];

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $isPopular = false;

    #[ORM\Column(type: 'boolean', options: ['default' => true])]
    private bool $isActive = true;

    #[ORM\Column(type: 'integer', options: ['default' => 0])]
    private int $sortOrder = 0;

    #[ORM\Column(type: 'jsonb', nullable: true)]
    private ?array $metadata = [];

    public function __construct(string $name)
    {
        $this->id = Uuid::v4()->toRfc4122();
        $this->name = $name;
        $this->features = [];
        $this->includedServices = [];
        $this->metadata = [];
    }

    public function getId(): string
    {
        return $this->id;
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

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): self
    {
        $this->slug = $slug;
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

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(?string $price): self
    {
        $this->price = $price;
        return $this;
    }

    public function getBillingCycle(): ?string
    {
        return $this->billingCycle;
    }

    public function setBillingCycle(?string $billingCycle): self
    {
        $this->billingCycle = $billingCycle;
        return $this;
    }

    public function getFeatures(): array
    {
        return $this->features;
    }

    public function setFeatures(array $features): self
    {
        $this->features = $features;
        return $this;
    }

    public function addFeature(string $feature): self
    {
        if (!in_array($feature, $this->features, true)) {
            $this->features[] = $feature;
        }
        return $this;
    }

    public function removeFeature(string $feature): self
    {
        $this->features = array_filter($this->features, fn($f) => $f !== $feature);
        return $this;
    }

    public function hasFeature(string $feature): bool
    {
        return in_array($feature, $this->features, true);
    }

    public function getIncludedServices(): array
    {
        return $this->includedServices;
    }

    public function setIncludedServices(array $includedServices): self
    {
        $this->includedServices = $includedServices;
        return $this;
    }

    public function addIncludedService(string $service): self
    {
        if (!in_array($service, $this->includedServices, true)) {
            $this->includedServices[] = $service;
        }
        return $this;
    }

    public function removeIncludedService(string $service): self
    {
        $this->includedServices = array_filter($this->includedServices, fn($s) => $s !== $service);
        return $this;
    }

    public function hasIncludedService(string $service): bool
    {
        return in_array($service, $this->includedServices, true);
    }

    public function isPopular(): bool
    {
        return $this->isPopular;
    }

    public function setIsPopular(bool $isPopular): self
    {
        $this->isPopular = $isPopular;
        return $this;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;
        return $this;
    }

    public function getSortOrder(): int
    {
        return $this->sortOrder;
    }

    public function setSortOrder(int $sortOrder): self
    {
        $this->sortOrder = $sortOrder;
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

    public function getMetaValue(string $key, $default = null)
    {
        return $this->metadata[$key] ?? $default;
    }

    public function setMetaValue(string $key, $value): self
    {
        $this->metadata[$key] = $value;
        return $this;
    }

    public function getPriceFormatted(): ?string
    {
        if ($this->price === null) {
            return null;
        }
        
        return '$' . number_format($this->price, 2);
    }

    public function getBillingCycleFormatted(): ?string
    {
        if ($this->billingCycle === null) {
            return null;
        }
        
        $cycles = [
            'monthly' => '/month',
            'quarterly' => '/quarter',
            'annually' => '/year'
        ];
        
        return $cycles[$this->billingCycle] ?? $this->billingCycle;
    }

    public function getFullPriceDisplay(): ?string
    {
        if ($this->price === null) {
            return null;
        }
        
        $price = $this->getPriceFormatted();
        $cycle = $this->getBillingCycleFormatted();
        
        return $price . $cycle;
    }

    // Legacy getter for backward compatibility
    public function getTenantId(): ?string
    {
        // This method is kept for backward compatibility but should not be used
        return null;
    }

    public function setTenantId(?string $tenantId): self
    {
        // This method is kept for backward compatibility but should not be used
        return $this;
    }
}
