#!/bin/bash

# SSH Connection Test Script for EC2 Staging Server
# This script helps test the SSH connection before configuring GitHub Actions

set -e

echo "🔍 SSH Connection Test for EC2 Staging Server"
echo "=============================================="

# Check if SSH key exists
if [ ! -f "staging_key" ]; then
    echo "❌ SSH private key 'staging_key' not found!"
    echo "💡 Run: ssh-keygen -t ed25519 -C 'github-actions-staging' -f staging_key -N ''"
    exit 1
fi

# Set proper permissions
chmod 600 staging_key

echo "✅ SSH key found: staging_key"
echo

# Get connection details from user
read -p "Enter your EC2 public IP address or hostname: " EC2_HOST
read -p "Enter your EC2 username (ec2-user/ubuntu/admin): " EC2_USER

echo
echo "🔐 Testing SSH connection to $EC2_USER@$EC2_HOST..."
echo

# Test 1: Basic SSH connection
echo "Test 1: Basic SSH Connection"
echo "----------------------------"
if ssh -i staging_key -o ConnectTimeout=10 -o StrictHostKeyChecking=no \
    "$EC2_USER@$EC2_HOST" "echo '✅ SSH connection successful!'"; then
    echo "✅ SSH connection test passed!"
else
    echo "❌ SSH connection test failed!"
    echo
    echo "💡 Troubleshooting steps:"
    echo "   1. Verify the public key is added to ~/.ssh/authorized_keys on the server"
    echo "   2. Check if the EC2 security group allows SSH (port 22)"
    echo "   3. Ensure the instance is running"
    echo "   4. Verify the username is correct (ec2-user for Amazon Linux, ubuntu for Ubuntu)"
    exit 1
fi

echo

# Test 2: Check server requirements
echo "Test 2: Server Requirements Check"
echo "----------------------------------"
ssh -i staging_key -o StrictHostKeyChecking=no \
    "$EC2_USER@$EC2_HOST" << 'EOF'
    echo "📁 Checking directories..."
    
    # Check if deployment directory exists
    if [ -d "/var/www/tulsa-seo-backend" ]; then
        echo "✅ Deployment directory exists: /var/www/tulsa-seo-backend"
    else
        echo "❌ Deployment directory missing: /var/www/tulsa-seo-backend"
        echo "💡 Create it with: sudo mkdir -p /var/www/tulsa-seo-backend"
        echo "💡 Set permissions with: sudo chown -R $USER:$USER /var/www/tulsa-seo-backend"
    fi
    
    # Check if PHP is installed
    if command -v php &> /dev/null; then
        echo "✅ PHP is installed: $(php --version | head -n1)"
    else
        echo "❌ PHP is not installed"
        echo "💡 Install with: sudo apt install php8.2-fpm php8.2-cli"
    fi
    
    # Check if Composer is installed
    if command -v composer &> /dev/null; then
        echo "✅ Composer is installed: $(composer --version | head -n1)"
    else
        echo "❌ Composer is not installed"
        echo "💡 Install with: curl -sS https://getcomposer.org/installer | php && sudo mv composer.phar /usr/local/bin/composer"
    fi
    
    # Check if required PHP extensions are installed
    echo "🔍 Checking PHP extensions..."
    required_extensions=("pdo_pgsql" "mbstring" "intl" "xml" "zip" "gd" "curl")
    
    for ext in "${required_extensions[@]}"; do
        if php -m | grep -q "$ext"; then
            echo "✅ PHP extension $ext is installed"
        else
            echo "❌ PHP extension $ext is missing"
        fi
    done
    
    # Check if services are running
    echo "🔍 Checking services..."
    if systemctl is-active --quiet php8.2-fpm 2>/dev/null; then
        echo "✅ PHP-FPM is running"
    elif systemctl is-active --quiet php-fpm 2>/dev/null; then
        echo "✅ PHP-FPM is running (generic version)"
    else
        echo "❌ PHP-FPM is not running"
    fi
    
    if systemctl is-active --quiet nginx 2>/dev/null; then
        echo "✅ Nginx is running"
    else
        echo "❌ Nginx is not running"
    fi
EOF

echo

# Test 3: File transfer test
echo "Test 3: File Transfer Test"
echo "-------------------------"
echo "test content" > test_file.txt

if scp -i staging_key -o StrictHostKeyChecking=no \
    test_file.txt "$EC2_USER@$EC2_HOST:/tmp/test_file.txt"; then
    echo "✅ File transfer test passed!"
    
    # Clean up test file
    ssh -i staging_key -o StrictHostKeyChecking=no \
        "$EC2_USER@$EC2_HOST" "rm -f /tmp/test_file.txt"
else
    echo "❌ File transfer test failed!"
fi

rm -f test_file.txt

echo
echo "🎉 SSH Connection Test Complete!"
echo
echo "📋 Next Steps:"
echo "1. Add these secrets to GitHub Repository → Settings → Secrets and variables → Actions:"
echo "   - STAGING_SSH_KEY: $(cat staging_key)"
echo "   - STAGING_USER: $EC2_USER"
echo "   - STAGING_HOST: $EC2_HOST"
echo
echo "2. Trigger a deployment by pushing to the 'develop' branch"
echo "3. Monitor GitHub Actions for successful deployment"
echo
echo "🔒 Security Note: Remove the SSH key files after setup:"
echo "   rm staging_key staging_key.pub"
