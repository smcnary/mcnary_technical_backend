<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Patch;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity]
#[ORM\Table(name: 'document_templates')]
#[ORM\HasLifecycleCallbacks]
#[ApiResource(
    operations: [
        new GetCollection(
            security: "is_granted('ROLE_AGENCY_ADMIN')",
            normalizationContext: ['groups' => ['template:read']]
        ),
        new Get(
            security: "is_granted('ROLE_AGENCY_ADMIN')",
            normalizationContext: ['groups' => ['template:read', 'template:detail']]
        ),
        new Post(
            security: "is_granted('ROLE_AGENCY_ADMIN')",
            denormalizationContext: ['groups' => ['template:write']]
        ),
        new Put(
            security: "is_granted('ROLE_AGENCY_ADMIN')",
            denormalizationContext: ['groups' => ['template:write']]
        ),
        new Patch(
            security: "is_granted('ROLE_AGENCY_ADMIN')",
            denormalizationContext: ['groups' => ['template:update']]
        ),
        new Delete(
            security: "is_granted('ROLE_AGENCY_ADMIN')"
        )
    ]
)]
class DocumentTemplate
{
    use Timestamps;

    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    #[Groups(['template:read', 'template:detail'])]
    private string $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank]
    #[Groups(['template:read', 'template:detail', 'template:write', 'template:update'])]
    private string $name;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['template:read', 'template:detail', 'template:write', 'template:update'])]
    private ?string $description = null;

    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank]
    #[Groups(['template:read', 'template:detail', 'template:write', 'template:update'])]
    private string $content;

    #[ORM\Column(type: 'string', length: 50)]
    #[Assert\Choice(['contract', 'agreement', 'proposal', 'invoice', 'report', 'other'])]
    #[Groups(['template:read', 'template:detail', 'template:write', 'template:update'])]
    private string $type = 'contract';

    #[ORM\Column(type: 'jsonb', nullable: true)]
    #[Groups(['template:read', 'template:detail', 'template:write', 'template:update'])]
    private ?array $variables = [];

    #[ORM\Column(type: 'jsonb', nullable: true)]
    #[Groups(['template:read', 'template:detail', 'template:write', 'template:update'])]
    private ?array $signatureFields = [];

    #[ORM\Column(type: 'jsonb', nullable: true)]
    #[Groups(['template:read', 'template:detail', 'template:write', 'template:update'])]
    private ?array $metadata = [];

    #[ORM\Column(type: 'boolean', options: ['default' => true])]
    #[Groups(['template:read', 'template:detail', 'template:write', 'template:update'])]
    private bool $isActive = true;

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    #[Groups(['template:read', 'template:detail', 'template:write', 'template:update'])]
    private bool $requiresSignature = true;

    #[ORM\Column(type: 'integer', options: ['default' => 0])]
    #[Groups(['template:read', 'template:detail'])]
    private int $usageCount = 0;

    /** @var Collection<int,Document> */
    #[ORM\OneToMany(mappedBy: 'template', targetEntity: Document::class)]
    #[Groups(['template:detail'])]
    private Collection $documents;

    public function __construct()
    {
        $this->id = Uuid::v4()->toRfc4122();
        $this->documents = new ArrayCollection();
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;
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

    public function getVariables(): ?array
    {
        return $this->variables;
    }

    public function setVariables(?array $variables): self
    {
        $this->variables = $variables;
        return $this;
    }

    public function getSignatureFields(): ?array
    {
        return $this->signatureFields;
    }

    public function setSignatureFields(?array $signatureFields): self
    {
        $this->signatureFields = $signatureFields;
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

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;
        return $this;
    }

    public function isRequiresSignature(): bool
    {
        return $this->requiresSignature;
    }

    public function setRequiresSignature(bool $requiresSignature): self
    {
        $this->requiresSignature = $requiresSignature;
        return $this;
    }

    public function getUsageCount(): int
    {
        return $this->usageCount;
    }

    public function setUsageCount(int $usageCount): self
    {
        $this->usageCount = $usageCount;
        return $this;
    }

    /**
     * @return Collection<int,Document>
     */
    public function getDocuments(): Collection
    {
        return $this->documents;
    }

    public function addDocument(Document $document): self
    {
        if (!$this->documents->contains($document)) {
            $this->documents->add($document);
            $document->setTemplate($this);
            $this->usageCount++;
        }
        return $this;
    }

    public function removeDocument(Document $document): self
    {
        if ($this->documents->removeElement($document)) {
            if ($document->getTemplate() === $this) {
                $document->setTemplate(null);
            }
            if ($this->usageCount > 0) {
                $this->usageCount--;
            }
        }
        return $this;
    }

    // Helper methods
    public function getVariable(string $key, $default = null)
    {
        return $this->variables[$key] ?? $default;
    }

    public function setVariable(string $key, $value): self
    {
        if ($this->variables === null) {
            $this->variables = [];
        }
        $this->variables[$key] = $value;
        return $this;
    }

    public function getMetaValue(string $key, $default = null)
    {
        return $this->metadata[$key] ?? $default;
    }

    public function setMetaValue(string $key, $value): self
    {
        if ($this->metadata === null) {
            $this->metadata = [];
        }
        $this->metadata[$key] = $value;
        return $this;
    }

    public function processContent(array $data = []): string
    {
        $content = $this->content;
        
        // Replace template variables with actual values
        foreach ($this->variables ?? [] as $variable => $defaultValue) {
            $value = $data[$variable] ?? $defaultValue ?? '';
            $content = str_replace("{{$variable}}", $value, $content);
        }
        
        return $content;
    }
}
