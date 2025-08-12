<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use App\Repository\FaqRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: FaqRepository::class)]
#[ORM\Table(name: 'faqs')]
#[ORM\UniqueConstraint(name: 'uq_faqs_tenant_question', columns: ['tenant_id', 'question'])]
#[ORM\HasLifecycleCallbacks]
#[ApiResource(
    operations: [
        new GetCollection(normalizationContext: ['groups' => ['faq:read']]),
        new Get(normalizationContext: ['groups' => ['faq:read']]),
        new Post(denormalizationContext: ['groups' => ['faq:write']], security: "is_granted('ROLE_ADMIN')"),
        new Put(denormalizationContext: ['groups' => ['faq:write']], security: "is_granted('ROLE_ADMIN')"),
        new Patch(denormalizationContext: ['groups' => ['faq:write']], security: "is_granted('ROLE_ADMIN')"),
        new Delete(security: "is_granted('ROLE_ADMIN')"),
    ],
    paginationEnabled: false
)]
#[ApiFilter(SearchFilter::class, properties: ['isActive' => 'exact', 'question' => 'partial'])]
#[ApiFilter(OrderFilter::class, properties: ['sort' => 'ASC', 'createdAt' => 'DESC'])]
class Faq
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ApiProperty(identifier: true)]
    #[Groups(['faq:read'])]
    private Uuid $id;

    #[ORM\ManyToOne(targetEntity: Tenant::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Tenant $tenant;

    #[ORM\Column(type: Types::STRING)]
    #[Assert\NotBlank]
    #[Groups(['faq:read','faq:write'])]
    private string $question;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank]
    #[Groups(['faq:read','faq:write'])]
    private string $answer;

    #[ORM\Column(name: 'is_active', type: Types::BOOLEAN, options: ['default' => true])]
    #[Groups(['faq:read','faq:write'])]
    private bool $isActive = true;

    #[ORM\Column(type: Types::INTEGER, options: ['default' => 0])]
    #[Groups(['faq:read','faq:write'])]
    private int $sort = 0;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    #[Groups(['faq:read'])]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    #[Groups(['faq:read'])]
    private \DateTimeImmutable $updatedAt;

    public function __construct(Tenant $tenant)
    {
        $this->id = Uuid::v4();
        $this->tenant = $tenant;
        $this->createdAt = new \DateTimeImmutable('now');
        $this->updatedAt = new \DateTimeImmutable('now');
    }

    #[ORM\PreUpdate]
    public function onPreUpdate(): void
    {
        $this->updatedAt = new \DateTimeImmutable('now');
    }

    // getters/setters ...

    public function getId(): Uuid { return $this->id; }

    public function getTenant(): Tenant { return $this->tenant; }
    public function setTenant(Tenant $tenant): self { $this->tenant = $tenant; return $this; }

    public function getQuestion(): string { return $this->question; }
    public function setQuestion(string $question): self { $this->question = $question; return $this; }

    public function getAnswer(): string { return $this->answer; }
    public function setAnswer(string $answer): self { $this->answer = $answer; return $this; }

    public function isActive(): bool { return $this->isActive; }
    public function setIsActive(bool $active): self { $this->isActive = $active; return $this; }

    public function getSort(): int { return $this->sort; }
    public function setSort(int $sort): self { $this->sort = $sort; return $this; }

    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }
    public function setCreatedAt(\DateTimeImmutable $dt): self { $this->createdAt = $dt; return $this; }

    public function getUpdatedAt(): \DateTimeImmutable { return $this->updatedAt; }
    public function setUpdatedAt(\DateTimeImmutable $dt): self { $this->updatedAt = $dt; return $this; }
}
