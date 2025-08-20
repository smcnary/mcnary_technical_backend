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
use ApiPlatform\Doctrine\Orm\Filter\RangeFilter;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\Table(name: 'invoices')]
#[ORM\HasLifecycleCallbacks]
#[ApiResource(
    operations: [
        new Get(security: "is_granted('ROLE_AGENCY_ADMIN') or is_granted('ROLE_AGENCY_STAFF') or (is_granted('ROLE_CLIENT_ADMIN') and object.getClientId() == user.getClientId())"),
        new GetCollection(security: "is_granted('ROLE_AGENCY_ADMIN') or is_granted('ROLE_AGENCY_STAFF') or is_granted('ROLE_CLIENT_ADMIN')"),
        new Post(security: "is_granted('ROLE_AGENCY_ADMIN') or is_granted('ROLE_CLIENT_ADMIN')"),
        new Put(security: "is_granted('ROLE_AGENCY_ADMIN') or (is_granted('ROLE_CLIENT_ADMIN') and object.getClientId() == user.getClientId())"),
        new Delete(security: "is_granted('ROLE_AGENCY_ADMIN')")
    ],
    normalizationContext: ['groups' => ['invoice:read']],
    denormalizationContext: ['groups' => ['invoice:write']]
)]
#[ApiFilter(SearchFilter::class, properties: [
    'invoiceNumber' => 'partial',
    'status' => 'exact',
    'clientId' => 'exact',
    'subscriptionId' => 'exact'
])]
#[ApiFilter(OrderFilter::class, properties: ['createdAt' => 'DESC', 'dueDate' => 'ASC', 'invoiceNumber' => 'ASC'])]
#[ApiFilter(DateFilter::class, properties: ['createdAt', 'dueDate', 'paidAt', 'issuedAt'])]
#[ApiFilter(RangeFilter::class, properties: ['amount', 'taxAmount', 'totalAmount'])]
class Invoice
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    #[ApiProperty(identifier: true)]
    #[Groups(['invoice:read'])]
    private string $id;

    #[ORM\Column(name: 'tenant_id', type: 'uuid', nullable: true)]
    private ?string $tenantId = null;

    #[ORM\Column(name: 'client_id', type: 'uuid')]
    #[Groups(['invoice:read', 'invoice:write'])]
    private string $clientId;

    #[ORM\Column(name: 'subscription_id', type: 'uuid', nullable: true)]
    #[Groups(['invoice:read', 'invoice:write'])]
    private ?string $subscriptionId = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank]
    #[Groups(['invoice:read', 'invoice:write'])]
    private string $invoiceNumber;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank]
    #[Groups(['invoice:read', 'invoice:write'])]
    private string $description;

    #[ORM\Column(type: 'string', length: 50)]
    #[Assert\Choice(['draft', 'sent', 'paid', 'overdue', 'cancelled', 'refunded'])]
    #[Groups(['invoice:read', 'invoice:write'])]
    private string $status = 'draft';

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    #[Groups(['invoice:read', 'invoice:write'])]
    private string $amount;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2, options: ['default' => 0])]
    #[Groups(['invoice:read', 'invoice:write'])]
    private string $taxAmount = '0.00';

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    #[Groups(['invoice:read', 'invoice:write'])]
    private string $totalAmount;

    #[ORM\Column(type: 'string', length: 3)]
    #[Groups(['invoice:read', 'invoice:write'])]
    private string $currency = 'USD';

    #[ORM\Column(name: 'issued_at', type: 'datetime_immutable')]
    #[Groups(['invoice:read', 'invoice:write'])]
    private \DateTimeImmutable $issuedAt;

    #[ORM\Column(name: 'due_date', type: 'datetime_immutable')]
    #[Groups(['invoice:read', 'invoice:write'])]
    private \DateTimeImmutable $dueDate;

    #[ORM\Column(name: 'paid_at', type: 'datetime_immutable', nullable: true)]
    #[Groups(['invoice:read', 'invoice:write'])]
    private ?\DateTimeImmutable $paidAt = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['invoice:read', 'invoice:write'])]
    private ?string $stripeInvoiceId = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['invoice:read', 'invoice:write'])]
    private ?string $stripePaymentIntentId = null;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['invoice:read', 'invoice:write'])]
    private ?string $notes = null;

    #[ORM\Column(type: 'jsonb', nullable: true)]
    #[Groups(['invoice:read', 'invoice:write'])]
    private ?array $lineItems = [];

    #[ORM\Column(type: 'jsonb', nullable: true)]
    #[Groups(['invoice:read', 'invoice:write'])]
    private ?array $taxDetails = [];

    #[ORM\Column(type: 'jsonb', nullable: true)]
    #[Groups(['invoice:read', 'invoice:write'])]
    private ?array $metadata = [];

    #[ORM\Column(name: 'created_at', type: 'datetime_immutable')]
    #[Groups(['invoice:read'])]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(name: 'updated_at', type: 'datetime_immutable')]
    #[Groups(['invoice:read'])]
    private \DateTimeImmutable $updatedAt;

    public function __construct()
    {
        $this->id = Uuid::v4()->toRfc4122();
        $now = new \DateTimeImmutable();
        $this->createdAt = $now;
        $this->updatedAt = $now;
        $this->issuedAt = $now;
        $this->dueDate = $now->modify('+30 days');
        $this->lineItems = [];
        $this->taxDetails = [];
        $this->metadata = [];
    }

    #[ORM\PreUpdate]
    public function setUpdatedAt(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function calculateTotalAmount(): void
    {
        $this->totalAmount = $this->amount + $this->taxAmount;
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

    public function getSubscriptionId(): ?string
    {
        return $this->subscriptionId;
    }

    public function setSubscriptionId(?string $subscriptionId): self
    {
        $this->subscriptionId = $subscriptionId;
        return $this;
    }

    public function getInvoiceNumber(): string
    {
        return $this->invoiceNumber;
    }

    public function setInvoiceNumber(string $invoiceNumber): self
    {
        $this->invoiceNumber = $invoiceNumber;
        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
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

    public function getTaxAmount(): string
    {
        return $this->taxAmount;
    }

    public function setTaxAmount(string $taxAmount): self
    {
        $this->taxAmount = $taxAmount;
        return $this;
    }

    public function getTotalAmount(): string
    {
        return $this->totalAmount;
    }

    public function setTotalAmount(string $totalAmount): self
    {
        $this->totalAmount = $totalAmount;
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

    public function getIssuedAt(): \DateTimeImmutable
    {
        return $this->issuedAt;
    }

    public function setIssuedAt(\DateTimeImmutable $issuedAt): self
    {
        $this->issuedAt = $issuedAt;
        return $this;
    }

    public function getDueDate(): \DateTimeImmutable
    {
        return $this->dueDate;
    }

    public function setDueDate(\DateTimeImmutable $dueDate): self
    {
        $this->dueDate = $dueDate;
        return $this;
    }

    public function getPaidAt(): ?\DateTimeImmutable
    {
        return $this->paidAt;
    }

    public function setPaidAt(?\DateTimeImmutable $paidAt): self
    {
        $this->paidAt = $paidAt;
        return $this;
    }

    public function getStripeInvoiceId(): ?string
    {
        return $this->stripeInvoiceId;
    }

    public function setStripeInvoiceId(?string $stripeInvoiceId): self
    {
        $this->stripeInvoiceId = $stripeInvoiceId;
        return $this;
    }

    public function getStripePaymentIntentId(): ?string
    {
        return $this->stripePaymentIntentId;
    }

    public function setStripePaymentIntentId(?string $stripePaymentIntentId): self
    {
        $this->stripePaymentIntentId = $stripePaymentIntentId;
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

    public function getLineItems(): array
    {
        return $this->lineItems;
    }

    public function setLineItems(array $lineItems): self
    {
        $this->lineItems = $lineItems;
        return $this;
    }

    public function getTaxDetails(): array
    {
        return $this->taxDetails;
    }

    public function setTaxDetails(array $taxDetails): self
    {
        $this->taxDetails = $taxDetails;
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
