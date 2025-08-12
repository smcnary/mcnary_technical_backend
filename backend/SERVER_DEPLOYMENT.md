# 🚀 Server Deployment Guide

This guide covers deploying the Symfony backend application to production servers, including database migrations, environment configuration, and deployment best practices.

## 📋 Prerequisites

- Production server with PHP 8.1+ and required extensions
- PostgreSQL database server
- Web server (Nginx/Apache)
- Git access to your repository
- SSH access to production server

## 🏗️ Server Setup

### 1. Install Required Software

```bash
# Update system packages
sudo apt update && sudo apt upgrade -y

# Install PHP and extensions
sudo apt install php8.1-fpm php8.1-cli php8.1-pgsql php8.1-xml php8.1-mbstring php8.1-curl php8.1-zip php8.1-intl php8.1-opcache

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Install Node.js (for asset compilation if needed)
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt-get install -y nodejs
```

### 2. Install and Configure Web Server

#### Nginx Configuration

```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /var/www/mcnary_backend/public;
    index index.php;

    location / {
        try_files $uri /index.php$is_args$args;
    }

    location ~ ^/index\.php(/|$) {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_split_path_info ^(.+\.php)(/.*)$;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        fastcgi_param DOCUMENT_ROOT $realpath_root;
        internal;
    }

    location ~ \.php$ {
        return 404;
    }

    error_log /var/log/nginx/mcnary_backend_error.log;
    access_log /var/log/nginx/mcnary_backend_access.log;
}
```

#### Apache Configuration

```apache
<VirtualHost *:80>
    ServerName your-domain.com
    DocumentRoot /var/www/mcnary_backend/public
    
    <Directory /var/www/mcnary_backend/public>
        AllowOverride All
        Require all granted
        FallbackResource /index.php
    </Directory>
    
    ErrorLog ${APACHE_LOG_DIR}/mcnary_backend_error.log
    CustomLog ${APACHE_LOG_DIR}/mcnary_backend_access.log combined
</VirtualHost>
```

## 🚀 Deployment Process

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

Create production `.env` file:

```bash
# Database
DATABASE_URL="postgresql://username:password@localhost:5432/mcnary_marketing?serverVersion=16&charset=utf8"

# Application
APP_ENV=prod
APP_DEBUG=false
APP_SECRET=your-super-secret-key-here

# JWT
JWT_SECRET_KEY=your-jwt-secret-key
JWT_PASSPHRASE=your-jwt-passphrase

# CORS
CORS_ALLOW_ORIGIN=https://your-frontend-domain.com
```

### 3. Database Setup

```bash
# Create production database
sudo -u postgres createdb mcnary_marketing

# Run migrations
bin/console doctrine:migrations:migrate --env=prod --no-interaction

# Load initial data (if any)
bin/console doctrine:fixtures:load --env=prod --no-interaction
```

### 4. Cache and Permissions

```bash
# Clear and warm up cache
bin/console cache:clear --env=prod
bin/console cache:warmup --env=prod

# Set proper permissions
sudo chown -R www-data:www-data var/
sudo chmod -R 755 var/
sudo chmod -R 777 var/cache/ var/logs/
```

## 🔄 Continuous Deployment

### 1. Automated Deployment Script

Create `deploy.sh`:

```bash
#!/bin/bash
set -e

echo "🚀 Starting deployment..."

# Pull latest changes
git pull origin main

# Install/update dependencies
composer install --no-dev --optimize-autoloader

# Run database migrations
bin/console doctrine:migrations:migrate --env=prod --no-interaction

# Clear and warm up cache
bin/console cache:clear --env=prod
bin/console cache:warmup --env=prod

# Set permissions
sudo chown -R www-data:www-data var/
sudo chmod -R 755 var/
sudo chmod -R 777 var/cache/ var/logs/

# Restart services
sudo systemctl reload php8.1-fpm
sudo systemctl reload nginx

echo "✅ Deployment completed successfully!"
```

### 2. GitHub Actions Workflow

Create `.github/workflows/deploy.yml`:

```yaml
name: Deploy to Production

on:
  push:
    branches: [ main ]

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
            cd /var/www/mcnary_backend
            ./deploy.sh
```

## 🔒 Security Configuration

### 1. SSL/TLS Setup

```bash
# Install Certbot
sudo apt install certbot python3-certbot-nginx

# Obtain SSL certificate
sudo certbot --nginx -d your-domain.com

# Auto-renewal
sudo crontab -e
# Add: 0 12 * * * /usr/bin/certbot renew --quiet
```

### 2. Security Headers

Add to Nginx configuration:

```nginx
# Security headers
add_header X-Frame-Options "SAMEORIGIN" always;
add_header X-XSS-Protection "1; mode=block" always;
add_header X-Content-Type-Options "nosniff" always;
add_header Referrer-Policy "no-referrer-when-downgrade" always;
add_header Content-Security-Policy "default-src 'self' http: https: data: blob: 'unsafe-inline'" always;
```

## 📊 Monitoring and Maintenance

### 1. Health Checks

```bash
# Check application status
curl -f https://your-domain.com/api/health

# Check database connection
bin/console doctrine:query:sql "SELECT 1" --env=prod

# Monitor logs
tail -f var/logs/prod.log
```

### 2. Backup Strategy

```bash
# Database backup script
#!/bin/bash
BACKUP_DIR="/backups/database"
DATE=$(date +%Y%m%d_%H%M%S)
pg_dump mcnary_marketing > "$BACKUP_DIR/backup_$DATE.sql"

# Keep only last 7 days
find $BACKUP_DIR -name "backup_*.sql" -mtime +7 -delete
```

### 3. Performance Optimization

```bash
# Enable OPcache
sudo nano /etc/php/8.1/fpm/conf.d/10-opcache.ini

# Add:
opcache.enable=1
opcache.memory_consumption=128
opcache.interned_strings_buffer=8
opcache.max_accelerated_files=4000
opcache.revalidate_freq=2
opcache.fast_shutdown=1
```

## 🚨 Troubleshooting

### Common Issues

1. **500 Internal Server Error:**
   - Check PHP-FPM logs: `sudo tail -f /var/log/php8.1-fpm.log`
   - Verify file permissions
   - Check Symfony cache: `bin/console cache:clear --env=prod`

2. **Database Connection Issues:**
   - Verify PostgreSQL service: `sudo systemctl status postgresql`
   - Check connection string in `.env`
   - Verify firewall settings

3. **Permission Denied:**
   - Check file ownership: `sudo chown -R www-data:www-data /var/www/mcnary_backend`
   - Verify directory permissions: `sudo chmod -R 755 /var/www/mcnary_backend`

## 📚 Additional Resources

- [Symfony Production Best Practices](https://symfony.com/doc/current/deployment.html)
- [Nginx Configuration Guide](https://nginx.org/en/docs/)
- [PostgreSQL Administration](https://www.postgresql.org/docs/current/admin.html)
- [Let's Encrypt Documentation](https://letsencrypt.org/docs/)

---

**Happy deploying! 🚀**
