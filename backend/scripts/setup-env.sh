#!/bin/bash

# Environment Setup Script for RDS Integration
# This script helps configure environment variables for different deployment scenarios

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
ENV_FILE="$PROJECT_ROOT/.env.local"

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

# Function to display usage
show_usage() {
    echo "Usage: $0 [OPTIONS]"
    echo ""
    echo "Options:"
    echo "  --local          Setup for local development with Docker"
    echo "  --rds-staging    Setup for local development with RDS staging"
    echo "  --rds-production Setup for production with RDS production"
    echo "  --github-secrets Display GitHub secrets configuration"
    echo "  --test-connection Test database connection"
    echo "  --help           Show this help message"
    echo ""
    echo "Examples:"
    echo "  $0 --local          # Use local Docker database"
    echo "  $0 --rds-staging    # Use RDS staging database"
    echo "  $0 --test-connection # Test current database connection"
}

# Function to setup local environment
setup_local() {
    print_status "Setting up local development environment with Docker..."
    
    # Backup existing .env.local if it exists
    if [ -f "$ENV_FILE" ]; then
        cp "$ENV_FILE" "$ENV_FILE.backup.$(date +%Y%m%d_%H%M%S)"
        print_warning "Backed up existing .env.local"
    fi
    
    # Copy local environment template
    cp "$PROJECT_ROOT/env.dev" "$ENV_FILE"
    
    print_success "Local environment configured"
    print_status "Database: Local Docker PostgreSQL (tulsa_seo)"
    print_status "To start: docker-compose up -d"
}

# Function to setup RDS staging environment
setup_rds_staging() {
    print_status "Setting up RDS staging environment..."
    
    # Backup existing .env.local if it exists
    if [ -f "$ENV_FILE" ]; then
        cp "$ENV_FILE" "$ENV_FILE.backup.$(date +%Y%m%d_%H%M%S)"
        print_warning "Backed up existing .env.local"
    fi
    
    # Copy RDS staging environment template
    cp "$PROJECT_ROOT/env.rds-staging" "$ENV_FILE"
    
    print_success "RDS staging environment configured"
    print_status "Database: RDS Staging (counselrank_staging)"
    print_status "Endpoint: counselrank-staging-db.cstm2wakq0zs.us-east-1.rds.amazonaws.com"
}

# Function to setup RDS production environment
setup_rds_production() {
    print_status "Setting up RDS production environment..."
    
    # Backup existing .env.local if it exists
    if [ -f "$ENV_FILE" ]; then
        cp "$ENV_FILE" "$ENV_FILE.backup.$(date +%Y%m%d_%H%M%S)"
        print_warning "Backed up existing .env.local"
    fi
    
    # Copy RDS production environment template
    cp "$PROJECT_ROOT/env.rds-production" "$ENV_FILE"
    
    print_warning "RDS production environment configured"
    print_status "Database: RDS Production (counselrank_prod)"
    print_warning "Please update the DATABASE_URL with your actual production RDS endpoint and password"
}

# Function to display GitHub secrets configuration
show_github_secrets() {
    print_status "GitHub Secrets Configuration"
    echo ""
    echo "Add these secrets to your GitHub repository:"
    echo "Settings → Secrets and variables → Actions"
    echo ""
    echo "=== AWS Credentials ==="
    echo "AWS_ACCESS_KEY_ID=your-access-key"
    echo "AWS_SECRET_ACCESS_KEY=your-secret-key"
    echo "AWS_REGION=us-east-1"
    echo ""
    echo "=== RDS Staging ==="
    echo "RDS_STAGING_ENDPOINT=counselrank-staging-db.cstm2wakq0zs.us-east-1.rds.amazonaws.com"
    echo "RDS_STAGING_PASSWORD=TulsaSeo122"
    echo "RDS_STAGING_DATABASE_URL=postgresql://counselrank_admin:TulsaSeo122@counselrank-staging-db.cstm2wakq0zs.us-east-1.rds.amazonaws.com:5432/counselrank_staging?serverVersion=16&charset=utf8"
    echo ""
    echo "=== RDS Production ==="
    echo "RDS_PRODUCTION_ENDPOINT=your-production-endpoint.region.rds.amazonaws.com"
    echo "RDS_PRODUCTION_PASSWORD=your-production-password"
    echo "RDS_PRODUCTION_DATABASE_URL=postgresql://counselrank_admin:your-production-password@your-production-endpoint.region.rds.amazonaws.com:5432/counselrank_prod?serverVersion=16&charset=utf8"
    echo ""
    echo "=== Server Deployment (Existing) ==="
    echo "STAGING_HOST=your-staging-server-ip"
    echo "STAGING_USER=your-staging-user"
    echo "STAGING_SSH_KEY=your-staging-ssh-private-key"
    echo "PRODUCTION_HOST=your-production-server-ip"
    echo "PRODUCTION_USER=your-production-user"
    echo "PRODUCTION_SSH_KEY=your-production-ssh-private-key"
}

# Function to test database connection
test_connection() {
    print_status "Testing database connection..."
    
    if [ ! -f "$ENV_FILE" ]; then
        print_error "Environment file not found: $ENV_FILE"
        print_status "Run setup script first: $0 --local or $0 --rds-staging"
        exit 1
    fi
    
    # Extract DATABASE_URL from .env.local
    DATABASE_URL=$(grep "^DATABASE_URL=" "$ENV_FILE" | cut -d'=' -f2- | tr -d '"')
    
    if [ -z "$DATABASE_URL" ]; then
        print_error "DATABASE_URL not found in environment file"
        exit 1
    fi
    
    print_status "Testing connection to: $DATABASE_URL"
    
    # Test connection using Symfony
    cd "$PROJECT_ROOT"
    
    if php bin/console doctrine:query:sql 'SELECT version();' > /dev/null 2>&1; then
        print_success "Database connection successful!"
        
        # Get database info
        DB_INFO=$(php bin/console doctrine:query:sql 'SELECT current_database(), current_user, version();' 2>/dev/null | tail -n +2)
        print_status "Database Info: $DB_INFO"
        
        # Check if migrations are up to date
        MIGRATION_STATUS=$(php bin/console doctrine:migrations:status --no-interaction 2>/dev/null | grep "Executed Migrations" | awk '{print $3}' || echo "Unknown")
        print_status "Migration Status: $MIGRATION_STATUS migrations executed"
        
    else
        print_error "Database connection failed"
        print_status "Check your DATABASE_URL in $ENV_FILE"
        exit 1
    fi
}

# Function to validate environment configuration
validate_config() {
    print_status "Validating environment configuration..."
    
    if [ ! -f "$ENV_FILE" ]; then
        print_error "Environment file not found: $ENV_FILE"
        exit 1
    fi
    
    # Check required variables
    REQUIRED_VARS=("APP_ENV" "APP_DEBUG" "APP_SECRET" "DATABASE_URL")
    
    for var in "${REQUIRED_VARS[@]}"; do
        if ! grep -q "^$var=" "$ENV_FILE"; then
            print_error "Required variable $var not found in $ENV_FILE"
            exit 1
        fi
    done
    
    print_success "Environment configuration validation passed"
    
    # Display current configuration
    echo ""
    echo "=== Current Configuration ==="
    grep -E "^(APP_ENV|APP_DEBUG|DATABASE_URL)=" "$ENV_FILE" | while read line; do
        if [[ "$line" == *"DATABASE_URL"* ]]; then
            # Mask password in DATABASE_URL
            masked_url=$(echo "$line" | sed 's/:[^@]*@/:***@/')
            echo "$masked_url"
        else
            echo "$line"
        fi
    done
}

# Main execution
main() {
    case "${1:-}" in
        --local)
            setup_local
            validate_config
            ;;
        --rds-staging)
            setup_rds_staging
            validate_config
            ;;
        --rds-production)
            setup_rds_production
            validate_config
            ;;
        --github-secrets)
            show_github_secrets
            ;;
        --test-connection)
            test_connection
            ;;
        --validate)
            validate_config
            ;;
        --help)
            show_usage
            ;;
        "")
            print_error "No option specified"
            show_usage
            exit 1
            ;;
        *)
            print_error "Unknown option: $1"
            show_usage
            exit 1
            ;;
    esac
}

# Run main function
main "$@"
