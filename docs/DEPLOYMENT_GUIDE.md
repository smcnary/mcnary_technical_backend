# 🚀 Deployment Guide

## 📋 Overview

This guide covers deploying both the Symfony backend and React frontend applications to production, including server setup, database configuration, build optimization, and deployment best practices.

## 🏗️ Prerequisites

### Backend Requirements
- Production server with PHP 8.2+ and required extensions
- PostgreSQL database server
- Web server (Nginx/Apache)
- Git access to your repository
- SSH access to production server

### Frontend Requirements
- Node.js 18+ and npm
- Web server (Nginx/Apache) or CDN service
- Access to your backend API
- Domain name and SSL certificate (recommended)

## 🖥️ Server Setup

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

## 🚀 Backend Deployment

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

## 🎨 Frontend Deployment

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

## 🔒 SSL Configuration

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

## 🔄 Continuous Deployment

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

## 📊 Monitoring & Maintenance

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

## 🆘 Troubleshooting

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

## 🔒 Security Checklist

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

## 📚 Next Steps

After successful deployment:

1. **Test all API endpoints** to ensure functionality
2. **Monitor performance** and set up alerts
3. **Configure backup automation** for database and files
4. **Set up monitoring tools** (New Relic, DataDog, etc.)
5. **Document deployment procedures** for team members

For more detailed information, refer to:
- **[DATABASE_GUIDE.md](./DATABASE_GUIDE.md)** - Database setup and management
- **[API_REFERENCE.md](./API_REFERENCE.md)** - API documentation and testing
- **[QUICK_START.md](./QUICK_START.md)** - Development setup guide
