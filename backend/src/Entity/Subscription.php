<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\DateFilter;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\Table(name: 'subscriptions')]
#[ORM\HasLifecycleCallbacks]
#[ApiResource(
    operations: [
        new Get(security: "is_granted('ROLE_AGENCY_ADMIN') or is_granted('ROLE_AGENCY_STAFF') or (is_granted('ROLE_CLIENT_ADMIN') and object.getClientId() == user.getClientId())"),
        new GetCollection(security: "is_granted('ROLE_AGENCY_ADMIN') or is_granted('ROLE_AGENCY_STAFF') or is_granted('ROLE_CLIENT_ADMIN')"),
        new Post(security: "is_granted('ROLE_AGENCY_ADMIN') or is_granted('ROLE_CLIENT_ADMIN')"),
        new Put(security: "is_granted('ROLE_AGENCY_ADMIN') or (is_granted('ROLE_CLIENT_ADMIN') and object.getClientId() == user.getClientId())"),
        new Delete(security: "is_granted('ROLE_AGENCY_ADMIN')")
    ],
    normalizationContext: ['groups' => ['subscription:read']],
    denormalizationContext: ['groups' => ['subscription:write']]
)]
#[ApiFilter(SearchFilter::class, properties: [
    'name' => 'partial',
    'status' => 'exact',
    'clientId' => 'exact',
    'packageId' => 'exact'
])]
#[ApiFilter(OrderFilter::class, properties: ['createdAt' => 'DESC', 'nextBillingDate' => 'ASC', 'name' => 'ASC'])]
#[ApiFilter(DateFilter::class, properties: ['createdAt', 'startDate', 'nextBillingDate', 'cancelledAt'])]
class Subscription
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    #[ApiProperty(identifier: true)]
    #[Groups(['subscription:read'])]
    private string $id;

    #[ORM\Column(name: 'tenant_id', type: 'uuid', nullable: true)]
    private ?string $tenantId = null;

    #[ORM\Column(name: 'client_id', type: 'uuid')]
    #[Groups(['subscription:read', 'subscription:write'])]
    private string $clientId;

    #[ORM\Column(name: 'package_id', type: 'uuid')]
    #[Groups(['subscription:read', 'subscription:write'])]
    private string $packageId;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank]
    #[Groups(['subscription:read', 'subscription:write'])]
    private string $name;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['subscription:read', 'subscription:write'])]
    private ?string $description = null;

    #[ORM\Column(type: 'string', length: 50)]
    #[Assert\Choice(['active', 'cancelled', 'suspended', 'expired', 'pending'])]
    #[Groups(['subscription:read', 'subscription:write'])]
    private string $status = 'pending';

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    #[Groups(['subscription:read', 'subscription:write'])]
    private string $amount;

    #[ORM\Column(type: 'string', length: 3)]
    #[Groups(['subscription:read', 'subscription:write'])]
    private string $currency = 'USD';

    #[ORM\Column(type: 'string', length: 20)]
    #[Assert\Choice(['monthly', 'quarterly', 'annually', 'one_time'])]
    #[Groups(['subscription:read', 'subscription:write'])]
    private string $billingCycle = 'monthly';

    #[ORM\Column(name: 'start_date', type: 'datetime_immutable')]
    #[Groups(['subscription:read', 'subscription:write'])]
    private \DateTimeImmutable $startDate;

    #[ORM\Column(name: 'next_billing_date', type: 'datetime_immutable', nullable: true)]
    #[Groups(['subscription:read', 'subscription:write'])]
    private ?\DateTimeImmutable $nextBillingDate = null;

    #[ORM\Column(name: 'cancelled_at', type: 'datetime_immutable', nullable: true)]
    #[Groups(['subscription:read', 'subscription:write'])]
    private ?\DateTimeImmutable $cancelledAt = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['subscription:read', 'subscription:write'])]
    private ?string $stripeSubscriptionId = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['subscription:read', 'subscription:write'])]
    private ?string $stripeCustomerId = null;

    #[ORM\Column(type: 'integer', options: ['default' => 1])]
    #[Groups(['subscription:read', 'subscription:write'])]
    private int $quantity = 1;

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    #[Groups(['subscription:read', 'subscription:write'])]
    private bool $autoRenew = true;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['subscription:read', 'subscription:write'])]
    private ?string $notes = null;

    #[ORM\Column(type: 'jsonb', nullable: true)]
    #[Groups(['subscription:read', 'subscription:write'])]
    private ?array $features = [];

    #[ORM\Column(type: 'jsonb', nullable: true)]
    #[Groups(['subscription:read', 'subscription:write'])]
    private ?array $metadata = [];

    #[ORM\Column(name: 'created_at', type: 'datetime_immutable')]
    #[Groups(['subscription:read'])]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(name: 'updated_at', type: 'datetime_immutable')]
    #[Groups(['subscription:read'])]
    private \DateTimeImmutable $updatedAt;

    public function __construct()
    {
        $this->id = Uuid::v4()->toRfc4122();
        $now = new \DateTimeImmutable();
        $this->createdAt = $now;
        $this->updatedAt = $now;
        $this->startDate = $now;
        $this->features = [];
        $this->metadata = [];
    }

    #[ORM\PreUpdate]
    public function setUpdatedAt(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }

    // Getters and Setters
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

    public function getClientId(): string
    {
        return $this->clientId;
    }

    public function setClientId(string $clientId): self
    {
        $this->clientId = $clientId;
        return $this;
    }

    public function getPackageId(): string
    {
        return $this->packageId;
    }

    public function setPackageId(string $packageId): self
    {
        $this->packageId = $packageId;
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;
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

    public function getAmount(): string
    {
        return $this->amount;
    }

    public function setAmount(string $amount): self
    {
        $this->amount = $amount;
        return $this;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function setCurrency(string $currency): self
    {
        $this->currency = $currency;
        return $this;
    }

    public function getBillingCycle(): string
    {
        return $this->billingCycle;
    }

    public function setBillingCycle(string $billingCycle): self
    {
        $this->billingCycle = $billingCycle;
        return $this;
    }

    public function getStartDate(): \DateTimeImmutable
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeImmutable $startDate): self
    {
        $this->startDate = $startDate;
        return $this;
    }

    public function getNextBillingDate(): ?\DateTimeImmutable
    {
        return $this->nextBillingDate;
    }

    public function setNextBillingDate(?\DateTimeImmutable $nextBillingDate): self
    {
        $this->nextBillingDate = $nextBillingDate;
        return $this;
    }

    public function getCancelledAt(): ?\DateTimeImmutable
    {
        return $this->cancelledAt;
    }

    public function setCancelledAt(?\DateTimeImmutable $cancelledAt): self
    {
        $this->cancelledAt = $cancelledAt;
        return $this;
    }

    public function getStripeSubscriptionId(): ?string
    {
        return $this->stripeSubscriptionId;
    }

    public function setStripeSubscriptionId(?string $stripeSubscriptionId): self
    {
        $this->stripeSubscriptionId = $stripeSubscriptionId;
        return $this;
    }

    public function getStripeCustomerId(): ?string
    {
        return $this->stripeCustomerId;
    }

    public function setStripeCustomerId(?string $stripeCustomerId): self
    {
        $this->stripeCustomerId = $stripeCustomerId;
        return $this;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;
        return $this;
    }

    public function getAutoRenew(): bool
    {
        return $this->autoRenew;
    }

    public function setAutoRenew(bool $autoRenew): self
    {
        $this->autoRenew = $autoRenew;
        return $this;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(?string $notes): self
    {
        $this->notes = $notes;
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

    public function getMetadata(): array
    {
        return $this->metadata;
    }

    public function setMetadata(array $metadata): self
    {
        $this->metadata = $metadata;
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
}
