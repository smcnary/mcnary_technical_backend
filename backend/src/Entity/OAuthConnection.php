<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Delete;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
#[ORM\Table(name: 'oauth_connections', uniqueConstraints: [
    new ORM\UniqueConstraint(name: 'uniq_client_provider', columns: ['client_id','provider'])
])]
#[ORM\HasLifecycleCallbacks]
#[ApiResource(
    operations: [
        new Get(security: "is_granted('ROLE_AGENCY_ADMIN') or is_granted('ROLE_AGENCY_STAFF') or (is_granted('ROLE_CLIENT_ADMIN') and object.getClient().getClientId() == user.getClientId())"),
        new GetCollection(security: "is_granted('ROLE_AGENCY_ADMIN') or is_granted('ROLE_AGENCY_STAFF')"),
        new Post(security: "is_granted('ROLE_AGENCY_ADMIN') or is_granted('ROLE_CLIENT_ADMIN')"),
        new Put(security: "is_granted('ROLE_AGENCY_ADMIN') or (is_granted('ROLE_CLIENT_ADMIN') and object.getClient().getClientId() == user.getClientId())"),
        new Delete(security: "is_granted('ROLE_AGENCY_ADMIN')")
    ]
)]
class OAuthConnection
{
    use Timestamps;

    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private string $id;

    #[ORM\ManyToOne(inversedBy: 'oauthConnections')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Client $client;

    #[ORM\Column]
    private string $provider; // google_gbp, google_sc, google_analytics, stripe

    #[ORM\Column(nullable: true)]
    private ?string $externalAccountId = null;

    #[ORM\Column(type: 'jsonb')]
    private array $scopes = [];

    #[ORM\Column(type: 'string', options: ['default' => 'active'])]
    private string $status = 'active';

    #[ORM\Column(type: 'jsonb', nullable: true)]
    private ?array $metadata = [];

    /** @var Collection<int,OAuthToken> */
    #[ORM\OneToMany(mappedBy: 'connection', targetEntity: OAuthToken::class, cascade: ['persist'], orphanRemoval: true)]
    private Collection $tokens;

    public function __construct(Client $client, string $provider)
    {
        $this->id = Uuid::v4()->toRfc4122();
        $this->client = $client;
        $this->provider = $provider;
        $this->scopes = [];
        $this->tokens = new ArrayCollection();
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

    public function setClient(?Client $client): self
    {
        $this->client = $client;
        return $this;
    }

    public function getProvider(): string
    {
        return $this->provider;
    }

    public function setProvider(string $provider): self
    {
        $this->provider = $provider;
        return $this;
    }

    public function getExternalAccountId(): ?string
    {
        return $this->externalAccountId;
    }

    public function setExternalAccountId(?string $externalAccountId): self
    {
        $this->externalAccountId = $externalAccountId;
        return $this;
    }

    public function getScopes(): array
    {
        return $this->scopes;
    }

    public function setScopes(array $scopes): self
    {
        $this->scopes = $scopes;
        return $this;
    }

    public function hasScope(string $scope): bool
    {
        return in_array($scope, $this->scopes, true);
    }

    public function addScope(string $scope): self
    {
        if (!$this->hasScope($scope)) {
            $this->scopes[] = $scope;
        }
        return $this;
    }

    public function removeScope(string $scope): self
    {
        $this->scopes = array_filter($this->scopes, fn($s) => $s !== $scope);
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

    public function getMetadata(): ?array
    {
        return $this->metadata;
    }

    public function setMetadata(?array $metadata): self
    {
        $this->metadata = $metadata;
        return $this;
    }

    public function getTokens(): Collection
    {
        return $this->tokens;
    }

    public function addToken(OAuthToken $token): self
    {
        if (!$this->tokens->contains($token)) {
            $this->tokens->add($token);
            $token->setConnection($this);
        }
        return $this;
    }

    public function removeToken(OAuthToken $token): self
    {
        if ($this->tokens->removeElement($token)) {
            if ($token->getConnection() === $this) {
                $token->setConnection(null);
            }
        }
        return $this;
    }

    public function getActiveToken(): ?OAuthToken
    {
        foreach ($this->tokens as $token) {
            if ($token->isActive()) {
                return $token;
            }
        }
        return null;
    }

    public function isConnected(): bool
    {
        return $this->status === 'active' && $this->getActiveToken() !== null;
    }
}
