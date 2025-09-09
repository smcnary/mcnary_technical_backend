#!/bin/bash

# RDS Deployment Monitoring Script
# This script helps monitor RDS deployment status and troubleshoot issues

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

# Default RDS instances
STAGING_INSTANCE="counselrank-staging-db"
PRODUCTION_INSTANCE="counselrank-prod-db"
AWS_REGION="us-east-1"

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

# Function to check RDS instance status
check_rds_status() {
    local instance_id="$1"
    local environment="$2"
    
    print_status "Checking $environment RDS instance: $instance_id"
    
    # Get instance details
    local status=$(aws rds describe-db-instances \
        --db-instance-identifier "$instance_id" \
        --region "$AWS_REGION" \
        --query 'DBInstances[0].DBInstanceStatus' \
        --output text 2>/dev/null || echo "NOT_FOUND")
    
    if [ "$status" = "NOT_FOUND" ]; then
        print_error "RDS instance '$instance_id' not found"
        return 1
    fi
    
    # Get additional details
    local endpoint=$(aws rds describe-db-instances \
        --db-instance-identifier "$instance_id" \
        --region "$AWS_REGION" \
        --query 'DBInstances[0].Endpoint.Address' \
        --output text)
    
    local publicly_accessible=$(aws rds describe-db-instances \
        --db-instance-identifier "$instance_id" \
        --region "$AWS_REGION" \
        --query 'DBInstances[0].PubliclyAccessible' \
        --output text)
    
    local db_name=$(aws rds describe-db-instances \
        --db-instance-identifier "$instance_id" \
        --region "$AWS_REGION" \
        --query 'DBInstances[0].DBName' \
        --output text)
    
    local instance_class=$(aws rds describe-db-instances \
        --db-instance-identifier "$instance_id" \
        --region "$AWS_REGION" \
        --query 'DBInstances[0].DBInstanceClass' \
        --output text)
    
    # Display status
    echo ""
    echo "=== $environment RDS Status ==="
    echo "Instance ID: $instance_id"
    echo "Status: $status"
    echo "Endpoint: $endpoint"
    echo "Database: $db_name"
    echo "Instance Class: $instance_class"
    echo "Publicly Accessible: $publicly_accessible"
    
    # Status interpretation
    case "$status" in
        "available")
            print_success "RDS instance is available and ready"
            ;;
        "creating")
            print_warning "RDS instance is being created (this can take 10-15 minutes)"
            ;;
        "backing-up")
            print_warning "RDS instance is backing up"
            ;;
        "modifying")
            print_warning "RDS instance is being modified"
            ;;
        "rebooting")
            print_warning "RDS instance is rebooting"
            ;;
        "deleting")
            print_error "RDS instance is being deleted"
            ;;
        *)
            print_warning "RDS instance status: $status"
            ;;
    esac
    
    echo ""
}

# Function to check security groups
check_security_groups() {
    local instance_id="$1"
    local environment="$2"
    
    print_status "Checking security groups for $environment RDS instance"
    
    # Get security group IDs
    local sg_ids=$(aws rds describe-db-instances \
        --db-instance-identifier "$instance_id" \
        --region "$AWS_REGION" \
        --query 'DBInstances[0].VpcSecurityGroups[].VpcSecurityGroupId' \
        --output text)
    
    for sg_id in $sg_ids; do
        echo ""
        echo "=== Security Group: $sg_id ==="
        
        # Get security group details
        aws ec2 describe-security-groups \
            --group-ids "$sg_id" \
            --region "$AWS_REGION" \
            --query 'SecurityGroups[0].[GroupName,Description,IpPermissions]' \
            --output table
        
        # Check if port 5432 is open
        local port_open=$(aws ec2 describe-security-groups \
            --group-ids "$sg_id" \
            --region "$AWS_REGION" \
            --query 'SecurityGroups[0].IpPermissions[?FromPort==`5432`]' \
            --output text)
        
        if [ -n "$port_open" ]; then
            print_success "Port 5432 (PostgreSQL) is open"
        else
            print_error "Port 5432 (PostgreSQL) is not open"
        fi
    done
}

# Function to check CloudWatch logs
check_cloudwatch_logs() {
    local instance_id="$1"
    local environment="$2"
    
    print_status "Checking CloudWatch logs for $environment RDS instance"
    
    # List log groups
    local log_groups=$(aws logs describe-log-groups \
        --log-group-name-prefix "/aws/rds/instance/$instance_id" \
        --region "$AWS_REGION" \
        --query 'logGroups[].logGroupName' \
        --output text 2>/dev/null || echo "")
    
    if [ -z "$log_groups" ]; then
        print_warning "No CloudWatch log groups found for $instance_id"
        return 0
    fi
    
    for log_group in $log_groups; do
        echo ""
        echo "=== Log Group: $log_group ==="
        
        # Get recent log streams
        local log_streams=$(aws logs describe-log-streams \
            --log-group-name "$log_group" \
            --region "$AWS_REGION" \
            --order-by LastEventTime \
            --descending \
            --max-items 5 \
            --query 'logStreams[].logStreamName' \
            --output text)
        
        if [ -n "$log_streams" ]; then
            print_success "Found log streams: $log_streams"
            
            # Get recent log events from the latest stream
            local latest_stream=$(echo "$log_streams" | awk '{print $1}')
            echo ""
            echo "=== Recent Log Events from $latest_stream ==="
            
            aws logs get-log-events \
                --log-group-name "$log_group" \
                --log-stream-name "$latest_stream" \
                --region "$AWS_REGION" \
                --start-time $(date -d '1 hour ago' +%s)000 \
                --query 'events[].message' \
                --output text | head -10
        else
            print_warning "No log streams found in $log_group"
        fi
    done
}

# Function to check GitHub Actions status
check_github_actions() {
    print_status "GitHub Actions RDS Deployment Status"
    echo ""
    echo "=== GitHub Actions Monitoring ==="
    echo "1. Go to: https://github.com/smcnary/mcnary_technical_backend/actions"
    echo "2. Look for 'RDS Database Deployment' workflow"
    echo "3. Check the latest run status"
    echo ""
    echo "=== Workflow Triggers ==="
    echo "- Push to 'develop' branch → Deploys to staging RDS"
    echo "- Push to 'main' branch → Deploys to production RDS"
    echo "- Manual workflow dispatch → Choose environment"
    echo ""
    echo "=== Common Issues ==="
    echo "- ❌ AWS credentials not configured → Check GitHub secrets"
    echo "- ❌ RDS instance creation failed → Check AWS permissions"
    echo "- ❌ Connection timeout → Check security groups"
    echo "- ❌ Migration failed → Check database schema"
}

# Function to test database connection
test_database_connection() {
    local instance_id="$1"
    local environment="$2"
    
    print_status "Testing database connection for $environment"
    
    # Get endpoint
    local endpoint=$(aws rds describe-db-instances \
        --db-instance-identifier "$instance_id" \
        --region "$AWS_REGION" \
        --query 'DBInstances[0].Endpoint.Address' \
        --output text)
    
    local db_name=$(aws rds describe-db-instances \
        --db-instance-identifier "$instance_id" \
        --region "$AWS_REGION" \
        --query 'DBInstances[0].DBName' \
        --output text)
    
    print_status "Testing connection to: $endpoint:5432/$db_name"
    
    # Test connection using Symfony (if available)
    if [ -f "$PROJECT_ROOT/backend/bin/console" ]; then
        cd "$PROJECT_ROOT/backend"
        
        # Set environment variables for testing
        export DATABASE_URL="postgresql://counselrank_admin:TulsaSeo122@$endpoint:5432/$db_name?serverVersion=16&charset=utf8"
        
        if php bin/console doctrine:query:sql 'SELECT version();' > /dev/null 2>&1; then
            print_success "Database connection successful!"
            
            # Get database info
            local db_info=$(php bin/console doctrine:query:sql 'SELECT current_database(), current_user, version();' 2>/dev/null | tail -n +2)
            print_status "Database Info: $db_info"
            
        else
            print_error "Database connection failed"
            print_status "Check security groups and network configuration"
        fi
    else
        print_warning "Symfony console not found, skipping connection test"
    fi
}

# Function to display comprehensive status
show_comprehensive_status() {
    print_status "Comprehensive RDS Deployment Status"
    echo ""
    
    # Check staging instance
    if aws rds describe-db-instances --db-instance-identifier "$STAGING_INSTANCE" --region "$AWS_REGION" >/dev/null 2>&1; then
        check_rds_status "$STAGING_INSTANCE" "Staging"
        check_security_groups "$STAGING_INSTANCE" "Staging"
        test_database_connection "$STAGING_INSTANCE" "Staging"
    else
        print_warning "Staging RDS instance '$STAGING_INSTANCE' not found"
    fi
    
    echo ""
    echo "=========================================="
    echo ""
    
    # Check production instance
    if aws rds describe-db-instances --db-instance-identifier "$PRODUCTION_INSTANCE" --region "$AWS_REGION" >/dev/null 2>&1; then
        check_rds_status "$PRODUCTION_INSTANCE" "Production"
        check_security_groups "$PRODUCTION_INSTANCE" "Production"
        test_database_connection "$PRODUCTION_INSTANCE" "Production"
    else
        print_warning "Production RDS instance '$PRODUCTION_INSTANCE' not found"
    fi
    
    echo ""
    echo "=========================================="
    echo ""
    
    check_github_actions
}

# Function to display usage
show_usage() {
    echo "Usage: $0 [OPTIONS]"
    echo ""
    echo "Options:"
    echo "  --staging         Check staging RDS instance only"
    echo "  --production      Check production RDS instance only"
    echo "  --github-actions  Show GitHub Actions monitoring info"
    echo "  --security-groups Check security groups for all instances"
    echo "  --cloudwatch      Check CloudWatch logs for all instances"
    echo "  --test-connection Test database connections"
    echo "  --comprehensive   Show comprehensive status (default)"
    echo "  --help            Show this help message"
    echo ""
    echo "Examples:"
    echo "  $0 --staging      # Check staging RDS only"
    echo "  $0 --github-actions # Show GitHub Actions info"
    echo "  $0 --test-connection # Test all connections"
}

# Main execution
main() {
    case "${1:-}" in
        --staging)
            check_rds_status "$STAGING_INSTANCE" "Staging"
            check_security_groups "$STAGING_INSTANCE" "Staging"
            test_database_connection "$STAGING_INSTANCE" "Staging"
            ;;
        --production)
            check_rds_status "$PRODUCTION_INSTANCE" "Production"
            check_security_groups "$PRODUCTION_INSTANCE" "Production"
            test_database_connection "$PRODUCTION_INSTANCE" "Production"
            ;;
        --github-actions)
            check_github_actions
            ;;
        --security-groups)
            check_security_groups "$STAGING_INSTANCE" "Staging"
            check_security_groups "$PRODUCTION_INSTANCE" "Production"
            ;;
        --cloudwatch)
            check_cloudwatch_logs "$STAGING_INSTANCE" "Staging"
            check_cloudwatch_logs "$PRODUCTION_INSTANCE" "Production"
            ;;
        --test-connection)
            test_database_connection "$STAGING_INSTANCE" "Staging"
            test_database_connection "$PRODUCTION_INSTANCE" "Production"
            ;;
        --comprehensive|"")
            show_comprehensive_status
            ;;
        --help)
            show_usage
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
