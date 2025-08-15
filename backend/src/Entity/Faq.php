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
#[ORM\HasLifecycleCallbacks]
#[ApiResource(
    operations: [
        new GetCollection(normalizationContext: ['groups' => ['faq:read']]),
        new Get(normalizationContext: ['groups' => ['faq:read']]),
        new Post(denormalizationContext: ['groups' => ['faq:write']], security: "is_granted('ROLE_AGENCY_ADMIN')"),
        new Put(denormalizationContext: ['groups' => ['faq:write']], security: "is_granted('ROLE_AGENCY_ADMIN')"),
        new Patch(denormalizationContext: ['groups' => ['faq:write']], security: "is_granted('ROLE_AGENCY_ADMIN')"),
        new Delete(security: "is_granted('ROLE_AGENCY_ADMIN')"),
    ],
    paginationEnabled: false
)]
#[ApiFilter(SearchFilter::class, properties: ['isActive' => 'exact', 'question' => 'partial'])]
#[ApiFilter(OrderFilter::class, properties: ['orderIndex' => 'ASC', 'createdAt' => 'DESC'])]
class Faq
{
    use Timestamps;

    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    #[ApiProperty(identifier: true)]
    #[Groups(['faq:read'])]
    private string $id;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank]
    #[Groups(['faq:read','faq:write'])]
    private string $question;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank]
    #[Groups(['faq:read','faq:write'])]
    private string $answerMd;

    #[ORM\Column(name: 'is_active', type: Types::BOOLEAN, options: ['default' => true])]
    #[Groups(['faq:read','faq:write'])]
    private bool $isActive = true;

    #[ORM\Column(options: ['default' => 0])]
    #[Groups(['faq:read','faq:write'])]
    private int $orderIndex = 0;

    public function __construct(string $question, string $answer)
    {
        $this->id = Uuid::v4()->toRfc4122();
        $this->question = $question;
        $this->answerMd = $answer;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getQuestion(): string
    {
        return $this->question;
    }

    public function setQuestion(string $question): self
    {
        $this->question = $question;
        return $this;
    }

    public function getAnswerMd(): string
    {
        return $this->answerMd;
    }

    public function setAnswerMd(string $answerMd): self
    {
        $this->answerMd = $answerMd;
        return $this;
    }

    // Legacy getter for backward compatibility
    public function getAnswer(): string
    {
        return $this->answerMd;
    }

    public function setAnswer(string $answer): self
    {
        $this->answerMd = $answer;
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

    public function getOrderIndex(): int
    {
        return $this->orderIndex;
    }

    public function setOrderIndex(int $orderIndex): self
    {
        $this->orderIndex = $orderIndex;
        return $this;
    }

    // Legacy getter for backward compatibility
    public function getSort(): int
    {
        return $this->orderIndex;
    }

    public function setSort(int $sort): self
    {
        $this->orderIndex = $sort;
        return $this;
    }

    // Legacy getter for backward compatibility
    public function getTenant(): ?object
    {
        // This method is kept for backward compatibility but should not be used
        return null;
    }

    public function setTenant(object $tenant): self
    {
        // This method is kept for backward compatibility but should not be used
        return $this;
    }
}
