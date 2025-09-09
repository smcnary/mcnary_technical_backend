#!/bin/bash

# RDS Database Deployment Script
# This script deploys the PostgreSQL database to AWS RDS

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

# Default RDS configuration
DB_INSTANCE_IDENTIFIER="${DB_INSTANCE_IDENTIFIER:-counselrank-prod-db}"
DB_NAME="${DB_NAME:-counselrank_prod}"
DB_USERNAME="${DB_USERNAME:-counselrank_admin}"
DB_PASSWORD="${DB_PASSWORD:-$(openssl rand -base64 32)}"
DB_INSTANCE_CLASS="${DB_INSTANCE_CLASS:-db.t3.micro}"
DB_ALLOCATED_STORAGE="${DB_ALLOCATED_STORAGE:-20}"
DB_ENGINE_VERSION="${DB_ENGINE_VERSION:-16.1}"
DB_SUBNET_GROUP="${DB_SUBNET_GROUP:-default}"
DB_SECURITY_GROUP="${DB_SECURITY_GROUP:-counselrank-db-sg}"
DB_VPC_ID="${DB_VPC_ID:-default}"
AWS_REGION="${AWS_REGION:-us-east-1}"

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

# Function to check if AWS CLI is installed and configured
check_aws_cli() {
    if ! command -v aws &> /dev/null; then
        print_error "AWS CLI is not installed. Please install it first."
        print_status "Installation guide: https://docs.aws.amazon.com/cli/latest/userguide/getting-started-install.html"
        exit 1
    fi

    if ! aws sts get-caller-identity &> /dev/null; then
        print_error "AWS CLI is not configured. Please run 'aws configure' first."
        exit 1
    fi

    print_success "AWS CLI is installed and configured"
}

# Function to create security group for RDS
create_security_group() {
    print_status "Creating security group for RDS..."
    
    # Check if security group already exists
    if aws ec2 describe-security-groups --group-names "$DB_SECURITY_GROUP" --region "$AWS_REGION" &> /dev/null; then
        print_warning "Security group '$DB_SECURITY_GROUP' already exists"
        return 0
    fi

    # Create security group
    aws ec2 create-security-group \
        --group-name "$DB_SECURITY_GROUP" \
        --description "Security group for CounselRank RDS database" \
        --vpc-id "$DB_VPC_ID" \
        --region "$AWS_REGION" \
        --output table

    # Add inbound rule for PostgreSQL (port 5432)
    aws ec2 authorize-security-group-ingress \
        --group-name "$DB_SECURITY_GROUP" \
        --protocol tcp \
        --port 5432 \
        --cidr 0.0.0.0/0 \
        --region "$AWS_REGION"

    print_success "Security group '$DB_SECURITY_GROUP' created"
}

# Function to create RDS instance
create_rds_instance() {
    print_status "Creating RDS PostgreSQL instance..."
    
    # Check if instance already exists
    if aws rds describe-db-instances --db-instance-identifier "$DB_INSTANCE_IDENTIFIER" --region "$AWS_REGION" &> /dev/null; then
        print_warning "RDS instance '$DB_INSTANCE_IDENTIFIER' already exists"
        return 0
    fi

    # Create RDS instance
    aws rds create-db-instance \
        --db-instance-identifier "$DB_INSTANCE_IDENTIFIER" \
        --db-instance-class "$DB_INSTANCE_CLASS" \
        --engine postgres \
        --engine-version "$DB_ENGINE_VERSION" \
        --master-username "$DB_USERNAME" \
        --master-user-password "$DB_PASSWORD" \
        --allocated-storage "$DB_ALLOCATED_STORAGE" \
        --storage-type gp2 \
        --db-name "$DB_NAME" \
        --vpc-security-group-ids "$DB_SECURITY_GROUP" \
        --db-subnet-group-name "$DB_SUBNET_GROUP" \
        --backup-retention-period 7 \
        --multi-az \
        --storage-encrypted \
        --region "$AWS_REGION" \
        --output table

    print_success "RDS instance '$DB_INSTANCE_IDENTIFIER' creation initiated"
}

# Function to wait for RDS instance to be available
wait_for_rds_instance() {
    print_status "Waiting for RDS instance to be available..."
    
    aws rds wait db-instance-available \
        --db-instance-identifier "$DB_INSTANCE_IDENTIFIER" \
        --region "$AWS_REGION"

    print_success "RDS instance is now available"
}

# Function to get RDS endpoint
get_rds_endpoint() {
    print_status "Getting RDS endpoint..."
    
    ENDPOINT=$(aws rds describe-db-instances \
        --db-instance-identifier "$DB_INSTANCE_IDENTIFIER" \
        --region "$AWS_REGION" \
        --query 'DBInstances[0].Endpoint.Address' \
        --output text)
    
    PORT=$(aws rds describe-db-instances \
        --db-instance-identifier "$DB_INSTANCE_IDENTIFIER" \
        --region "$AWS_REGION" \
        --query 'DBInstances[0].Endpoint.Port' \
        --output text)
    
    print_success "RDS endpoint: $ENDPOINT:$PORT"
    echo "ENDPOINT=$ENDPOINT" > "$PROJECT_ROOT/.rds-config"
    echo "PORT=$PORT" >> "$PROJECT_ROOT/.rds-config"
}

# Function to update environment file
update_env_file() {
    print_status "Updating environment file with RDS configuration..."
    
    # Create .env.local if it doesn't exist
    if [ ! -f "$ENV_FILE" ]; then
        cp "$PROJECT_ROOT/env-template.txt" "$ENV_FILE"
    fi

    # Update DATABASE_URL in .env.local
    DATABASE_URL="postgresql://$DB_USERNAME:$DB_PASSWORD@$ENDPOINT:$PORT/$DB_NAME?serverVersion=16&charset=utf8"
    
    # Backup existing .env.local
    cp "$ENV_FILE" "$ENV_FILE.backup.$(date +%Y%m%d_%H%M%S)"
    
    # Update DATABASE_URL
    if grep -q "^DATABASE_URL=" "$ENV_FILE"; then
        sed -i.bak "s|^DATABASE_URL=.*|DATABASE_URL=\"$DATABASE_URL\"|" "$ENV_FILE"
    else
        echo "DATABASE_URL=\"$DATABASE_URL\"" >> "$ENV_FILE"
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
    
    print_success "Environment file updated with RDS configuration"
}

# Function to run database migrations
run_migrations() {
    print_status "Running database migrations..."
    
    cd "$PROJECT_ROOT"
    
    # Test database connection
    php bin/console doctrine:query:sql 'SELECT version()' || {
        print_error "Failed to connect to RDS database"
        exit 1
    }
    
    # Run migrations
    php bin/console doctrine:migrations:migrate --no-interaction || {
        print_error "Failed to run database migrations"
        exit 1
    }
    
    print_success "Database migrations completed successfully"
}

# Function to create database backup
create_backup() {
    print_status "Creating initial database backup..."
    
    BACKUP_IDENTIFIER="${DB_INSTANCE_IDENTIFIER}-initial-backup-$(date +%Y%m%d-%H%M%S)"
    
    aws rds create-db-snapshot \
        --db-instance-identifier "$DB_INSTANCE_IDENTIFIER" \
        --db-snapshot-identifier "$BACKUP_IDENTIFIER" \
        --region "$AWS_REGION" \
        --output table
    
    print_success "Initial backup created: $BACKUP_IDENTIFIER"
}

# Function to display connection information
display_connection_info() {
    print_success "RDS deployment completed successfully!"
    echo ""
    echo "=== Connection Information ==="
    echo "Instance ID: $DB_INSTANCE_IDENTIFIER"
    echo "Endpoint: $ENDPOINT:$PORT"
    echo "Database: $DB_NAME"
    echo "Username: $DB_USERNAME"
    echo "Password: $DB_PASSWORD"
    echo ""
    echo "=== Environment Configuration ==="
    echo "Environment file: $ENV_FILE"
    echo "DATABASE_URL: postgresql://$DB_USERNAME:$DB_PASSWORD@$ENDPOINT:$PORT/$DB_NAME?serverVersion=16&charset=utf8"
    echo ""
    echo "=== Next Steps ==="
    echo "1. Update your application's environment variables"
    echo "2. Test the database connection: php bin/console doctrine:query:sql 'SELECT version()'"
    echo "3. Monitor the RDS instance in AWS Console"
    echo "4. Set up automated backups and monitoring"
    echo ""
    echo "=== Security Notes ==="
    echo "- Change the default password in production"
    echo "- Restrict security group access to your application servers only"
    echo "- Enable SSL/TLS for database connections"
    echo "- Set up VPC peering or VPN for secure access"
}

# Main execution
main() {
    print_status "Starting RDS deployment for CounselRank database..."
    echo ""
    
    # Check prerequisites
    check_aws_cli
    
    # Create security group
    create_security_group
    
    # Create RDS instance
    create_rds_instance
    
    # Wait for instance to be available
    wait_for_rds_instance
    
    # Get endpoint information
    get_rds_endpoint
    
    # Update environment file
    update_env_file
    
    # Run migrations
    run_migrations
    
    # Create initial backup
    create_backup
    
    # Display connection information
    display_connection_info
}

# Parse command line arguments
while [[ $# -gt 0 ]]; do
    case $1 in
        --instance-id)
            DB_INSTANCE_IDENTIFIER="$2"
            shift 2
            ;;
        --db-name)
            DB_NAME="$2"
            shift 2
            ;;
        --username)
            DB_USERNAME="$2"
            shift 2
            ;;
        --password)
            DB_PASSWORD="$2"
            shift 2
            ;;
        --instance-class)
            DB_INSTANCE_CLASS="$2"
            shift 2
            ;;
        --storage)
            DB_ALLOCATED_STORAGE="$2"
            shift 2
            ;;
        --region)
            AWS_REGION="$2"
            shift 2
            ;;
        --help)
            echo "Usage: $0 [OPTIONS]"
            echo ""
            echo "Options:"
            echo "  --instance-id     RDS instance identifier (default: counselrank-prod-db)"
            echo "  --db-name         Database name (default: counselrank_prod)"
            echo "  --username        Master username (default: counselrank_admin)"
            echo "  --password        Master password (default: auto-generated)"
            echo "  --instance-class  DB instance class (default: db.t3.micro)"
            echo "  --storage         Allocated storage in GB (default: 20)"
            echo "  --region          AWS region (default: us-east-1)"
            echo "  --help            Show this help message"
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
