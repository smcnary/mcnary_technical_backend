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
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\Table(name: 'clients')]
#[ORM\HasLifecycleCallbacks]
#[ApiResource(
    operations: [
        new Get(security: "is_granted('ROLE_AGENCY_ADMIN') or is_granted('ROLE_AGENCY_STAFF') or (is_granted('ROLE_CLIENT_ADMIN') and object.getClientId() == user.getClientId())"),
        new GetCollection(security: "is_granted('ROLE_AGENCY_ADMIN') or is_granted('ROLE_AGENCY_STAFF')"),
        new Post(security: "is_granted('ROLE_AGENCY_ADMIN')"),
        new Put(security: "is_granted('ROLE_AGENCY_ADMIN') or (is_granted('ROLE_CLIENT_ADMIN') and object.getClientId() == user.getClientId())"),
        new Delete(security: "is_granted('ROLE_AGENCY_ADMIN')")
    ]
)]
class Client
{
    use Timestamps;

    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private string $id;

    #[ORM\ManyToOne(inversedBy: 'clients')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Agency $agency;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank]
    private string $name;

    #[ORM\Column(type: 'string', length: 255, nullable: true, unique: true)]
    private ?string $slug = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $websiteUrl = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $phone = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $email = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $address = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $city = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $state = null;

    #[ORM\Column(type: 'string', length: 10, nullable: true)]
    private ?string $postalCode = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $country = null;

    #[ORM\Column(type: 'string', options: ['default' => 'law'])]
    private string $industry = 'law';

    #[ORM\Column(type: 'string', options: ['default' => 'active'])]
    private string $status = 'active';

    #[ORM\Column(type: 'jsonb', nullable: true)]
    private ?array $metadata = [];

    #[ORM\Column(type: 'jsonb', nullable: true)]
    private ?array $googleBusinessProfile = [];

    #[ORM\Column(type: 'jsonb', nullable: true)]
    private ?array $googleSearchConsole = [];

    #[ORM\Column(type: 'jsonb', nullable: true)]
    private ?array $googleAnalytics = [];

    /** @var Collection<int,ClientLocation> */
    #[ORM\OneToMany(mappedBy: 'client', targetEntity: ClientLocation::class, cascade: ['persist'], orphanRemoval: true)]
    private Collection $locations;

    /** @var Collection<int,UserClientAccess> */
    #[ORM\OneToMany(mappedBy: 'client', targetEntity: UserClientAccess::class, cascade: ['persist'], orphanRemoval: true)]
    private Collection $userAccess;

    /** @var Collection<int,Lead> */
    #[ORM\OneToMany(mappedBy: 'client', targetEntity: Lead::class)]
    private Collection $leads;

    /** @var Collection<int,Campaign> */
    #[ORM\OneToMany(mappedBy: 'client', targetEntity: Campaign::class)]
    private Collection $campaigns;

    /** @var Collection<int,Keyword> */
    #[ORM\OneToMany(mappedBy: 'client', targetEntity: Keyword::class)]
    private Collection $keywords;

    // Temporarily commented out - missing proper mappedBy relationship
    // /** @var Collection<int,Backlink> */
    // #[ORM\OneToMany(mappedBy: 'client', targetEntity: Backlink::class)]
    // private Collection $backlinks;

    // Temporarily commented out - missing proper mappedBy relationship
    // /** @var Collection<int,Citation> */
    // #[ORM\OneToMany(mappedBy: 'client', targetEntity: Citation::class)]
    // private Collection $citations;

    // Temporarily commented out - missing proper mappedBy relationship
    // /** @var Collection<int,Review> */
    // #[ORM\OneToMany(mappedBy: 'client', targetEntity: Review::class)]
    // private Collection $reviews;

    // Temporarily commented out - missing proper mappedBy relationship
    // /** @var Collection<int,ContentItem> */
    // #[ORM\OneToMany(mappedBy: 'client', targetEntity: ContentItem::class)]
    // private Collection $contentItems;

    // Temporarily commented out - missing proper mappedBy relationship
    // /** @var Collection<int,AuditRun> */
    // #[ORM\OneToMany(mappedBy: 'client', targetEntity: AuditRun::class)]
    // private Collection $auditRuns;

    // Temporarily commented out - missing proper mappedBy relationship
    // /** @var Collection<int,Recommendation> */
    // #[ORM\OneToMany(mappedBy: 'client', targetEntity: Recommendation::class)]
    // private Collection $recommendations;

    /** @var Collection<int,OAuthConnection> */
    #[ORM\OneToMany(mappedBy: 'client', targetEntity: OAuthConnection::class, cascade: ['persist'], orphanRemoval: true)]
    private Collection $oauthConnections;

    // Temporarily commented out - missing proper mappedBy relationship
    // /** @var Collection<int,Subscription> */
    // #[ORM\OneToMany(mappedBy: 'client', targetEntity: Subscription::class)]
    // private Collection $subscriptions;

    public function __construct(Agency $agency, string $name)
    {
        $this->id = Uuid::v4()->toRfc4122();
        $this->agency = $agency;
        $this->name = $name;
        $this->locations = new ArrayCollection();
        $this->userAccess = new ArrayCollection();
        $this->leads = new ArrayCollection();
        $this->campaigns = new ArrayCollection();
        $this->keywords = new ArrayCollection();
        // $this->backlinks = new ArrayCollection();
        // $this->citations = new ArrayCollection();
        // $this->reviews = new ArrayCollection();
        // $this->contentItems = new ArrayCollection();
        // $this->auditRuns = new ArrayCollection();
        // $this->recommendations = new ArrayCollection();
        $this->oauthConnections = new ArrayCollection();
        // $this->subscriptions = new ArrayCollection();
        $this->metadata = [];
        $this->googleBusinessProfile = [];
        $this->googleSearchConsole = [];
        $this->googleAnalytics = [];
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getAgency(): Agency
    {
        return $this->agency;
    }

    public function setAgency(Agency $agency): self
    {
        $this->agency = $agency;
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

    public function getWebsiteUrl(): ?string
    {
        return $this->websiteUrl;
    }

    public function setWebsiteUrl(?string $websiteUrl): self
    {
        $this->websiteUrl = $websiteUrl;
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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): self
    {
        $this->address = $address;
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

    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    public function setPostalCode(?string $postalCode): self
    {
        $this->postalCode = $postalCode;
        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): self
    {
        $this->country = $country;
        return $this;
    }

    public function getIndustry(): string
    {
        return $this->industry;
    }

    public function setIndustry(string $industry): self
    {
        $this->industry = $industry;
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

    public function getGoogleBusinessProfile(): ?array
    {
        return $this->googleBusinessProfile;
    }

    public function setGoogleBusinessProfile(?array $googleBusinessProfile): self
    {
        $this->googleBusinessProfile = $googleBusinessProfile;
        return $this;
    }

    public function getGoogleSearchConsole(): ?array
    {
        return $this->googleSearchConsole;
    }

    public function setGoogleSearchConsole(?array $googleSearchConsole): self
    {
        $this->googleSearchConsole = $googleSearchConsole;
        return $this;
    }

    public function getGoogleAnalytics(): ?array
    {
        return $this->googleAnalytics;
    }

    public function setGoogleAnalytics(?array $googleAnalytics): self
    {
        $this->googleAnalytics = $googleAnalytics;
        return $this;
    }

    public function getLocations(): Collection
    {
        return $this->locations;
    }

    public function addLocation(ClientLocation $location): self
    {
        if (!$this->locations->contains($location)) {
            $this->locations->add($location);
            $location->setClient($this);
        }
        return $this;
    }

    public function removeLocation(ClientLocation $location): self
    {
        if ($this->locations->removeElement($location)) {
            if ($location->getClient() === $this) {
                $location->setClient(null);
            }
        }
        return $this;
    }

    public function getUserAccess(): Collection
    {
        return $this->userAccess;
    }

    public function addUserAccess(UserClientAccess $userAccess): self
    {
        if (!$this->userAccess->contains($userAccess)) {
            $this->userAccess->add($userAccess);
            $userAccess->setClient($this);
        }
        return $this;
    }

    public function removeUserAccess(UserClientAccess $userAccess): self
    {
        if ($this->userAccess->removeElement($userAccess)) {
            if ($userAccess->getClient() === $this) {
                $userAccess->setClient(null);
            }
        }
        return $this;
    }

    public function getLeads(): Collection
    {
        return $this->leads;
    }

    public function addLead(Lead $lead): self
    {
        if (!$this->leads->contains($lead)) {
            $this->leads->add($lead);
            $lead->setClient($this);
        }
        return $this;
    }

    public function removeLead(Lead $lead): self
    {
        if ($this->leads->removeElement($lead)) {
            if ($lead->getClient() === $this) {
                $lead->setClient(null);
            }
        }
        return $this;
    }

    public function getCampaigns(): Collection
    {
        return $this->campaigns;
    }

    public function addCampaign(Campaign $campaign): self
    {
        if (!$this->campaigns->contains($campaign)) {
            $this->campaigns->add($campaign);
            $campaign->setClient($this);
        }
        return $this;
    }

    public function removeCampaign(Campaign $campaign): self
    {
        if ($this->campaigns->removeElement($campaign)) {
            if ($campaign->getClient() === $this) {
                $campaign->setClient(null);
            }
        }
        return $this;
    }

    public function getKeywords(): Collection
    {
        return $this->keywords;
    }

    public function addKeyword(Keyword $keyword): self
    {
        if (!$this->keywords->contains($keyword)) {
            $this->keywords->add($keyword);
            $keyword->setClient($this);
        }
        return $this;
    }

    public function removeKeyword(Keyword $keyword): self
    {
        if ($this->keywords->removeElement($keyword)) {
            if ($keyword->getClient() === $this) {
                $keyword->setClient(null);
            }
        }
        return $this;
    }

    // Temporarily commented out - these associations are invalid
    /*
    public function getBacklinks(): Collection
    {
        return $this->backlinks;
    }

    public function addBacklink(Backlink $backlink): self
    {
        if (!$this->backlinks->contains($backlink)) {
            $this->backlinks->add($backlink);
            $backlink->setClient($this);
        }
        return $this;
    }

    public function removeBacklink(Backlink $backlink): self
    {
        if ($this->backlinks->removeElement($backlink)) {
            if ($backlink->getClient() === $this) {
                $backlink->setClient(null);
            }
        }
        return $this;
    }
    */

    // Temporarily commented out - these associations are invalid
    /*
    public function getCitations(): Collection
    {
        return $this->citations;
    }

    public function addCitation(Citation $citation): self
    {
        if (!$this->citations->contains($citation)) {
            $this->citations->add($citation);
            $citation->setClient($this);
        }
        return $this;
    }

    public function removeCitation(Citation $citation): self
    {
        if ($this->citations->removeElement($citation)) {
            if ($citation->getClient() === $this) {
                $citation->setClient(null);
            }
        }
        return $this;
    }
    */

    public function getReviews(): Collection
    {
        return $this->reviews;
    }

    public function addReview(Review $review): self
    {
        if (!$this->reviews->contains($review)) {
            $this->reviews->add($review);
            $review->setClient($this);
        }
        return $this;
    }

    public function removeReview(Review $review): self
    {
        if ($this->reviews->removeElement($review)) {
            if ($review->getClient() === $this) {
                $review->setClient(null);
            }
        }
        return $this;
    }

    public function getContentItems(): Collection
    {
        return $this->contentItems;
    }

    public function addContentItem(ContentItem $contentItem): self
    {
        if (!$this->contentItems->contains($contentItem)) {
            $this->contentItems->add($contentItem);
            $contentItem->setClient($this);
        }
        return $this;
    }

    public function removeContentItem(ContentItem $contentItem): self
    {
        if ($this->contentItems->removeElement($contentItem)) {
            if ($contentItem->getClient() === $this) {
                $contentItem->setClient(null);
            }
        }
        return $this;
    }

    public function getAuditRuns(): Collection
    {
        return $this->auditRuns;
    }

    public function addAuditRun(AuditRun $auditRun): self
    {
        if (!$this->auditRuns->contains($auditRun)) {
            $this->auditRuns->add($auditRun);
            $auditRun->setClient($this);
        }
        return $this;
    }

    public function removeAuditRun(AuditRun $auditRun): self
    {
        if ($this->auditRuns->removeElement($auditRun)) {
            if ($auditRun->getClient() === $this) {
                $auditRun->setClient(null);
            }
        }
        return $this;
    }

    public function getRecommendations(): Collection
    {
        return $this->recommendations;
    }

    public function addRecommendation(Recommendation $recommendation): self
    {
        if (!$this->recommendations->contains($recommendation)) {
            $this->recommendations->add($recommendation);
            $recommendation->setClient($this);
        }
        return $this;
    }

    public function removeRecommendation(Recommendation $recommendation): self
    {
        if ($this->recommendations->removeElement($recommendation)) {
            if ($recommendation->getClient() === $this) {
                $recommendation->setClient(null);
            }
        }
        return $this;
    }

    public function getOauthConnections(): Collection
    {
        return $this->oauthConnections;
    }

    public function addOauthConnection(OAuthConnection $oauthConnection): self
    {
        if (!$this->oauthConnections->contains($oauthConnection)) {
            $this->oauthConnections->add($oauthConnection);
            $oauthConnection->setClient($this);
        }
        return $this;
    }

    public function removeOauthConnection(OAuthConnection $oauthConnection): self
    {
        if ($this->oauthConnections->removeElement($oauthConnection)) {
            if ($oauthConnection->getClient() === $this) {
                $oauthConnection->setClient(null);
            }
        }
        return $this;
    }

    public function getSubscriptions(): Collection
    {
        return $this->subscriptions;
    }

    public function addSubscription(Subscription $subscription): self
    {
        if (!$this->subscriptions->contains($subscription)) {
            $this->subscriptions->add($subscription);
            $subscription->setClient($this);
        }
        return $this;
    }

    public function removeSubscription(Subscription $subscription): self
    {
        if ($this->subscriptions->removeElement($subscription)) {
            if ($subscription->getClient() === $this) {
                $subscription->setClient(null);
            }
        }
        return $this;
    }

    // Legacy getter for backward compatibility
    public function getWebsite(): ?string
    {
        return $this->websiteUrl;
    }

    public function setWebsite(?string $website): self
    {
        $this->websiteUrl = $website;
        return $this;
    }

    public function getZipCode(): ?string
    {
        return $this->postalCode;
    }

    public function setZipCode(?string $zipCode): self
    {
        $this->postalCode = $zipCode;
        return $this;
    }
}
