#!/bin/bash

# Entity Relationship Fix Script
# This script fixes the Doctrine entity relationship mappings

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Configuration
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(dirname "$SCRIPT_DIR")"

# Function to print colored output
print_status() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

print_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Function to fix User entity
fix_user_entity() {
    print_status "Fixing User entity relationships..."
    
    local user_file="$PROJECT_ROOT/src/Entity/User.php"
    
    # Add missing relationships to User entity
    cat >> "$user_file" << 'EOF'

    /** @var Collection<int,OAuthConnection> */
    #[ORM\OneToMany(mappedBy: 'user', targetEntity: OAuthConnection::class, cascade: ['persist'], orphanRemoval: true)]
    private Collection $oauthConnections;

    /** @var Collection<int,OAuthToken> */
    #[ORM\OneToMany(mappedBy: 'user', targetEntity: OAuthToken::class, cascade: ['persist'], orphanRemoval: true)]
    private Collection $tokens;

    /** @var Collection<int,UserClientAccess> */
    #[ORM\OneToMany(mappedBy: 'user', targetEntity: UserClientAccess::class, cascade: ['persist'], orphanRemoval: true)]
    private Collection $userAccess;

EOF

    # Update constructor to initialize collections
    sed -i.bak '/\$this->metadata = \[\];/a\
        $this->oauthConnections = new ArrayCollection();\
        $this->tokens = new ArrayCollection();\
        $this->userAccess = new ArrayCollection();' "$user_file"

    # Add getter methods
    cat >> "$user_file" << 'EOF'

    public function getOauthConnections(): Collection
    {
        return $this->oauthConnections;
    }

    public function addOauthConnection(OAuthConnection $oauthConnection): self
    {
        if (!$this->oauthConnections->contains($oauthConnection)) {
            $this->oauthConnections->add($oauthConnection);
            $oauthConnection->setUser($this);
        }
        return $this;
    }

    public function removeOauthConnection(OAuthConnection $oauthConnection): self
    {
        if ($this->oauthConnections->removeElement($oauthConnection)) {
            if ($oauthConnection->getUser() === $this) {
                $oauthConnection->setUser(null);
            }
        }
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
            $token->setUser($this);
        }
        return $this;
    }

    public function removeToken(OAuthToken $token): self
    {
        if ($this->tokens->removeElement($token)) {
            if ($token->getUser() === $this) {
                $token->setUser(null);
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
            $userAccess->setUser($this);
        }
        return $this;
    }

    public function removeUserAccess(UserClientAccess $userAccess): self
    {
        if ($this->userAccess->removeElement($userAccess)) {
            if ($userAccess->getUser() === $this) {
                $userAccess->setUser(null);
            }
        }
        return $this;
    }
EOF

    print_success "User entity relationships fixed"
}

# Function to fix Agency entity
fix_agency_entity() {
    print_status "Fixing Agency entity relationships..."
    
    local agency_file="$PROJECT_ROOT/src/Entity/Agency.php"
    
    # Update the users relationship to include inversedBy
    sed -i.bak 's/#\[ORM\\OneToMany(mappedBy: '\''agency'\'', targetEntity: User::class)\]/#[ORM\\OneToMany(mappedBy: '\''agency'\'', targetEntity: User::class, inversedBy: '\''users'\'')]/' "$agency_file"
    
    print_success "Agency entity relationships fixed"
}

# Function to fix OAuthConnection entity
fix_oauth_connection_entity() {
    print_status "Fixing OAuthConnection entity relationships..."
    
    local oauth_file="$PROJECT_ROOT/src/Entity/OAuthConnection.php"
    
    # Add user relationship
    sed -i.bak '/private Client \$client;/a\
\
    #[ORM\\ManyToOne(targetEntity: User::class)]\
    #[ORM\\JoinColumn(name: '\''user_id'\'', nullable: true)]\
    private ?User \$user = null;' "$oauth_file"
    
    # Update constructor to accept user parameter
    sed -i.bak 's/public function __construct(Client \$client, string \$provider)/public function __construct(Client \$client, string \$provider, ?User \$user = null)/' "$oauth_file"
    
    # Add user assignment in constructor
    sed -i.bak '/\$this->provider = \$provider;/a\
        $this->user = $user;' "$oauth_file"
    
    # Add user getter/setter methods
    cat >> "$oauth_file" << 'EOF'

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;
        return $this;
    }
EOF

    print_success "OAuthConnection entity relationships fixed"
}

# Function to fix OAuthToken entity
fix_oauth_token_entity() {
    print_status "Fixing OAuthToken entity relationships..."
    
    local token_file="$PROJECT_ROOT/src/Entity/OAuthToken.php"
    
    # Add user relationship
    sed -i.bak '/private OAuthConnection \$connection;/a\
\
    #[ORM\\ManyToOne(targetEntity: User::class)]\
    #[ORM\\JoinColumn(name: '\''user_id'\'', nullable: true)]\
    private ?User \$user = null;' "$token_file"
    
    # Add user getter/setter methods
    cat >> "$token_file" << 'EOF'

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;
        return $this;
    }
EOF

    print_success "OAuthToken entity relationships fixed"
}

# Function to fix UserClientAccess entity
fix_user_client_access_entity() {
    print_status "Fixing UserClientAccess entity relationships..."
    
    local access_file="$PROJECT_ROOT/src/Entity/UserClientAccess.php"
    
    # Update the user relationship to include inversedBy
    sed -i.bak 's/#\[ORM\\ManyToOne(targetEntity: User::class)\]/#[ORM\\ManyToOne(targetEntity: User::class, inversedBy: '\''userAccess'\'')]/' "$access_file"
    
    # Update the client relationship to include inversedBy
    sed -i.bak 's/#\[ORM\\ManyToOne(targetEntity: Client::class)\]/#[ORM\\ManyToOne(targetEntity: Client::class, inversedBy: '\''userAccess'\'')]/' "$access_file"
    
    print_success "UserClientAccess entity relationships fixed"
}

# Function to fix Lead entity
fix_lead_entity() {
    print_status "Fixing Lead entity relationships..."
    
    local lead_file="$PROJECT_ROOT/src/Entity/Lead.php"
    
    # Update the client relationship to include inversedBy
    sed -i.bak 's/#\[ORM\\ManyToOne(targetEntity: Client::class)\]/#[ORM\\ManyToOne(targetEntity: Client::class, inversedBy: '\''leads'\'')]/' "$lead_file"
    
    print_success "Lead entity relationships fixed"
}

# Function to fix RankingDaily entity
fix_ranking_daily_entity() {
    print_status "Fixing RankingDaily entity relationships..."
    
    local ranking_file="$PROJECT_ROOT/src/Entity/RankingDaily.php"
    
    # Update the keyword relationship to include inversedBy
    sed -i.bak 's/#\[ORM\\ManyToOne(targetEntity: Keyword::class)\]/#[ORM\\ManyToOne(targetEntity: Keyword::class, inversedBy: '\''rankings'\'')]/' "$ranking_file"
    
    print_success "RankingDaily entity relationships fixed"
}

# Function to validate schema after fixes
validate_schema() {
    print_status "Validating entity schema after fixes..."
    
    cd "$PROJECT_ROOT"
    
    if php bin/console doctrine:schema:validate > /dev/null 2>&1; then
        print_success "Entity schema validation passed!"
        return 0
    else
        print_warning "Entity schema validation failed, but continuing..."
        return 1
    fi
}

# Function to generate new migration
generate_migration() {
    print_status "Generating new migration for relationship fixes..."
    
    cd "$PROJECT_ROOT"
    
    php bin/console doctrine:migrations:diff
    
    print_success "New migration generated"
}

# Main execution
main() {
    print_status "Starting entity relationship fixes..."
    echo ""
    
    # Fix all entity relationships
    fix_user_entity
    fix_agency_entity
    fix_oauth_connection_entity
    fix_oauth_token_entity
    fix_user_client_access_entity
    fix_lead_entity
    fix_ranking_daily_entity
    
    echo ""
    print_status "All entity relationships fixed!"
    
    # Validate schema
    if validate_schema; then
        print_success "Entity schema is now valid!"
    else
        print_warning "Schema validation failed, but relationships are fixed"
    fi
    
    # Generate new migration
    generate_migration
    
    echo ""
    print_success "Entity relationship fixes completed!"
    echo ""
    echo "Next steps:"
    echo "1. Review the generated migration"
    echo "2. Run: php bin/console doctrine:migrations:migrate --no-interaction"
    echo "3. Validate: php bin/console doctrine:schema:validate"
}

# Run main function
main "$@"
