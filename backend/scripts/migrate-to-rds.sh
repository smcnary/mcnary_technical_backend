#!/bin/bash

# Database Migration Script for RDS
# This script migrates data from local PostgreSQL to AWS RDS

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
BACKUP_DIR="$PROJECT_ROOT/backups"
TIMESTAMP=$(date +%Y%m%d_%H%M%S)

# Default configuration
LOCAL_DB_HOST="${LOCAL_DB_HOST:-127.0.0.1}"
LOCAL_DB_PORT="${LOCAL_DB_PORT:-5432}"
LOCAL_DB_NAME="${LOCAL_DB_NAME:-tulsa_seo}"
LOCAL_DB_USER="${LOCAL_DB_USER:-smcnary}"
LOCAL_DB_PASSWORD="${LOCAL_DB_PASSWORD:-TulsaSeo122}"

RDS_ENDPOINT="${RDS_ENDPOINT:-}"
RDS_PORT="${RDS_PORT:-5432}"
RDS_DB_NAME="${RDS_DB_NAME:-counselrank_prod}"
RDS_USER="${RDS_USER:-counselrank_admin}"
RDS_PASSWORD="${RDS_PASSWORD:-}"

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

# Function to check prerequisites
check_prerequisites() {
    print_status "Checking prerequisites..."
    
    # Check if pg_dump is available
    if ! command -v pg_dump &> /dev/null; then
        print_error "pg_dump is not installed. Please install PostgreSQL client tools."
        exit 1
    fi
    
    # Check if psql is available
    if ! command -v psql &> /dev/null; then
        print_error "psql is not installed. Please install PostgreSQL client tools."
        exit 1
    fi
    
    # Check if RDS configuration is provided
    if [ -z "$RDS_ENDPOINT" ] || [ -z "$RDS_PASSWORD" ]; then
        print_error "RDS configuration is missing. Please provide RDS_ENDPOINT and RDS_PASSWORD."
        print_status "Example: RDS_ENDPOINT=your-rds-endpoint.region.rds.amazonaws.com RDS_PASSWORD=your-password $0"
        exit 1
    fi
    
    print_success "Prerequisites check passed"
}

# Function to create backup directory
create_backup_dir() {
    print_status "Creating backup directory..."
    
    mkdir -p "$BACKUP_DIR"
    print_success "Backup directory created: $BACKUP_DIR"
}

# Function to backup local database
backup_local_database() {
    print_status "Creating backup of local database..."
    
    BACKUP_FILE="$BACKUP_DIR/local_db_backup_$TIMESTAMP.sql"
    
    # Set password for pg_dump
    export PGPASSWORD="$LOCAL_DB_PASSWORD"
    
    # Create backup
    pg_dump \
        --host="$LOCAL_DB_HOST" \
        --port="$LOCAL_DB_PORT" \
        --username="$LOCAL_DB_USER" \
        --dbname="$LOCAL_DB_NAME" \
        --verbose \
        --no-password \
        --format=plain \
        --file="$BACKUP_FILE"
    
    if [ $? -eq 0 ]; then
        print_success "Local database backup created: $BACKUP_FILE"
    else
        print_error "Failed to create local database backup"
        exit 1
    fi
    
    # Unset password
    unset PGPASSWORD
}

# Function to test RDS connection
test_rds_connection() {
    print_status "Testing RDS connection..."
    
    # Set password for psql
    export PGPASSWORD="$RDS_PASSWORD"
    
    # Test connection
    psql \
        --host="$RDS_ENDPOINT" \
        --port="$RDS_PORT" \
        --username="$RDS_USER" \
        --dbname="$RDS_DB_NAME" \
        --command="SELECT version();" \
        --quiet
    
    if [ $? -eq 0 ]; then
        print_success "RDS connection test passed"
    else
        print_error "Failed to connect to RDS database"
        exit 1
    fi
    
    # Unset password
    unset PGPASSWORD
}

# Function to run migrations on RDS
run_rds_migrations() {
    print_status "Running migrations on RDS database..."
    
    cd "$PROJECT_ROOT"
    
    # Update DATABASE_URL for RDS
    export DATABASE_URL="postgresql://$RDS_USER:$RDS_PASSWORD@$RDS_ENDPOINT:$RDS_PORT/$RDS_DB_NAME?serverVersion=16&charset=utf8"
    
    # Run migrations
    php bin/console doctrine:migrations:migrate --no-interaction
    
    if [ $? -eq 0 ]; then
        print_success "RDS migrations completed successfully"
    else
        print_error "Failed to run RDS migrations"
        exit 1
    fi
}

# Function to restore data to RDS
restore_data_to_rds() {
    print_status "Restoring data to RDS database..."
    
    BACKUP_FILE="$BACKUP_DIR/local_db_backup_$TIMESTAMP.sql"
    
    if [ ! -f "$BACKUP_FILE" ]; then
        print_error "Backup file not found: $BACKUP_FILE"
        exit 1
    fi
    
    # Set password for psql
    export PGPASSWORD="$RDS_PASSWORD"
    
    # Restore data (excluding schema creation commands)
    grep -v "^CREATE\|^DROP\|^ALTER\|^COMMENT ON\|^REVOKE\|^GRANT" "$BACKUP_FILE" | \
    psql \
        --host="$RDS_ENDPOINT" \
        --port="$RDS_PORT" \
        --username="$RDS_USER" \
        --dbname="$RDS_DB_NAME" \
        --quiet
    
    if [ $? -eq 0 ]; then
        print_success "Data restored to RDS successfully"
    else
        print_error "Failed to restore data to RDS"
        exit 1
    fi
    
    # Unset password
    unset PGPASSWORD
}

# Function to verify migration
verify_migration() {
    print_status "Verifying migration..."
    
    # Set password for psql
    export PGPASSWORD="$RDS_PASSWORD"
    
    # Check table counts
    print_status "Checking table counts..."
    
    TABLES=("user" "client" "audit_intake" "audit_run" "audit_finding" "leads" "campaigns" "keywords")
    
    for table in "${TABLES[@]}"; do
        COUNT=$(psql \
            --host="$RDS_ENDPOINT" \
            --port="$RDS_PORT" \
            --username="$RDS_USER" \
            --dbname="$RDS_DB_NAME" \
            --command="SELECT COUNT(*) FROM $table;" \
            --quiet \
            --tuples-only)
        
        if [ $? -eq 0 ]; then
            print_success "Table $table: $COUNT records"
        else
            print_warning "Could not count records in table $table"
        fi
    done
    
    # Unset password
    unset PGPASSWORD
}

# Function to update environment configuration
update_environment_config() {
    print_status "Updating environment configuration..."
    
    ENV_FILE="$PROJECT_ROOT/.env.local"
    
    # Create .env.local if it doesn't exist
    if [ ! -f "$ENV_FILE" ]; then
        cp "$PROJECT_ROOT/env-template.txt" "$ENV_FILE"
    fi
    
    # Backup existing .env.local
    cp "$ENV_FILE" "$ENV_FILE.backup.$TIMESTAMP"
    
    # Update DATABASE_URL
    NEW_DATABASE_URL="postgresql://$RDS_USER:$RDS_PASSWORD@$RDS_ENDPOINT:$RDS_PORT/$RDS_DB_NAME?serverVersion=16&charset=utf8"
    
    if grep -q "^DATABASE_URL=" "$ENV_FILE"; then
        sed -i.bak "s|^DATABASE_URL=.*|DATABASE_URL=\"$NEW_DATABASE_URL\"|" "$ENV_FILE"
    else
        echo "DATABASE_URL=\"$NEW_DATABASE_URL\"" >> "$ENV_FILE"
    fi
    
    # Update APP_ENV to prod
    if grep -q "^APP_ENV=" "$ENV_FILE"; then
        sed -i.bak "s|^APP_ENV=.*|APP_ENV=prod|" "$ENV_FILE"
    else
        echo "APP_ENV=prod" >> "$ENV_FILE"
    fi
    
    # Update APP_DEBUG to false
    if grep -q "^APP_DEBUG=" "$ENV_FILE"; then
        sed -i.bak "s|^APP_DEBUG=.*|APP_DEBUG=false|" "$ENV_FILE"
    else
        echo "APP_DEBUG=false" >> "$ENV_FILE"
    fi
    
    # Clean up backup files
    rm -f "$ENV_FILE.bak"
    
    print_success "Environment configuration updated"
}

# Function to display migration summary
display_migration_summary() {
    print_success "Migration to RDS completed successfully!"
    echo ""
    echo "=== Migration Summary ==="
    echo "Source Database: $LOCAL_DB_HOST:$LOCAL_DB_PORT/$LOCAL_DB_NAME"
    echo "Target Database: $RDS_ENDPOINT:$RDS_PORT/$RDS_DB_NAME"
    echo "Backup File: $BACKUP_DIR/local_db_backup_$TIMESTAMP.sql"
    echo "Environment File: $PROJECT_ROOT/.env.local"
    echo ""
    echo "=== Next Steps ==="
    echo "1. Test your application with the new RDS database"
    echo "2. Update your deployment scripts to use RDS"
    echo "3. Set up monitoring and alerts for RDS"
    echo "4. Configure automated backups"
    echo "5. Update your application's connection pooling settings"
    echo ""
    echo "=== Security Recommendations ==="
    echo "- Rotate database passwords regularly"
    echo "- Use SSL/TLS for database connections"
    echo "- Restrict security group access to your application servers"
    echo "- Enable VPC endpoints for secure access"
    echo "- Set up CloudTrail for audit logging"
}

# Main execution
main() {
    print_status "Starting migration from local PostgreSQL to AWS RDS..."
    echo ""
    
    # Check prerequisites
    check_prerequisites
    
    # Create backup directory
    create_backup_dir
    
    # Backup local database
    backup_local_database
    
    # Test RDS connection
    test_rds_connection
    
    # Run migrations on RDS
    run_rds_migrations
    
    # Restore data to RDS
    restore_data_to_rds
    
    # Verify migration
    verify_migration
    
    # Update environment configuration
    update_environment_config
    
    # Display migration summary
    display_migration_summary
}

# Parse command line arguments
while [[ $# -gt 0 ]]; do
    case $1 in
        --local-host)
            LOCAL_DB_HOST="$2"
            shift 2
            ;;
        --local-port)
            LOCAL_DB_PORT="$2"
            shift 2
            ;;
        --local-db)
            LOCAL_DB_NAME="$2"
            shift 2
            ;;
        --local-user)
            LOCAL_DB_USER="$2"
            shift 2
            ;;
        --local-password)
            LOCAL_DB_PASSWORD="$2"
            shift 2
            ;;
        --rds-endpoint)
            RDS_ENDPOINT="$2"
            shift 2
            ;;
        --rds-port)
            RDS_PORT="$2"
            shift 2
            ;;
        --rds-db)
            RDS_DB_NAME="$2"
            shift 2
            ;;
        --rds-user)
            RDS_USER="$2"
            shift 2
            ;;
        --rds-password)
            RDS_PASSWORD="$2"
            shift 2
            ;;
        --help)
            echo "Usage: $0 [OPTIONS]"
            echo ""
            echo "Options:"
            echo "  --local-host      Local database host (default: 127.0.0.1)"
            echo "  --local-port      Local database port (default: 5432)"
            echo "  --local-db        Local database name (default: tulsa_seo)"
            echo "  --local-user      Local database user (default: smcnary)"
            echo "  --local-password  Local database password (default: TulsaSeo122)"
            echo "  --rds-endpoint   RDS endpoint (required)"
            echo "  --rds-port       RDS port (default: 5432)"
            echo "  --rds-db         RDS database name (default: counselrank_prod)"
            echo "  --rds-user       RDS username (default: counselrank_admin)"
            echo "  --rds-password   RDS password (required)"
            echo "  --help           Show this help message"
            echo ""
            echo "Example:"
            echo "  $0 --rds-endpoint your-rds-endpoint.region.rds.amazonaws.com --rds-password your-password"
            exit 0
            ;;
        *)
            print_error "Unknown option: $1"
            echo "Use --help for usage information"
            exit 1
            ;;
    esac
done

# Run main function
main
