<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use App\Repository\LeadRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: LeadRepository::class)]
#[ORM\Table(name: 'leads')]
#[ORM\HasLifecycleCallbacks]
#[ApiResource(
    operations: [
        new Post(
            normalizationContext: ['groups' => ['lead:read']],
            denormalizationContext: ['groups' => ['lead:write']],
            security: "is_granted('PUBLIC_ACCESS')",
            validationContext: ['groups' => ['Default']]
        ),
        new Get(
            normalizationContext: ['groups' => ['lead:admin:read']],
            security: "is_granted('ROLE_AGENCY_ADMIN') or is_granted('ROLE_AGENCY_STAFF') or (is_granted('ROLE_CLIENT_ADMIN') and object.getClient().getClientId() == user.getClientId())"
        ),
        new GetCollection(
            normalizationContext: ['groups' => ['lead:admin:read']],
            security: "is_granted('ROLE_AGENCY_ADMIN') or is_granted('ROLE_AGENCY_STAFF')"
        ),
    ],
    paginationItemsPerPage: 25
)]
#[ApiFilter(SearchFilter::class, properties: [
    'status' => 'exact',
    'city' => 'partial',
    'state' => 'exact',
    'firm' => 'partial',
    'email' => 'partial',
])]
#[ApiFilter(DateFilter::class, properties: ['createdAt'])]
#[ApiFilter(OrderFilter::class, properties: ['createdAt' => 'DESC'], arguments: ['orderParameterName' => 'order'])]
class Lead
{
    use Timestamps;

    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    #[Groups(['lead:read', 'lead:admin:read'])]
    #[ApiProperty(identifier: true)]
    private string $id;

    #[ORM\ManyToOne(inversedBy: 'leads')]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?Client $client = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true)]
    private ?LeadSource $source = null;

    #[ORM\Column(type: Types::STRING)]
    #[Assert\NotBlank]
    #[Groups(['lead:read','lead:write','lead:admin:read'])]
    private string $fullName;

    #[ORM\Column(type: Types::STRING)]
    #[Assert\NotBlank]
    #[Assert\Email]
    #[Groups(['lead:read','lead:write','lead:admin:read'])]
    private string $email;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    #[Groups(['lead:read','lead:write','lead:admin:read'])]
    private ?string $phone = null;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    #[Groups(['lead:read','lead:write','lead:admin:read'])]
    private ?string $firm = null;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    #[Assert\Url(protocols: ['http', 'https'])]
    #[Groups(['lead:read','lead:write','lead:admin:read'])]
    private ?string $website = null;

    #[ORM\Column(type: Types::ARRAY, options: ['default' => '{}'])]
    #[Groups(['lead:read','lead:write','lead:admin:read'])]
    private array $practiceAreas = [];

    #[ORM\Column(type: Types::STRING, nullable: true)]
    #[Groups(['lead:read','lead:write','lead:admin:read'])]
    private ?string $city = null;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    #[Groups(['lead:read','lead:write','lead:admin:read'])]
    private ?string $state = null;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    #[Groups(['lead:read','lead:write','lead:admin:read'])]
    private ?string $zipCode = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['lead:read','lead:write','lead:admin:read'])]
    private ?string $message = null;

    #[ORM\Column(type: Types::STRING, length: 16, options: ['default' => 'new'])]
    #[Groups(['lead:read','lead:admin:read'])]
    private string $status = 'new';

    #[ORM\Column(type: Types::JSON, options: ['default' => '{}'])]
    #[Groups(['lead:read','lead:write','lead:admin:read'])]
    private array $utmJson = [];

    #[ORM\Column(options: ['default' => false])]
    #[Groups(['lead:admin:read'])]
    private bool $isTest = false;

    /** @var Collection<int,LeadEvent> */
    #[ORM\OneToMany(mappedBy: 'lead', targetEntity: LeadEvent::class, cascade: ['persist'], orphanRemoval: true)]
    private Collection $events;

    public function __construct()
    {
        $this->id = Uuid::v4()->toRfc4122();
        $this->events = new ArrayCollection();
        $this->practiceAreas = [];
        $this->utmJson = [];
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

    public function getSource(): ?LeadSource
    {
        return $this->source;
    }

    public function setSource(?LeadSource $source): self
    {
        $this->source = $source;
        return $this;
    }

    public function getFullName(): string
    {
        return $this->fullName;
    }

    public function setFullName(string $fullName): self
    {
        $this->fullName = $fullName;
        return $this;
    }

    // Legacy getter for backward compatibility
    public function getName(): string
    {
        return $this->fullName;
    }

    public function setName(string $name): self
    {
        $this->fullName = $name;
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

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;
        return $this;
    }

    public function getFirm(): ?string
    {
        return $this->firm;
    }

    public function setFirm(?string $firm): self
    {
        $this->firm = $firm;
        return $this;
    }

    public function getWebsite(): ?string
    {
        return $this->website;
    }

    public function setWebsite(?string $website): self
    {
        $this->website = $website;
        return $this;
    }

    public function getPracticeAreas(): array
    {
        return $this->practiceAreas;
    }

    public function setPracticeAreas(array $practiceAreas): self
    {
        $this->practiceAreas = $practiceAreas;
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

    public function getZipCode(): ?string
    {
        return $this->zipCode;
    }

    public function setZipCode(?string $zipCode): self
    {
        $this->zipCode = $zipCode;
        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(?string $message): self
    {
        $this->message = $message;
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

    public function getUtmJson(): array
    {
        return $this->utmJson;
    }

    public function setUtmJson(array $utmJson): self
    {
        $this->utmJson = $utmJson;
        return $this;
    }

    public function isTest(): bool
    {
        return $this->isTest;
    }

    public function setIsTest(bool $isTest): self
    {
        $this->isTest = $isTest;
        return $this;
    }

    public function getEvents(): Collection
    {
        return $this->events;
    }

    public function addEvent(LeadEvent $event): self
    {
        if (!$this->events->contains($event)) {
            $this->events->add($event);
            $event->setLead($this);
        }
        return $this;
    }

    public function removeEvent(LeadEvent $event): self
    {
        if ($this->events->removeElement($event)) {
            if ($event->getLead() === $this) {
                $event->setLead(null);
            }
        }
        return $this;
    }

    // Legacy getter for backward compatibility
    public function getClientId(): ?string
    {
        return $this->client?->getId();
    }

    public function setClientId(?string $clientId): self
    {
        // This method is kept for backward compatibility but should not be used
        // Use setClient() instead
        return $this;
    }
}
