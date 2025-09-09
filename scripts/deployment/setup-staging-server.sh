#!/bin/bash

# Staging Server Setup Script for Tulsa SEO
# This script helps you set up a staging server for deployment

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Logging functions
log() {
    echo -e "${GREEN}[$(date +'%Y-%m-%d %H:%M:%S')] $1${NC}"
}

warn() {
    echo -e "${YELLOW}[$(date +'%Y-%m-%d %H:%M:%S')] WARNING: $1${NC}"
}

error() {
    echo -e "${RED}[$(date +'%Y-%m-%d %H:%M:%S')] ERROR: $1${NC}"
    exit 1
}

info() {
    echo -e "${BLUE}[$(date +'%Y-%m-%d %H:%M:%S')] INFO: $1${NC}"
}

echo "🚀 Tulsa SEO Staging Server Setup"
echo "=================================="
echo

# Check if AWS CLI is available
if ! command -v aws &> /dev/null; then
    error "AWS CLI is not installed. Please install it first: brew install awscli"
fi

# Check AWS credentials
if ! aws sts get-caller-identity &> /dev/null; then
    error "AWS credentials not configured. Please run: aws configure"
fi

log "AWS CLI and credentials verified"

echo
echo "Choose your staging setup option:"
echo "1. Create new EC2 instance for staging"
echo "2. Use existing EC2 instance"
echo "3. Use local machine for staging (development only)"
echo "4. Skip staging setup (deployments will fail until configured)"
echo

read -p "Enter your choice (1-4): " choice

case $choice in
    1)
        log "Creating new EC2 instance for staging..."
        create_ec2_instance
        ;;
    2)
        log "Using existing EC2 instance..."
        use_existing_instance
        ;;
    3)
        log "Setting up local staging..."
        setup_local_staging
        ;;
    4)
        warn "Skipping staging setup. Deployments will fail until configured."
        exit 0
        ;;
    *)
        error "Invalid choice. Please run the script again."
        ;;
esac

# Function to create new EC2 instance
create_ec2_instance() {
    log "Creating new EC2 instance for staging..."
    
    # Get user input for instance configuration
    read -p "Enter instance type (default: t3.micro): " instance_type
    instance_type=${instance_type:-t3.micro}
    
    read -p "Enter key pair name (default: staging-key): " key_name
    key_name=${key_name:-staging-key}
    
    read -p "Enter security group name (default: staging-sg): " sg_name
    sg_name=${sg_name:-staging-sg}
    
    # Create key pair if it doesn't exist
    if ! aws ec2 describe-key-pairs --key-names "$key_name" &> /dev/null; then
        log "Creating key pair: $key_name"
        aws ec2 create-key-pair --key-name "$key_name" --query 'KeyMaterial' --output text > "${key_name}.pem"
        chmod 600 "${key_name}.pem"
        log "Key pair created and saved as ${key_name}.pem"
    fi
    
    # Create security group if it doesn't exist
    if ! aws ec2 describe-security-groups --group-names "$sg_name" &> /dev/null; then
        log "Creating security group: $sg_name"
        sg_id=$(aws ec2 create-security-group --group-name "$sg_name" --description "Staging server security group" --query 'GroupId' --output text)
        
        # Add SSH access
        aws ec2 authorize-security-group-ingress --group-id "$sg_id" --protocol tcp --port 22 --cidr 0.0.0.0/0
        # Add HTTP access
        aws ec2 authorize-security-group-ingress --group-id "$sg_id" --protocol tcp --port 80 --cidr 0.0.0.0/0
        # Add HTTPS access
        aws ec2 authorize-security-group-ingress --group-id "$sg_id" --protocol tcp --port 443 --cidr 0.0.0.0/0
        
        log "Security group created with ID: $sg_id"
    else
        sg_id=$(aws ec2 describe-security-groups --group-names "$sg_name" --query 'SecurityGroups[0].GroupId' --output text)
        log "Using existing security group: $sg_id"
    fi
    
    # Launch EC2 instance
    log "Launching EC2 instance..."
    instance_id=$(aws ec2 run-instances \
        --image-id ami-0c02fb55956c7d316 \
        --count 1 \
        --instance-type "$instance_type" \
        --key-name "$key_name" \
        --security-group-ids "$sg_id" \
        --tag-specifications 'ResourceType=instance,Tags=[{Key=Name,Value=tulsa-seo-staging}]' \
        --query 'Instances[0].InstanceId' \
        --output text)
    
    log "Instance launched with ID: $instance_id"
    
    # Wait for instance to be running
    log "Waiting for instance to be running..."
    aws ec2 wait instance-running --instance-ids "$instance_id"
    
    # Get public IP
    public_ip=$(aws ec2 describe-instances --instance-ids "$instance_id" --query 'Reservations[0].Instances[0].PublicIpAddress' --output text)
    
    log "Instance is running with public IP: $public_ip"
    
    # Setup server
    setup_server "$public_ip" "$key_name"
}

# Function to use existing EC2 instance
use_existing_instance() {
    log "Listing available EC2 instances..."
    aws ec2 describe-instances \
        --query 'Reservations[*].Instances[*].[InstanceId,State.Name,PublicIpAddress,Tags[?Key==`Name`].Value|[0]]' \
        --output table
    
    read -p "Enter instance ID: " instance_id
    
    # Get instance details
    instance_info=$(aws ec2 describe-instances --instance-ids "$instance_id" --query 'Reservations[0].Instances[0]')
    state=$(echo "$instance_info" | jq -r '.State.Name')
    public_ip=$(echo "$instance_info" | jq -r '.PublicIpAddress')
    key_name=$(echo "$instance_info" | jq -r '.KeyName')
    
    if [ "$state" != "running" ]; then
        error "Instance $instance_id is not running (current state: $state)"
    fi
    
    if [ "$public_ip" = "null" ]; then
        error "Instance $instance_id does not have a public IP address"
    fi
    
    log "Using instance $instance_id with public IP: $public_ip"
    
    # Setup server
    setup_server "$public_ip" "$key_name"
}

# Function to setup local staging
setup_local_staging() {
    log "Setting up local staging environment..."
    
    # Update GitHub secrets for local staging
    log "Updating GitHub secrets for local staging..."
    
    # Get local IP
    local_ip=$(ifconfig | grep "inet " | grep -v 127.0.0.1 | awk '{print $2}' | head -1)
    
    info "Local IP detected: $local_ip"
    info "You'll need to update these GitHub secrets:"
    echo "  STAGING_HOST=$local_ip"
    echo "  STAGING_USER=$(whoami)"
    echo "  STAGING_SSH_KEY=[your local SSH private key]"
    echo
    info "To update secrets, run:"
    echo "  gh secret set STAGING_HOST --body '$local_ip'"
    echo "  gh secret set STAGING_USER --body '$(whoami)'"
    echo "  gh secret set STAGING_SSH_KEY --body '$(cat ~/.ssh/id_rsa)'"
    echo
    warn "Note: Local staging is for development only. Make sure your local machine is accessible from GitHub Actions."
}

# Function to setup server
setup_server() {
    local public_ip="$1"
    local key_name="$2"
    
    log "Setting up server at $public_ip..."
    
    # Wait for SSH to be available
    log "Waiting for SSH to be available..."
    until ssh -i "${key_name}.pem" -o ConnectTimeout=5 -o StrictHostKeyChecking=no ubuntu@"$public_ip" "echo 'SSH ready'" &> /dev/null; do
        sleep 10
        log "Still waiting for SSH..."
    done
    
    # Install required software
    log "Installing required software on server..."
    ssh -i "${key_name}.pem" ubuntu@"$public_ip" << 'EOF'
        sudo apt-get update
        sudo apt-get install -y nginx php8.2-fpm php8.2-cli php8.2-mysql php8.2-xml php8.2-mbstring php8.2-curl php8.2-zip composer postgresql-client
        sudo systemctl enable nginx php8.2-fpm
        sudo systemctl start nginx php8.2-fpm
        
        # Create deployment directory
        sudo mkdir -p /var/www/tulsa-seo-backend
        sudo chown -R ubuntu:ubuntu /var/www/tulsa-seo-backend
        
        # Create nginx configuration
        sudo tee /etc/nginx/sites-available/tulsa-seo-staging << 'NGINX_EOF'
server {
    listen 80;
    server_name _;
    root /var/www/tulsa-seo-backend/public;
    index index.php;
    
    location / {
        try_files $uri $uri/ /index.php$is_args$args;
    }
    
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
NGINX_EOF
        
        sudo ln -sf /etc/nginx/sites-available/tulsa-seo-staging /etc/nginx/sites-enabled/
        sudo rm -f /etc/nginx/sites-enabled/default
        sudo nginx -t
        sudo systemctl reload nginx
        
        echo "Server setup completed successfully"
EOF
    
    log "Server setup completed!"
    
    # Update GitHub secrets
    log "Updating GitHub secrets..."
    gh secret set STAGING_HOST --body "$public_ip"
    gh secret set STAGING_USER --body "ubuntu"
    
    # Convert key to OpenSSH format if needed
    if [ -f "${key_name}.pem" ]; then
        # Copy the key content for GitHub secret
        key_content=$(cat "${key_name}.pem")
        gh secret set STAGING_SSH_KEY --body "$key_content"
        log "SSH key uploaded to GitHub secrets"
    fi
    
    log "Staging server setup completed!"
    log "Server IP: $public_ip"
    log "SSH command: ssh -i ${key_name}.pem ubuntu@$public_ip"
    log "You can now deploy to staging by pushing to the develop branch"
}

# Main execution
main() {
    log "Starting staging server setup..."
    
    # Check prerequisites
    if ! command -v gh &> /dev/null; then
        error "GitHub CLI is not installed. Please install it first: brew install gh"
    fi
    
    if ! gh auth status &> /dev/null; then
        error "GitHub CLI is not authenticated. Please run: gh auth login"
    fi
    
    log "Prerequisites verified"
}

# Run main function
main "$@"
