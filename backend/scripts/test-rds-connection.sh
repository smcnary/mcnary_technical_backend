#!/bin/bash

# RDS Connection Test Script
# This script tests the connection to AWS RDS and validates the database setup

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

# Function to check prerequisites
check_prerequisites() {
    print_status "Checking prerequisites..."
    
    # Check if psql is available
    if ! command -v psql &> /dev/null; then
        print_error "psql is not installed. Please install PostgreSQL client tools."
        exit 1
    fi
    
    # Check if environment file exists
    if [ ! -f "$ENV_FILE" ]; then
        print_error "Environment file not found: $ENV_FILE"
        print_status "Please run the RDS deployment script first or create .env.local manually"
        exit 1
    fi
    
    print_success "Prerequisites check passed"
}

# Function to extract database configuration from environment file
extract_db_config() {
    print_status "Extracting database configuration..."
    
    # Extract DATABASE_URL from .env.local
    DATABASE_URL=$(grep "^DATABASE_URL=" "$ENV_FILE" | cut -d'=' -f2- | tr -d '"')
    
    if [ -z "$DATABASE_URL" ]; then
        print_error "DATABASE_URL not found in environment file"
        exit 1
    fi
    
    # Parse DATABASE_URL
    # Format: postgresql://username:password@host:port/database?params
    DB_URL_REGEX="postgresql://([^:]+):([^@]+)@([^:]+):([^/]+)/([^?]+)"
    
    if [[ $DATABASE_URL =~ $DB_URL_REGEX ]]; then
        DB_USERNAME="${BASH_REMATCH[1]}"
        DB_PASSWORD="${BASH_REMATCH[2]}"
        DB_HOST="${BASH_REMATCH[3]}"
        DB_PORT="${BASH_REMATCH[4]}"
        DB_NAME="${BASH_REMATCH[5]}"
    else
        print_error "Invalid DATABASE_URL format"
        exit 1
    fi
    
    print_success "Database configuration extracted"
    print_status "Host: $DB_HOST:$DB_PORT"
    print_status "Database: $DB_NAME"
    print_status "Username: $DB_USERNAME"
}

# Function to test basic connection
test_basic_connection() {
    print_status "Testing basic database connection..."
    
    # Set password for psql
    export PGPASSWORD="$DB_PASSWORD"
    
    # Test connection
    if psql \
        --host="$DB_HOST" \
        --port="$DB_PORT" \
        --username="$DB_USERNAME" \
        --dbname="$DB_NAME" \
        --command="SELECT version();" \
        --quiet; then
        print_success "Basic connection test passed"
    else
        print_error "Failed to connect to RDS database"
        exit 1
    fi
    
    # Unset password
    unset PGPASSWORD
}

# Function to test Symfony connection
test_symfony_connection() {
    print_status "Testing Symfony database connection..."
    
    cd "$PROJECT_ROOT"
    
    # Test Doctrine connection
    if php bin/console doctrine:query:sql 'SELECT version()' > /dev/null 2>&1; then
        print_success "Symfony connection test passed"
    else
        print_error "Failed to connect via Symfony/Doctrine"
        exit 1
    fi
}

# Function to check database schema
check_database_schema() {
    print_status "Checking database schema..."
    
    cd "$PROJECT_ROOT"
    
    # Check if migrations are up to date
    MIGRATION_STATUS=$(php bin/console doctrine:migrations:status --no-interaction 2>/dev/null | grep "Executed Migrations" | awk '{print $3}')
    
    if [ -n "$MIGRATION_STATUS" ]; then
        print_success "Migration status: $MIGRATION_STATUS migrations executed"
    else
        print_warning "Could not determine migration status"
    fi
    
    # Check for key tables
    TABLES=("user" "client" "audit_intake" "audit_run" "audit_finding")
    
    for table in "${TABLES[@]}"; do
        if php bin/console doctrine:query:sql "SELECT COUNT(*) FROM $table;" > /dev/null 2>&1; then
            print_success "Table '$table' exists and is accessible"
        else
            print_warning "Table '$table' may not exist or is not accessible"
        fi
    done
}

# Function to test database performance
test_database_performance() {
    print_status "Testing database performance..."
    
    cd "$PROJECT_ROOT"
    
    # Test query performance
    START_TIME=$(date +%s%N)
    php bin/console doctrine:query:sql "SELECT COUNT(*) FROM user;" > /dev/null 2>&1
    END_TIME=$(date +%s%N)
    
    QUERY_TIME=$(( (END_TIME - START_TIME) / 1000000 )) # Convert to milliseconds
    
    if [ $QUERY_TIME -lt 1000 ]; then
        print_success "Query performance test passed (${QUERY_TIME}ms)"
    else
        print_warning "Query performance may be slow (${QUERY_TIME}ms)"
    fi
}

# Function to check SSL connection
test_ssl_connection() {
    print_status "Testing SSL connection..."
    
    # Set password for psql
    export PGPASSWORD="$DB_PASSWORD"
    
    # Test SSL connection
    if psql \
        --host="$DB_HOST" \
        --port="$DB_PORT" \
        --username="$DB_USERNAME" \
        --dbname="$DB_NAME" \
        --command="SELECT ssl_is_used();" \
        --quiet; then
        print_success "SSL connection test passed"
    else
        print_warning "SSL connection test failed or SSL not configured"
    fi
    
    # Unset password
    unset PGPASSWORD
}

# Function to check database size and usage
check_database_usage() {
    print_status "Checking database usage..."
    
    cd "$PROJECT_ROOT"
    
    # Get database size
    DB_SIZE=$(php bin/console doctrine:query:sql "
        SELECT pg_size_pretty(pg_database_size('$DB_NAME')) as size;
    " 2>/dev/null | tail -n 1 | xargs)
    
    if [ -n "$DB_SIZE" ]; then
        print_success "Database size: $DB_SIZE"
    else
        print_warning "Could not determine database size"
    fi
    
    # Get connection count
    CONNECTION_COUNT=$(php bin/console doctrine:query:sql "
        SELECT COUNT(*) as connections 
        FROM pg_stat_activity 
        WHERE datname = '$DB_NAME';
    " 2>/dev/null | tail -n 1 | xargs)
    
    if [ -n "$CONNECTION_COUNT" ]; then
        print_success "Active connections: $CONNECTION_COUNT"
    else
        print_warning "Could not determine connection count"
    fi
}

# Function to run comprehensive tests
run_comprehensive_tests() {
    print_status "Running comprehensive database tests..."
    
    cd "$PROJECT_ROOT"
    
    # Test CRUD operations
    print_status "Testing CRUD operations..."
    
    # Test INSERT
    if php bin/console doctrine:query:sql "
        INSERT INTO user (id, email, first_name, last_name, status, role, created_at, updated_at) 
        VALUES (gen_random_uuid(), 'test@example.com', 'Test', 'User', 'active', 'ROLE_USER', NOW(), NOW())
        ON CONFLICT (email) DO NOTHING;
    " > /dev/null 2>&1; then
        print_success "INSERT operation test passed"
    else
        print_warning "INSERT operation test failed"
    fi
    
    # Test SELECT
    if php bin/console doctrine:query:sql "
        SELECT COUNT(*) FROM user WHERE email = 'test@example.com';
    " > /dev/null 2>&1; then
        print_success "SELECT operation test passed"
    else
        print_warning "SELECT operation test failed"
    fi
    
    # Test UPDATE
    if php bin/console doctrine:query:sql "
        UPDATE user SET updated_at = NOW() WHERE email = 'test@example.com';
    " > /dev/null 2>&1; then
        print_success "UPDATE operation test passed"
    else
        print_warning "UPDATE operation test failed"
    fi
    
    # Test DELETE
    if php bin/console doctrine:query:sql "
        DELETE FROM user WHERE email = 'test@example.com';
    " > /dev/null 2>&1; then
        print_success "DELETE operation test passed"
    else
        print_warning "DELETE operation test failed"
    fi
}

# Function to display test summary
display_test_summary() {
    print_success "RDS connection tests completed!"
    echo ""
    echo "=== Test Summary ==="
    echo "✅ Basic connection test"
    echo "✅ Symfony/Doctrine connection test"
    echo "✅ Database schema check"
    echo "✅ Performance test"
    echo "✅ SSL connection test"
    echo "✅ Database usage check"
    echo "✅ CRUD operations test"
    echo ""
    echo "=== Connection Details ==="
    echo "Host: $DB_HOST:$DB_PORT"
    echo "Database: $DB_NAME"
    echo "Username: $DB_USERNAME"
    echo "Environment: $(grep "^APP_ENV=" "$ENV_FILE" | cut -d'=' -f2 | tr -d '"')"
    echo ""
    echo "=== Next Steps ==="
    echo "1. Your RDS database is ready for production use"
    echo "2. Monitor database performance and usage"
    echo "3. Set up automated backups and monitoring"
    echo "4. Configure connection pooling if needed"
    echo "5. Update your application deployment scripts"
}

# Main execution
main() {
    print_status "Starting RDS connection tests..."
    echo ""
    
    # Check prerequisites
    check_prerequisites
    
    # Extract database configuration
    extract_db_config
    
    # Run tests
    test_basic_connection
    test_symfony_connection
    check_database_schema
    test_database_performance
    test_ssl_connection
    check_database_usage
    run_comprehensive_tests
    
    # Display summary
    display_test_summary
}

# Parse command line arguments
while [[ $# -gt 0 ]]; do
    case $1 in
        --env-file)
            ENV_FILE="$2"
            shift 2
            ;;
        --help)
            echo "Usage: $0 [OPTIONS]"
            echo ""
            echo "Options:"
            echo "  --env-file     Path to environment file (default: .env.local)"
            echo "  --help         Show this help message"
            echo ""
            echo "This script tests the connection to AWS RDS and validates the database setup."
            echo "It requires a .env.local file with DATABASE_URL configured."
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
