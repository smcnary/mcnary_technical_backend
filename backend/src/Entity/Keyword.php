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
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\Table(name: 'keywords', uniqueConstraints: [
    new ORM\UniqueConstraint(name: 'uniq_client_phrase_loc', columns: ['client_id','phrase','location'])
])]
#[ORM\HasLifecycleCallbacks]
#[ApiResource(
    operations: [
        new Get(security: "is_granted('ROLE_AGENCY_ADMIN') or is_granted('ROLE_AGENCY_STAFF') or (is_granted('ROLE_CLIENT_ADMIN') and object.getClient().getClientId() == user.getClientId())"),
        new GetCollection(security: "is_granted('ROLE_AGENCY_ADMIN') or is_granted('ROLE_AGENCY_STAFF') or is_granted('ROLE_CLIENT_ADMIN')"),
        new Post(security: "is_granted('ROLE_AGENCY_ADMIN') or is_granted('ROLE_CLIENT_ADMIN')"),
        new Put(security: "is_granted('ROLE_AGENCY_ADMIN') or (is_granted('ROLE_CLIENT_ADMIN') and object.getClient().getClientId() == user.getClientId())"),
        new Delete(security: "is_granted('ROLE_AGENCY_ADMIN')")
    ],
    normalizationContext: ['groups' => ['keyword:read']],
    denormalizationContext: ['groups' => ['keyword:write']]
)]
#[ApiFilter(SearchFilter::class, properties: [
    'phrase' => 'partial',
    'status' => 'exact',
    'difficulty' => 'exact'
])]
#[ApiFilter(OrderFilter::class, properties: ['createdAt' => 'DESC', 'phrase' => 'ASC', 'difficulty' => 'ASC'])]
#[ApiFilter(DateFilter::class, properties: ['createdAt', 'updatedAt'])]
class Keyword
{
    use Timestamps;

    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    #[ApiProperty(identifier: true)]
    #[Groups(['keyword:read'])]
    private string $id;

    #[ORM\ManyToOne(inversedBy: 'keywords')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Client $client;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank]
    #[Groups(['keyword:read', 'keyword:write'])]
    private string $phrase;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['keyword:read', 'keyword:write'])]
    private ?string $description = null;

    #[ORM\Column(type: 'string', length: 50)]
    #[Assert\Choice(['draft', 'active', 'paused', 'completed'])]
    #[Groups(['keyword:read', 'keyword:write'])]
    private string $status = 'draft';

    #[ORM\Column(type: 'string', length: 20)]
    #[Assert\Choice(['low', 'medium', 'high', 'very_high'])]
    #[Groups(['keyword:read', 'keyword:write'])]
    private string $difficulty = 'medium';

    #[ORM\Column(type: 'integer', nullable: true)]
    #[Groups(['keyword:read', 'keyword:write'])]
    private ?int $searchVolume = null;

    #[ORM\Column(type: 'decimal', precision: 5, scale: 2, nullable: true)]
    #[Groups(['keyword:read', 'keyword:write'])]
    private ?string $cpc = null;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    #[Groups(['keyword:read', 'keyword:write'])]
    private ?string $intent = null; // informational, navigational, transactional

    #[ORM\Column(type: 'jsonb', nullable: true)]
    #[Groups(['keyword:read', 'keyword:write'])]
    private ?array $relatedKeywords = [];

    #[ORM\Column(type: 'jsonb', nullable: true)]
    #[Groups(['keyword:read', 'keyword:write'])]
    private ?array $metadata = [];

    #[ORM\Column(options: ['default' => 'en-US'])]
    private string $locale = 'en-US';

    #[ORM\Column(nullable: true)]
    private ?string $location = null;

    #[ORM\Column(nullable: true)]
    private ?string $targetUrl = null;

    /** @var Collection<int,RankingDaily> */
    #[ORM\OneToMany(mappedBy: 'keyword', targetEntity: RankingDaily::class, cascade: ['persist'], orphanRemoval: true)]
    private Collection $rankings;

    public function __construct(Client $client, string $phrase)
    {
        $this->id = Uuid::v4()->toRfc4122();
        $this->client = $client;
        $this->phrase = $phrase;
        $this->rankings = new ArrayCollection();
        $this->relatedKeywords = [];
        $this->metadata = [];
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getClient(): Client
    {
        return $this->client;
    }

    public function setClient(Client $client): self
    {
        $this->client = $client;
        return $this;
    }

    public function getPhrase(): string
    {
        return $this->phrase;
    }

    public function setPhrase(string $phrase): self
    {
        $this->phrase = $phrase;
        return $this;
    }

    // Legacy getter for backward compatibility
    public function getKeyword(): string
    {
        return $this->phrase;
    }

    public function setKeyword(string $keyword): self
    {
        $this->phrase = $keyword;
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

    public function getDifficulty(): string
    {
        return $this->difficulty;
    }

    public function setDifficulty(string $difficulty): self
    {
        $this->difficulty = $difficulty;
        return $this;
    }

    public function getSearchVolume(): ?int
    {
        return $this->searchVolume;
    }

    public function setSearchVolume(?int $searchVolume): self
    {
        $this->searchVolume = $searchVolume;
        return $this;
    }

    public function getCpc(): ?string
    {
        return $this->cpc;
    }

    public function setCpc(?string $cpc): self
    {
        $this->cpc = $cpc;
        return $this;
    }

    public function getIntent(): ?string
    {
        return $this->intent;
    }

    public function setIntent(?string $intent): self
    {
        $this->intent = $intent;
        return $this;
    }

    public function getRelatedKeywords(): ?array
    {
        return $this->relatedKeywords;
    }

    public function setRelatedKeywords(?array $relatedKeywords): self
    {
        $this->relatedKeywords = $relatedKeywords;
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

    public function getLocale(): string
    {
        return $this->locale;
    }

    public function setLocale(string $locale): self
    {
        $this->locale = $locale;
        return $this;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(?string $location): self
    {
        $this->location = $location;
        return $this;
    }

    public function getTargetUrl(): ?string
    {
        return $this->targetUrl;
    }

    public function setTargetUrl(?string $targetUrl): self
    {
        $this->targetUrl = $targetUrl;
        return $this;
    }

    public function getRankings(): Collection
    {
        return $this->rankings;
    }

    public function addRanking(RankingDaily $ranking): self
    {
        if (!$this->rankings->contains($ranking)) {
            $this->rankings->add($ranking);
            $ranking->setKeyword($this);
        }
        return $this;
    }

    public function removeRanking(RankingDaily $ranking): self
    {
        if ($this->rankings->removeElement($ranking)) {
            if ($ranking->getKeyword() === $this) {
                $ranking->setKeyword(null);
            }
        }
        return $this;
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isPaused(): bool
    {
        return $this->status === 'paused';
    }

    public function getCurrentRanking(): ?RankingDaily
    {
        $latestRanking = null;
        $latestDate = null;
        
        foreach ($this->rankings as $ranking) {
            if ($ranking->getDate() > $latestDate) {
                $latestDate = $ranking->getDate();
                $latestRanking = $ranking;
            }
        }
        
        return $latestRanking;
    }

    // Legacy getters for backward compatibility
    public function getClientId(): string
    {
        return $this->client->getId();
    }

    public function setClientId(string $clientId): self
    {
        // This method is kept for backward compatibility but should not be used
        // Use setClient() instead
        return $this;
    }

    public function getCampaignId(): ?string
    {
        // This method is kept for backward compatibility but should not be used
        // Campaign relationship should be implemented separately if needed
        return null;
    }

    public function setCampaignId(?string $campaignId): self
    {
        // This method is kept for backward compatibility but should not be used
        // Campaign relationship should be implemented separately if needed
        return $this;
    }
}
