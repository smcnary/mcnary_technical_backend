<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
#[ORM\Table(name: 'credentials')]
#[ORM\HasLifecycleCallbacks]
#[ApiResource(
    operations: [
        new Get(security: "is_granted('ROLE_USER')"),
        new GetCollection(security: "is_granted('ROLE_USER')"),
        new Post(security: "is_granted('ROLE_ANALYST')"),
        new Put(security: "is_granted('ROLE_ANALYST')")
    ],
    normalizationContext: ['groups' => ['credential:read']],
    denormalizationContext: ['groups' => ['credential:write']]
)]
class Credential
{
    public const TYPE_GSC = 'gsc';
    public const TYPE_PSI = 'psi';
    public const TYPE_AHREFS = 'ahrefs';
    public const TYPE_SEMRUSH = 'semrush';
    public const TYPE_MAJESTIC = 'majestic';

    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    #[Groups(['credential:read'])]
    private string $id;

    #[ORM\ManyToOne(targetEntity: Tenant::class, inversedBy: 'credentials')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['credential:read'])]
    private Tenant $tenant;

    #[ORM\Column(type: 'string', length: 50)]
    #[Assert\Choice([self::TYPE_GSC, self::TYPE_PSI, self::TYPE_AHREFS, self::TYPE_SEMRUSH, self::TYPE_MAJESTIC])]
    #[Groups(['credential:read', 'credential:write'])]
    private string $type;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank]
    #[Groups(['credential:read', 'credential:write'])]
    private string $label;

    #[ORM\Column(type: 'json')]
    #[Groups(['credential:write'])]
    private array $encryptedPayload = [];

    #[ORM\Column(name: 'created_at', type: 'datetime_immutable')]
    #[Groups(['credential:read'])]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(name: 'updated_at', type: 'datetime_immutable')]
    #[Groups(['credential:read'])]
    private \DateTimeImmutable $updatedAt;

    public function __construct()
    {
        $this->id = Uuid::v4()->toRfc4122();
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    #[ORM\PreUpdate]
    public function setUpdatedAt(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getTenant(): Tenant
    {
        return $this->tenant;
    }

    public function setTenant(Tenant $tenant): self
    {
        $this->tenant = $tenant;
        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;
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

    public function getEncryptedPayload(): array
    {
        return $this->encryptedPayload;
    }

    public function setEncryptedPayload(array $encryptedPayload): self
    {
        $this->encryptedPayload = $encryptedPayload;
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
