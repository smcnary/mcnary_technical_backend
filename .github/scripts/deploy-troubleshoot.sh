#!/bin/bash

# Deployment Troubleshooting Script
# This script helps diagnose SSH and deployment issues

set -e

echo "🔍 Deployment Troubleshooting Script"
echo "====================================="

# Check if required environment variables are set
check_env_vars() {
    echo "📋 Checking environment variables..."
    
    required_vars=("STAGING_USER" "STAGING_HOST" "STAGING_SSH_KEY")
    
    for var in "${required_vars[@]}"; do
        if [ -z "${!var}" ]; then
            echo "❌ Missing required environment variable: $var"
            return 1
        else
            echo "✅ $var is set"
        fi
    done
    
    echo "✅ All required environment variables are present"
    return 0
}

# Test SSH connection
test_ssh_connection() {
    echo "🔐 Testing SSH connection..."
    
    # Create temporary SSH key file
    echo "$STAGING_SSH_KEY" > temp_key
    chmod 600 temp_key
    
    # Test SSH connection
    if ssh -i temp_key -o StrictHostKeyChecking=no -o ConnectTimeout=10 \
        "$STAGING_USER@$STAGING_HOST" "echo 'SSH connection successful'"; then
        echo "✅ SSH connection successful"
        rm -f temp_key
        return 0
    else
        echo "❌ SSH connection failed"
        rm -f temp_key
        return 1
    fi
}

# Check server requirements
check_server_requirements() {
    echo "🖥️ Checking server requirements..."
    
    # Create temporary SSH key file
    echo "$STAGING_SSH_KEY" > temp_key
    chmod 600 temp_key
    
    # Check if required directories exist
    ssh -i temp_key -o StrictHostKeyChecking=no \
        "$STAGING_USER@$STAGING_HOST" << 'EOF'
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
        fi
        
        # Check if Composer is installed
        if command -v composer &> /dev/null; then
            echo "✅ Composer is installed: $(composer --version | head -n1)"
        else
            echo "❌ Composer is not installed"
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
        if systemctl is-active --quiet php8.2-fpm; then
            echo "✅ PHP-FPM is running"
        else
            echo "❌ PHP-FPM is not running"
        fi
        
        if systemctl is-active --quiet nginx; then
            echo "✅ Nginx is running"
        else
            echo "❌ Nginx is not running"
        fi
EOF
    
    rm -f temp_key
}

# Test file transfer
test_file_transfer() {
    echo "📤 Testing file transfer..."
    
    # Create temporary SSH key file
    echo "$STAGING_SSH_KEY" > temp_key
    chmod 600 temp_key
    
    # Create a test file
    echo "test content" > test_file.txt
    
    # Test SCP
    if scp -i temp_key -o StrictHostKeyChecking=no \
        test_file.txt "$STAGING_USER@$STAGING_HOST:/tmp/test_file.txt"; then
        echo "✅ File transfer successful"
        
        # Clean up test file
        ssh -i temp_key -o StrictHostKeyChecking=no \
            "$STAGING_USER@$STAGING_HOST" "rm -f /tmp/test_file.txt"
    else
        echo "❌ File transfer failed"
    fi
    
    rm -f temp_key test_file.txt
}

# Main troubleshooting function
main() {
    echo "Starting deployment troubleshooting..."
    echo
    
    # Check environment variables
    if ! check_env_vars; then
        echo "❌ Environment variables check failed"
        exit 1
    fi
    
    echo
    
    # Test SSH connection
    if ! test_ssh_connection; then
        echo "❌ SSH connection test failed"
        echo "💡 Possible solutions:"
        echo "   1. Check if SSH key is properly formatted"
        echo "   2. Verify public key is added to server's authorized_keys"
        echo "   3. Check if server is accessible"
        echo "   4. Verify username and hostname are correct"
        exit 1
    fi
    
    echo
    
    # Check server requirements
    check_server_requirements
    
    echo
    
    # Test file transfer
    test_file_transfer
    
    echo
    echo "✅ Troubleshooting completed successfully!"
    echo "🚀 Your deployment should work now"
}

# Run main function
main "$@"
