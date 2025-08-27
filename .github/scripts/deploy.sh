#!/bin/bash

# Deployment Script Template for Tulsa SEO
# Customize this script for your specific deployment environment

set -e  # Exit on any error

# Configuration
ENVIRONMENT=${1:-staging}
DEPLOY_PATH="/var/www/counselrank-legal"
BACKUP_PATH="/var/backups/counselrank-legal"
TIMESTAMP=$(date +%Y%m%d_%H%M%S)

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Logging function
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

# Check if running as root or with sudo
check_permissions() {
    if [[ $EUID -eq 0 ]]; then
        error "This script should not be run as root"
    fi
}

# Create backup of current deployment
create_backup() {
    if [ -d "$DEPLOY_PATH" ]; then
        log "Creating backup of current deployment..."
        sudo mkdir -p "$BACKUP_PATH"
        sudo tar -czf "$BACKUP_PATH/backup_$TIMESTAMP.tar.gz" -C "$DEPLOY_PATH" .
        log "Backup created: backup_$TIMESTAMP.tar.gz"
    else
        warn "No existing deployment found, skipping backup"
    fi
}

# Deploy backend
deploy_backend() {
    log "Deploying backend application..."
    
    # Extract backend build
    if [ -f "backend-build.tar.gz" ]; then
        sudo mkdir -p "$DEPLOY_PATH/backend"
        sudo tar -xzf backend-build.tar.gz -C "$DEPLOY_PATH/backend"
        
        # Set permissions
        sudo chown -R www-data:www-data "$DEPLOY_PATH/backend"
        sudo chmod -R 755 "$DEPLOY_PATH/backend"
        sudo chmod -R 777 "$DEPLOY_PATH/backend/var"
        
        # Install dependencies (dev dependencies already included in build)
        cd "$DEPLOY_PATH/backend"
        composer install --no-interaction
        
        # Optimize autoloader for production
        composer dump-autoload --optimize --no-dev --classmap-authoritative || {
            echo "⚠️ Autoloader optimization failed, continuing with standard autoloader"
        }
        
        # Clear and warm cache in production environment
        php bin/console cache:clear --env=prod --no-debug || {
            echo "⚠️ Cache clear failed, continuing..."
        }
        php bin/console cache:warmup --env=prod || {
            echo "⚠️ Cache warmup failed, continuing..."
        }
        
        # Run migrations
        php bin/console doctrine:migrations:migrate --env=prod --no-interaction
        
        log "Backend deployed successfully"
    else
        error "Backend build artifact not found"
    fi
}

# Deploy frontend
deploy_frontend() {
    log "Deploying frontend application..."
    
    if [ -d "frontend-build" ]; then
        sudo mkdir -p "$DEPLOY_PATH/frontend"
        sudo cp -r frontend-build/* "$DEPLOY_PATH/frontend/"
        
        # Set permissions
        sudo chown -R www-data:www-data "$DEPLOY_PATH/frontend"
        sudo chmod -R 755 "$DEPLOY_PATH/frontend"
        
        log "Frontend deployed successfully"
    else
        error "Frontend build artifact not found"
    fi
}

# Deploy audit service
deploy_audit_service() {
    log "Deploying audit service..."
    
    if [ -f "audit-service-build.tar.gz" ]; then
        sudo mkdir -p "$DEPLOY_PATH/audit-service"
        sudo tar -xzf audit-service-build.tar.gz -C "$DEPLOY_PATH/audit-service"
        
        # Set permissions
        sudo chown -R www-data:www-data "$DEPLOY_PATH/audit-service"
        sudo chmod -R 755 "$DEPLOY_PATH/audit-service"
        sudo chmod -R 777 "$DEPLOY_PATH/audit-service/var"
        
        # Install dependencies if composer.json exists
        if [ -f "$DEPLOY_PATH/audit-service/composer.json" ]; then
            cd "$DEPLOY_PATH/audit-service"
            composer install --no-interaction
            
            # Optimize autoloader for production
            composer dump-autoload --optimize --no-dev --classmap-authoritative || {
                echo "⚠️ Autoloader optimization failed, continuing with standard autoloader"
            }
            
            # Clear and warm cache if Symfony console exists
            if [ -f "bin/console" ]; then
                php bin/console cache:clear --env=prod --no-debug
                php bin/console cache:warmup --env=prod
            fi
        fi
        
        log "Audit service deployed successfully"
    else
        warn "Audit service build artifact not found, skipping..."
    fi
}

# Configure web server
configure_webserver() {
    log "Configuring web server..."
    
    # Create nginx configuration
    sudo tee /etc/nginx/sites-available/counselrank-legal << EOF
server {
    listen 80;
    server_name tulsa-seo.com www.tulsa-seo.com;
    
    # Frontend
    location / {
        root $DEPLOY_PATH/frontend;
        try_files \$uri \$uri/ /index.html;
        
        # Cache static assets
        location ~* \.(js|css|png|jpg|jpeg|gif|ico|svg)$ {
            expires 1y;
            add_header Cache-Control "public, immutable";
        }
    }
    
    # Backend API
    location /api {
        try_files \$uri /backend/public/index.php\$is_args\$args;
    }
    
    # Backend public files
    location /backend/public {
        alias $DEPLOY_PATH/backend/public;
        try_files \$uri /backend/public/index.php\$is_args\$args;
    }
    
    # Audit Service API
    location /audit {
        try_files \$uri /audit-service/public/index.php\$is_args\$args;
    }
    
    # Audit Service public files
    location /audit-service/public {
        alias $DEPLOY_PATH/audit-service/public;
        try_files \$uri /audit-service/public/index.php\$is_args\$args;
    }
    
    # PHP processing
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME \$document_root\$fastcgi_script_name;
        include fastcgi_params;
    }
}
EOF

    # Enable site
    sudo ln -sf /etc/nginx/sites-available/counselrank-legal /etc/nginx/sites-enabled/
    
    # Test configuration
    sudo nginx -t
    
    # Reload nginx
    sudo systemctl reload nginx
    
    log "Web server configured successfully"
}

# Health check
health_check() {
    log "Performing health check..."
    
    # Wait for services to start
    sleep 5
    
    # Check if backend is responding
    if curl -f http://localhost/api > /dev/null 2>&1; then
        log "Backend health check passed"
    else
        warn "Backend health check failed"
    fi
    
    # Check if frontend is accessible
    if curl -f http://localhost/ > /dev/null 2>&1; then
        log "Frontend health check passed"
    else
        warn "Frontend health check failed"
    fi
}

# Rollback function
rollback() {
    log "Rolling back to previous deployment..."
    
    if [ -f "$BACKUP_PATH/backup_$TIMESTAMP.tar.gz" ]; then
        sudo rm -rf "$DEPLOY_PATH"
        sudo mkdir -p "$DEPLOY_PATH"
        sudo tar -xzf "$BACKUP_PATH/backup_$TIMESTAMP.tar.gz" -C "$DEPLOY_PATH"
        sudo chown -R www-data:www-data "$DEPLOY_PATH"
        sudo systemctl reload nginx
        log "Rollback completed"
    else
        error "No backup found for rollback"
    fi
}

# Main deployment function
main() {
    log "Starting deployment to $ENVIRONMENT environment..."
    
    check_permissions
    create_backup
    
    # Deploy applications
    deploy_backend
    deploy_frontend
    deploy_audit_service
    
    # Configure web server
    configure_webserver
    
    # Health check
    health_check
    
    log "Deployment completed successfully!"
    log "Application accessible at: http://localhost"
}

# Handle script arguments
case "${1:-}" in
    "rollback")
        rollback
        ;;
    "help"|"-h"|"--help")
        echo "Usage: $0 [environment|rollback|help]"
        echo "  environment: staging|production (default: staging)"
        echo "  rollback:   rollback to previous deployment"
        echo "  help:       show this help message"
        ;;
    *)
        main
        ;;
esac
