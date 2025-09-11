# Complete Deployment Guide

This comprehensive guide covers deploying the CounselRank.legal application to production, including server setup, database configuration, RDS deployment, build optimization, and deployment best practices.

## Table of Contents

1. [Overview](#overview)
2. [Prerequisites](#prerequisites)
3. [Server Setup](#server-setup)
4. [Backend Deployment](#backend-deployment)
5. [Frontend Deployment](#frontend-deployment)
6. [RDS Database Deployment](#rds-database-deployment)
7. [SSL Configuration](#ssl-configuration)
8. [Continuous Deployment](#continuous-deployment)
9. [Monitoring & Maintenance](#monitoring--maintenance)
10. [Troubleshooting](#troubleshooting)

## Overview

This guide covers deploying both the Symfony backend and React frontend applications to production, including:

- **Server Setup**: PHP, Node.js, web server configuration
- **Database Deployment**: Local PostgreSQL and AWS RDS options
- **Application Deployment**: Backend and frontend build processes
- **Security Configuration**: SSL, CORS, authentication
- **Monitoring**: Logs, performance, alerts
- **Maintenance**: Backups, updates, scaling

## Prerequisites

### Backend Requirements
- Production server with PHP 8.2+ and required extensions
- PostgreSQL database server (local or AWS RDS)
- Web server (Nginx/Apache)
- Git access to your repository
- SSH access to production server

### Frontend Requirements
- Node.js 18+ and npm
- Web server (Nginx/Apache) or CDN service
- Access to your backend API
- Domain name and SSL certificate (recommended)

### Required Tools
- **AWS CLI** - Configured with appropriate permissions (for RDS)
- **PostgreSQL Client Tools** - `pg_dump`, `psql`
- **Terraform** (optional) - For infrastructure as code deployment
- **PHP 8.3+** - For running Symfony migrations
- **Composer** - For PHP dependencies

### AWS Permissions (for RDS)
Your AWS user/role needs the following permissions:
- `rds:*` - RDS instance management
- `ec2:*` - Security groups and VPC management
- `iam:*` - IAM roles for monitoring
- `cloudwatch:*` - CloudWatch alarms and logs

## Server Setup

### 1. Install Required Software

```bash
# Update system packages
sudo apt update && sudo apt upgrade -y

# Install PHP and extensions
sudo apt install php8.2-fpm php8.2-cli php8.2-pgsql php8.2-xml php8.2-mbstring php8.2-curl php8.2-zip php8.2-intl php8.2-opcache

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Install Node.js (for frontend asset compilation)
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt-get install -y nodejs
```

### 2. Install and Configure Web Server

#### Nginx Configuration (Recommended)

```nginx
# Backend API
server {
    listen 80;
    server_name api.your-domain.com;
    root /var/www/mcnary_backend/public;
    index index.php;

    location / {
        try_files $uri /index.php$is_args$args;
    }

    location ~ ^/index\.php(/|$) {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_split_path_info ^(.+\.php)(/.*)$;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        fastcgi_param DOCUMENT_ROOT $realpath_root;
        internal;
    }

    location ~ \.php$ {
        return 404;
    }

    # Security headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;

    error_log /var/log/nginx/mcnary_backend_error.log;
    access_log /var/log/nginx/mcnary_backend_access.log;
}

# Frontend App
server {
    listen 80;
    server_name your-domain.com;
    root /var/www/mcnary_frontend/dist;
    index index.html;

    location / {
        try_files $uri $uri/ /index.html;
    }

    # Cache static assets
    location ~* \.(js|css|png|jpg|jpeg|gif|ico|svg)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }

    # Security headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;

    error_log /var/log/nginx/mcnary_frontend_error.log;
    access_log /var/log/nginx/mcnary_frontend_access.log;
}
```

#### Apache Configuration

```apache
# Backend API
<VirtualHost *:80>
    ServerName api.your-domain.com
    DocumentRoot /var/www/mcnary_backend/public
    
    <Directory /var/www/mcnary_backend/public>
        AllowOverride All
        Require all granted
        FallbackResource /index.php
    </Directory>
    
    ErrorLog ${APACHE_LOG_DIR}/mcnary_backend_error.log
    CustomLog ${APACHE_LOG_DIR}/mcnary_backend_access.log combined
</VirtualHost>

# Frontend App
<VirtualHost *:80>
    ServerName your-domain.com
    DocumentRoot /var/www/mcnary_frontend/dist
    
    <Directory /var/www/mcnary_frontend/dist>
        AllowOverride All
        Require all granted
        FallbackResource /index.html
    </Directory>
    
    ErrorLog ${APACHE_LOG_DIR}/mcnary_frontend_error.log
    CustomLog ${APACHE_LOG_DIR}/mcnary_frontend_access.log combined
</VirtualHost>
```

## Backend Deployment

### 1. Initial Deployment

```bash
# Clone repository
cd /var/www
sudo git clone https://github.com/your-username/mcnary_technical_backend.git mcnary_backend
sudo chown -R www-data:www-data mcnary_backend
cd mcnary_backend

# Install dependencies
composer install --no-dev --optimize-autoloader

# Set up environment
cp .env.example .env
# Edit .env with production values
```

### 2. Environment Configuration

Create `.env` file with production values:

```env
APP_ENV=prod
APP_DEBUG=false
APP_SECRET=your-production-secret-key
DATABASE_URL="postgresql://username:password@host:5432/database_name?serverVersion=16&charset=utf8"
JWT_SECRET_KEY=%kernel.project_dir%/config/jwt/private.pem
JWT_PUBLIC_KEY=%kernel.project_dir%/config/jwt/public.pem
JWT_PASSPHRASE=your-production-passphrase
CORS_ALLOW_ORIGIN="^https?://(your-domain\.com|api\.your-domain\.com)$"
```

### 3. Database Setup

```bash
# Generate JWT keys
php bin/console lexik:jwt:generate-keypair --overwrite

# Run database migrations
php bin/console doctrine:migrations:migrate --env=prod --no-interaction

# Create system account
php bin/console app:create-system-account --username=admin --display-name="System Administrator" --permissions=read,write,admin --env=prod

# Warm up cache
php bin/console cache:warmup --env=prod
```

### 4. File Permissions

```bash
# Set proper permissions
sudo chown -R www-data:www-data /var/www/mcnary_backend
sudo chmod -R 755 /var/www/mcnary_backend
sudo chmod -R 775 /var/www/mcnary_backend/var/cache
sudo chmod -R 775 /var/www/mcnary_backend/var/log
sudo chmod -R 775 /var/www/mcnary_backend/config/jwt
```

## Frontend Deployment

### 1. Build Configuration

Create `.env.production` file:

```bash
# API Configuration
VITE_API_BASE_URL=https://api.your-domain.com/api
VITE_APP_NAME="CounselRank.legal"
VITE_APP_VERSION=1.0.0

# Feature Flags
VITE_ENABLE_ANALYTICS=true
VITE_ENABLE_DEBUG=false

# External Services
VITE_GOOGLE_ANALYTICS_ID=GA_MEASUREMENT_ID
```

### 2. Build Optimization

Update `vite.config.ts` for production:

```typescript
import { defineConfig } from 'vite'
import react from '@vitejs/plugin-react'

export default defineConfig({
  plugins: [react()],
  build: {
    outDir: 'dist',
    sourcemap: false,
    minify: 'terser',
    rollupOptions: {
      output: {
        manualChunks: {
          vendor: ['react', 'react-dom'],
          utils: ['axios', 'react-router-dom']
        }
      }
    },
    terserOptions: {
      compress: {
        drop_console: true,
        drop_debugger: true
      }
    }
  }
})
```

### 3. Production Build

```bash
# Install dependencies
npm ci

# Build for production
npm run build

# Preview build locally
npm run preview
```

### 4. Deploy Frontend

```bash
# Copy build files to server
scp -r dist/* user@your-server:/var/www/mcnary_frontend/dist/

# Or use deployment script
rsync -avz --delete dist/ user@your-server:/var/www/mcnary_frontend/dist/
```

## RDS Database Deployment

### Deployment Options

#### Option 1: Automated Script Deployment (Recommended)

The `deploy-rds.sh` script provides a fully automated deployment process.

```bash
# Navigate to the backend directory
cd backend

# Run the deployment script
./scripts/deploy-rds.sh

# Or with custom parameters
./scripts/deploy-rds.sh \
  --instance-id "counselrank-prod-db" \
  --db-name "counselrank_prod" \
  --username "counselrank_admin" \
  --password "your-secure-password" \
  --instance-class "db.t3.small" \
  --storage 50 \
  --region "us-east-1"
```

#### Script Features
- ✅ Creates security groups
- ✅ Provisions RDS PostgreSQL 16 instance
- ✅ Configures parameter groups
- ✅ Sets up monitoring and alarms
- ✅ Runs database migrations
- ✅ Updates environment configuration
- ✅ Creates initial backup

#### Option 2: Terraform Infrastructure as Code

For production environments, use Terraform for infrastructure management.

```bash
# Navigate to scripts directory
cd backend/scripts

# Copy and configure variables
cp rds-terraform.tfvars.example terraform.tfvars
# Edit terraform.tfvars with your values

# Initialize Terraform
terraform init

# Plan deployment
terraform plan

# Apply deployment
terraform apply
```

#### Terraform Features
- ✅ Infrastructure as Code
- ✅ State management
- ✅ Resource tagging
- ✅ CloudWatch monitoring
- ✅ Security group management
- ✅ Parameter group configuration
- ✅ Output values for integration

### RDS Configuration

#### Instance Classes
| Environment | Instance Class | Use Case |
|-------------|----------------|----------|
| Development | `db.t3.micro` | Testing, development |
| Staging | `db.t3.small` | Pre-production testing |
| Production | `db.t3.medium+` | Production workloads |

#### Storage Configuration
- **Storage Type**: General Purpose SSD (gp2)
- **Initial Storage**: 20GB (development) to 100GB+ (production)
- **Auto-scaling**: Enabled (up to 2x initial storage)
- **Encryption**: Enabled at rest

#### High Availability
- **Multi-AZ**: Enabled for production
- **Backup Retention**: 7 days (production), 1 day (development)
- **Backup Window**: 03:00-04:00 UTC
- **Maintenance Window**: Sunday 04:00-05:00 UTC

### Data Migration

#### From Local Development Database

If you have existing data in your local PostgreSQL database:

```bash
# Run the migration script
./scripts/migrate-to-rds.sh \
  --rds-endpoint "your-rds-endpoint.region.rds.amazonaws.com" \
  --rds-password "your-rds-password"
```

#### Migration Process
1. **Backup Local Database** - Creates SQL dump of local data
2. **Test RDS Connection** - Verifies connectivity
3. **Run Migrations** - Applies schema to RDS
4. **Restore Data** - Imports data from local backup
5. **Verify Migration** - Confirms data integrity
6. **Update Configuration** - Updates environment files

#### From Existing RDS Instance

For migrating from one RDS instance to another:

```bash
# Create snapshot of source instance
aws rds create-db-snapshot \
  --db-instance-identifier "source-instance" \
  --db-snapshot-identifier "migration-snapshot-$(date +%Y%m%d)"

# Restore from snapshot
aws rds restore-db-instance-from-db-snapshot \
  --db-instance-identifier "target-instance" \
  --db-snapshot-identifier "migration-snapshot-$(date +%Y%m%d)"
```

### Security Configuration

#### Network Security

##### Security Groups
The deployment creates a security group with:
- **Inbound**: PostgreSQL (port 5432) from your application servers
- **Outbound**: All traffic (for updates and monitoring)

##### VPC Configuration
- **Public Access**: Disabled for production
- **Subnet Groups**: Uses default or custom subnets
- **VPC Peering**: Recommended for cross-region access

#### Database Security

##### Authentication
- **Master Username**: `counselrank_admin`
- **Password**: Auto-generated secure password
- **SSL/TLS**: Required for all connections

##### Access Control
- **IAM Database Authentication**: Available
- **Parameter Groups**: Optimized for security
- **Encryption**: Enabled at rest and in transit

## SSL Configuration

### 1. Let's Encrypt Setup

```bash
# Install Certbot
sudo apt install certbot python3-certbot-nginx

# Get SSL certificate
sudo certbot --nginx -d your-domain.com -d api.your-domain.com

# Auto-renewal
sudo crontab -e
# Add: 0 12 * * * /usr/bin/certbot renew --quiet
```

### 2. Nginx SSL Configuration

```nginx
# Redirect HTTP to HTTPS
server {
    listen 80;
    server_name your-domain.com api.your-domain.com;
    return 301 https://$server_name$request_uri;
}

# HTTPS Backend
server {
    listen 443 ssl http2;
    server_name api.your-domain.com;
    
    ssl_certificate /etc/letsencrypt/live/api.your-domain.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/api.your-domain.com/privkey.pem;
    
    # SSL configuration
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers ECDHE-RSA-AES128-GCM-SHA256:ECDHE-RSA-AES256-GCM-SHA384;
    ssl_prefer_server_ciphers off;
    
    # Rest of backend configuration...
}

# HTTPS Frontend
server {
    listen 443 ssl http2;
    server_name your-domain.com;
    
    ssl_certificate /etc/letsencrypt/live/your-domain.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/your-domain.com/privkey.pem;
    
    # Rest of frontend configuration...
}
```

## Continuous Deployment

### 1. Deployment Script

Create `deploy.sh`:

```bash
#!/bin/bash

# Backend deployment
echo "Deploying backend..."
cd /var/www/mcnary_backend
git pull origin main
composer install --no-dev --optimize-autoloader
php bin/console cache:clear --env=prod
php bin/console cache:warmup --env=prod
php bin/console doctrine:migrations:migrate --env=prod --no-interaction

# Frontend deployment
echo "Deploying frontend..."
cd /var/www/mcnary_frontend
git pull origin main
npm ci
npm run build
sudo systemctl reload nginx

echo "Deployment complete!"
```

### 2. GitHub Actions

Create `.github/workflows/deploy.yml`:

```yaml
name: Deploy to Production

on:
  push:
    branches: [main]

jobs:
  deploy:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v3
      
      - name: Deploy to server
        uses: appleboy/ssh-action@v0.1.5
        with:
          host: ${{ secrets.HOST }}
          username: ${{ secrets.USERNAME }}
          key: ${{ secrets.KEY }}
          script: |
            cd /var/www/mcnary_technical_backend
            ./deploy.sh
```

## Monitoring & Maintenance

### 1. Log Monitoring

```bash
# Monitor backend logs
tail -f /var/log/nginx/mcnary_backend_error.log
tail -f /var/www/mcnary_backend/var/log/prod.log

# Monitor frontend logs
tail -f /var/log/nginx/mcnary_frontend_error.log
```

### 2. Performance Monitoring

```bash
# Check PHP-FPM status
php-fpm8.2 -t
sudo systemctl status php8.2-fpm

# Check Nginx status
sudo nginx -t
sudo systemctl status nginx

# Monitor system resources
htop
df -h
free -h
```

### 3. Database Maintenance

```bash
# Backup database
pg_dump -h localhost -U username database_name > backup_$(date +%Y%m%d_%H%M%S).sql

# Check database size
psql -h localhost -U username -d database_name -c "SELECT pg_size_pretty(pg_database_size(current_database()));"

# Analyze tables
psql -h localhost -U username -d database_name -c "ANALYZE;"
```

### 4. CloudWatch Integration (RDS)

The deployment automatically configures:

#### Metrics
- **CPU Utilization** - Alert at 80%
- **Freeable Memory** - Alert below 100MB
- **Free Storage Space** - Alert below 2GB
- **Database Connections** - Monitor active connections
- **Read/Write IOPS** - Performance monitoring

#### Logs
- **PostgreSQL Logs** - Query and error logs
- **Performance Insights** - Query performance analysis
- **Enhanced Monitoring** - OS-level metrics

### 5. Custom Monitoring

```bash
# Check database performance
php bin/console doctrine:query:sql "
SELECT 
  schemaname,
  tablename,
  indexname,
  idx_scan,
  idx_tup_read,
  idx_tup_fetch
FROM pg_stat_user_indexes 
ORDER BY idx_scan DESC;
"

# Monitor slow queries
php bin/console doctrine:query:sql "
SELECT 
  query,
  calls,
  total_time,
  mean_time,
  rows
FROM pg_stat_statements 
ORDER BY mean_time DESC 
LIMIT 10;
"
```

### 6. Backup Management

#### Automated Backups
- **Retention**: 7 days (configurable)
- **Window**: 03:00-04:00 UTC
- **Point-in-time Recovery**: Available

#### Manual Snapshots
```bash
# Create manual snapshot
aws rds create-db-snapshot \
  --db-instance-identifier "counselrank-prod-db" \
  --db-snapshot-identifier "manual-backup-$(date +%Y%m%d-%H%M%S)"

# List snapshots
aws rds describe-db-snapshots \
  --db-instance-identifier "counselrank-prod-db"
```

### 7. Performance Tuning

#### Parameter Group Optimization
```bash
# Update parameter group
aws rds modify-db-parameter-group \
  --db-parameter-group-name "counselrank-postgres-16" \
  --parameters "ParameterName=shared_buffers,ParameterValue=512MB,ApplyMethod=pending-reboot"
```

#### Connection Pooling
Consider implementing connection pooling for high-traffic applications:
- **PgBouncer** - Lightweight connection pooler
- **AWS RDS Proxy** - Managed connection pooling

### 8. Scaling Operations

#### Vertical Scaling (Instance Class)
```bash
# Modify instance class
aws rds modify-db-instance \
  --db-instance-identifier "counselrank-prod-db" \
  --db-instance-class "db.t3.large" \
  --apply-immediately
```

#### Storage Scaling
Storage automatically scales up to the maximum allocated storage.

## Troubleshooting

### Common Issues

1. **500 Internal Server Error**
   - Check PHP error logs
   - Verify file permissions
   - Check database connection

2. **Database Connection Failed**
   - Verify database credentials
   - Check firewall settings
   - Ensure database server is running

3. **CORS Issues**
   - Verify CORS configuration in `.env`
   - Check frontend API base URL
   - Ensure SSL certificates are valid

4. **Performance Issues**
   - Enable OPcache for PHP
   - Configure Nginx caching
   - Optimize database queries

5. **Connection Issues (RDS)**
   ```bash
   # Test connection
   psql -h your-rds-endpoint.region.rds.amazonaws.com \
        -p 5432 \
        -U counselrank_admin \
        -d counselrank_prod
   
   # Check security groups
   aws ec2 describe-security-groups \
     --group-names "counselrank-db-sg"
   ```

6. **Performance Issues (RDS)**
   ```bash
   # Check slow queries
   php bin/console doctrine:query:sql "
   SELECT 
     query,
     calls,
     total_time,
     mean_time
   FROM pg_stat_statements 
   WHERE mean_time > 1000
   ORDER BY mean_time DESC;
   "
   
   # Analyze table statistics
   php bin/console doctrine:query:sql "ANALYZE;"
   ```

7. **Migration Issues**
   ```bash
   # Check migration status
   php bin/console doctrine:migrations:status
   
   # Rollback last migration
   php bin/console doctrine:migrations:migrate prev
   
   # Force migration
   php bin/console doctrine:migrations:migrate --no-interaction
   ```

### Debug Commands

```bash
# Check PHP configuration
php -m | grep -E "(pdo|xml|mbstring|curl|zip|intl|opcache)"

# Check Nginx configuration
sudo nginx -t

# Check PHP-FPM configuration
php-fpm8.2 -t

# Check database connection
php bin/console doctrine:query:sql 'SELECT version()' --env=prod
```

### Log Analysis

#### RDS Logs
```bash
# Download PostgreSQL logs
aws rds download-db-log-file-portion \
  --db-instance-identifier "counselrank-prod-db" \
  --log-file-name "postgresql.log" \
  --starting-token 0 \
  --max-items 1000
```

#### Application Logs
```bash
# Check Symfony logs
tail -f var/log/prod.log

# Check Doctrine query logs
tail -f var/log/doctrine.log
```

## Cost Optimization

### Instance Right-sizing
- **Monitor CPU/Memory usage** for 2-4 weeks
- **Use CloudWatch metrics** to identify optimal instance class
- **Consider Reserved Instances** for production workloads

### Storage Optimization
- **Enable storage autoscaling** to avoid over-provisioning
- **Monitor storage usage** and adjust accordingly
- **Use General Purpose SSD** for most workloads

### Backup Optimization
- **Adjust retention period** based on compliance requirements
- **Use automated backups** instead of manual snapshots for regular backups
- **Delete old snapshots** to reduce storage costs

## Disaster Recovery

### Backup Strategy
1. **Automated Backups** - Daily backups with 7-day retention
2. **Manual Snapshots** - Before major changes
3. **Cross-region Snapshots** - For disaster recovery
4. **Point-in-time Recovery** - Restore to any point within retention period

### Recovery Procedures
```bash
# Restore from snapshot
aws rds restore-db-instance-from-db-snapshot \
  --db-instance-identifier "counselrank-recovery-db" \
  --db-snapshot-identifier "backup-snapshot-20250101"

# Point-in-time recovery
aws rds restore-db-instance-to-point-in-time \
  --source-db-instance-identifier "counselrank-prod-db" \
  --target-db-instance-identifier "counselrank-recovery-db" \
  --restore-time "2025-01-01T12:00:00Z"
```

## Security Checklist

- [ ] SSL certificates installed and auto-renewing
- [ ] Firewall configured (UFW)
- [ ] Database user has minimal required permissions
- [ ] JWT keys generated and secured
- [ ] Environment variables properly set
- [ ] File permissions restricted
- [ ] CORS origins limited to production domains
- [ ] Debug mode disabled in production
- [ ] Regular security updates applied
- [ ] Database backups automated

## Next Steps

After successful deployment:

1. **Test all API endpoints** to ensure functionality
2. **Monitor performance** and set up alerts
3. **Configure backup automation** for database and files
4. **Set up monitoring tools** (New Relic, DataDog, etc.)
5. **Document deployment procedures** for team members

## Related Documentation

- [Authentication Guide](./AUTHENTICATION_GUIDE.md) - Complete authentication system
- [API Documentation](./API_DOCUMENTATION.md) - Complete API reference
- [Database Guide](./DATABASE_GUIDE.md) - Database setup and management
- [Setup Guide](./SETUP_GUIDE.md) - Development setup guide

---

**Last Updated:** January 2025  
**Maintained By:** Development Team  
**Status:** Complete and consolidated ✅