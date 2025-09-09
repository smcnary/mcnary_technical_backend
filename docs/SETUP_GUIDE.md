# Complete Setup Guide

## Overview

This comprehensive guide covers the complete setup process for CounselRank.legal, including development, staging, and production environments.

## Table of Contents

1. [Prerequisites](#prerequisites)
2. [Quick Start](#quick-start)
3. [Environment Configuration](#environment-configuration)
4. [Database Setup](#database-setup)
5. [Development Environment](#development-environment)
6. [Staging Environment](#staging-environment)
7. [Production Environment](#production-environment)
8. [Environment Switching](#environment-switching)
9. [Build Scripts](#build-scripts)
10. [Troubleshooting](#troubleshooting)

## Prerequisites

### System Requirements
- **PHP 8.2+** with extensions: `pdo_pgsql`, `mbstring`, `intl`, `xml`, `zip`, `gd`, `curl`, `iconv`
- **Node.js 18+** and npm
- **PostgreSQL 16+** running locally
- **Composer** (PHP package manager)
- **Git**

### Installation Commands
```bash
# macOS users
brew install php node postgresql composer

# Ubuntu/Debian users
sudo apt-get install php8.2 php8.2-pgsql php8.2-mbstring php8.2-intl php8.2-xml php8.2-zip php8.2-gd php8.2-curl php8.2-iconv nodejs npm postgresql composer

# Windows users
# Install via Chocolatey or download from official websites
```

## Quick Start

### Step 1: Clone and Setup
```bash
# Clone the repository
git clone <your-repo-url>
cd mcnary_technical_backend

# Copy and configure backend environment
cd backend
cp .env .env.local
# Edit .env.local with your PostgreSQL credentials
cd ..
```

### Step 2: Install Dependencies
```bash
# Backend dependencies
cd backend
composer install
cd ..

# Frontend dependencies
cd frontend
npm install
cd ..
```

### Step 3: Start Applications

#### Option A: Automatic (Recommended)
```bash
# macOS users
./dev-start-macos.sh

# Linux/Windows users
./dev-start.sh
```

#### Option B: Manual
```bash
# Terminal 1 - Backend
cd backend
symfony server:start --port=8000

# Terminal 2 - Frontend
cd frontend
npm run dev
```

### Step 4: First Time Setup
```bash
cd backend

# Create database
php bin/console doctrine:database:create

# Run migrations
php bin/console doctrine:migrations:migrate

# Create system account
php bin/console app:create-system-account --username=admin --display-name="System Administrator" --permissions=read,write,admin

# Generate JWT keys (if needed)
php bin/console lexik:jwt:generate-keypair --overwrite
```

## Environment Configuration

### Environment Files
- `env.dev` - Development environment template
- `env.prod` - Production environment template
- `env.rds-staging` - RDS staging environment template
- `env.rds-production` - RDS production environment template
- `env.db-setup` - Database setup configuration
- `.env.local` - Your actual environment configuration (created from templates)

### Environment Variables Reference

#### Development Environment (`env.dev`)
```bash
APP_ENV=dev
APP_DEBUG=true
APP_SECRET=dev-secret-key-change-in-production

# Local Database Configuration
DATABASE_URL="postgresql://smcnary:TulsaSeo122@127.0.0.1:5432/tulsa_seo?serverVersion=16&charset=utf8"

# Google OAuth Configuration (Development)
GOOGLE_OAUTH_CLIENT_ID="your_google_oauth_client_id"
GOOGLE_OAUTH_CLIENT_SECRET="your_google_oauth_client_secret"
GOOGLE_OAUTH_REDIRECT_URI="http://localhost:8000/api/v1/auth/google/callback"

# Microsoft OAuth Configuration (Development)
MICROSOFT_OAUTH_CLIENT_ID="your_microsoft_oauth_client_id"
MICROSOFT_OAUTH_CLIENT_SECRET="your_microsoft_oauth_client_secret"
MICROSOFT_OAUTH_REDIRECT_URI="http://localhost:8000/api/v1/auth/microsoft/callback"

# Frontend URL (Development)
APP_FRONTEND_URL="http://localhost:3000"

# JWT Configuration (Development)
JWT_SECRET_KEY=%kernel.project_dir%/config/jwt/private.pem
JWT_PUBLIC_KEY=%kernel.project_dir%/config/jwt/public.pem
JWT_PASSPHRASE=dev-passphrase

# CORS Configuration (Development)
CORS_ALLOW_ORIGIN="^http://localhost:3000$"

# Logging (Development)
MONOLOG_LEVEL=DEBUG

# Cache (Development)
CACHE_DRIVER=file
```

#### Production Environment (`env.prod`)
```bash
APP_ENV=prod
APP_DEBUG=false
APP_SECRET=your-production-secret-key-change-this

# Production Database Configuration
DATABASE_URL="postgresql://smcnary:TulsaSeo122@host:5432/tulsa_seo?serverVersion=16&charset=utf8"

# Google OAuth Configuration (Production)
GOOGLE_OAUTH_CLIENT_ID="your_google_oauth_client_id"
GOOGLE_OAUTH_CLIENT_SECRET="your_google_oauth_client_secret"
GOOGLE_OAUTH_REDIRECT_URI="https://api.your-domain.com/api/v1/auth/google/callback"

# Microsoft OAuth Configuration (Production)
MICROSOFT_OAUTH_CLIENT_ID="your_microsoft_oauth_client_id"
MICROSOFT_OAUTH_CLIENT_SECRET="your_microsoft_oauth_client_secret"
MICROSOFT_OAUTH_REDIRECT_URI="https://api.your-domain.com/api/v1/auth/microsoft/callback"

# Frontend URL (Production)
APP_FRONTEND_URL="https://your-domain.com"

# JWT Configuration (Production)
JWT_SECRET_KEY=%kernel.project_dir%/config/jwt/private.pem
JWT_PUBLIC_KEY=%kernel.project_dir%/config/jwt/public.pem
JWT_PASSPHRASE=your-production-passphrase

# CORS Configuration (Production)
CORS_ALLOW_ORIGIN="^https://your-domain\.com$"

# Logging (Production)
MONOLOG_LEVEL=WARNING

# Cache (Production)
CACHE_DRIVER=file

# Security (Production)
SECURITY_STRICT_TRANSPORT_SECURITY=true
SECURITY_CONTENT_TYPE_NOSNIFF=true
SECURITY_X_FRAME_OPTIONS=DENY
SECURITY_X_CONTENT_TYPE_OPTIONS=nosniff
```

## Database Setup

### Local PostgreSQL Setup
```bash
# Connect to PostgreSQL
psql -U postgres -h localhost

# Create database
CREATE DATABASE tulsa_seo;

# Create user (optional)
CREATE USER smcnary WITH PASSWORD 'TulsaSeo122';
GRANT ALL PRIVILEGES ON DATABASE tulsa_seo TO smcnary;
```

### Docker Database Setup
```bash
# Start Docker Compose database
cd backend
docker-compose up -d

# Database will be available at:
# Host: localhost
# Port: 5434
# Database: tulsa_seo
# User: smcnary
# Password: TulsaSeo122
```

### RDS Database Setup
```bash
# Use the RDS deployment scripts
cd backend/scripts
./deploy-rds.sh --staging
./deploy-rds.sh --production

# Test RDS connection
./test-rds-connection.sh --staging
./test-rds-connection.sh --production
```

## Development Environment

### Setup Process
```bash
# Switch to development environment
cd backend
./scripts/setup-env.sh --dev

# Build and start development environment
./build-dev.sh

# Start development server
php bin/console server:start 0.0.0.0:8000
```

### Development Features
- Hot reloading enabled
- Debug mode active
- Verbose logging
- Development OAuth URLs
- Local database connection

### Testing Development
```bash
# Test API endpoint
curl http://localhost:8000/api/v1/health

# Test database connection
php bin/console doctrine:query:sql "SELECT 1"

# Test JWT generation
php bin/console lexik:jwt:generate-keypair --overwrite
```

## Staging Environment

### RDS Staging Setup
```bash
# Deploy RDS staging instance
cd backend/scripts
./deploy-rds.sh --staging

# Switch to RDS staging environment
cd ..
./scripts/setup-env.sh --rds-staging

# Test staging environment
php bin/console server:start 0.0.0.0:8000
```

### Staging Features
- RDS PostgreSQL database
- Production-like configuration
- Staging OAuth URLs
- Performance monitoring

## Production Environment

### Setup Process
```bash
# Switch to production environment
cd backend
./scripts/setup-env.sh --prod

# Build production environment
./build-prod.sh

# Start production server
APP_ENV=prod php bin/console server:start 0.0.0.0:8000
```

### Production Features
- Optimized for performance
- Security headers enabled
- Production OAuth URLs
- Warning-level logging
- HTTPS enforcement

### Production Checklist
- [ ] Update `APP_SECRET` with secure value
- [ ] Update `JWT_PASSPHRASE` with secure value
- [ ] Configure production OAuth URLs
- [ ] Set up HTTPS certificates
- [ ] Configure production database
- [ ] Enable security headers
- [ ] Set up monitoring and logging
- [ ] Configure backup strategy

## Environment Switching

### Using Setup Script
```bash
cd backend

# Switch to development
./scripts/setup-env.sh --dev

# Switch to staging
./scripts/setup-env.sh --rds-staging

# Switch to production
./scripts/setup-env.sh --prod

# Test current environment
./scripts/setup-env.sh --test-connection
```

### Manual Switching
```bash
# Copy environment template
cp env.dev .env.local        # Development
cp env.prod .env.local       # Production
cp env.rds-staging .env.local # RDS Staging
cp env.rds-production .env.local # RDS Production

# Update .env.local with your specific values
```

## Build Scripts

### Development Build (`build-dev.sh`)
- Installs all dependencies (including dev dependencies)
- Sets up development environment
- Generates JWT keys
- Starts Docker database (if available)
- Sets up database user with admin privileges
- Clears and warms up development cache
- Runs database migrations

### Production Build (`build-prod.sh`)
- Installs only production dependencies
- Sets up production environment
- Generates JWT keys
- Clears and warms up production cache
- Runs database migrations
- Sets proper file permissions

### Database User Setup (`setup-db-user.sh`)
- Creates `tulsa_seo` database
- Creates `smcnary` user with password `TulsaSeo122`
- Grants system admin privileges
- Creates system admin user in Symfony
- Tests database connectivity

## Troubleshooting

### Common Issues

#### 1. Permission Denied
```bash
chmod +x *.sh
chmod +x scripts/*.sh
```

#### 2. Database Connection Failed
- Check PostgreSQL is running: `sudo systemctl status postgresql` (Linux) or `brew services start postgresql` (macOS)
- Verify connection string in `.env.local`
- Check firewall settings
- Ensure database exists and user has proper permissions

#### 3. Port Already in Use
- Backend: `symfony server:stop` then restart
- Frontend: Kill process on port 5173 or change port in `vite.config.ts`

#### 4. CORS Issues
- Ensure backend CORS configuration allows frontend origin
- Check `CORS_ALLOW_ORIGIN` in backend `.env.local`

#### 5. JWT Issues
- Generate JWT keys: `php bin/console lexik:jwt:generate-keypair`
- Check JWT configuration in `config/packages/lexik_jwt_authentication.yaml`

#### 6. Cache Issues
```bash
php bin/console cache:clear --env=dev
php bin/console cache:clear --env=prod
```

#### 7. Migration Issues
```bash
# Check migration status
php bin/console doctrine:migrations:status

# Run migrations
php bin/console doctrine:migrations:migrate

# Validate schema
php bin/console doctrine:schema:validate
```

### Useful Commands

```bash
# Backend
cd backend
php bin/console cache:clear
php bin/console debug:router
php bin/console doctrine:schema:validate
php bin/console doctrine:migrations:status

# Frontend
cd frontend
npm run build
npm run preview
npm run test
```

### Debug Mode

Enable debug mode in development to see detailed error messages:
```yaml
# config/packages/dev/framework.yaml
debug: true
```

## Environment Checklist

### Development Setup
- [ ] Run `./scripts/setup-env.sh --dev`
- [ ] Update `.env.local` with credentials
- [ ] Run `./build-dev.sh`
- [ ] Start server: `php bin/console server:start 0.0.0.0:8000`
- [ ] Test API endpoint: `curl http://localhost:8000/api/v1/health`

### Staging Setup
- [ ] Deploy RDS staging: `./scripts/deploy-rds.sh --staging`
- [ ] Run `./scripts/setup-env.sh --rds-staging`
- [ ] Test staging environment
- [ ] Verify OAuth configuration

### Production Setup
- [ ] Run `./scripts/setup-env.sh --prod`
- [ ] Update `.env.local` with production credentials
- [ ] Change default secrets
- [ ] Run `./build-prod.sh`
- [ ] Start server: `APP_ENV=prod php bin/console server:start 0.0.0.0:8000`
- [ ] Test all API endpoints
- [ ] Verify security settings

## Related Documentation

- [Authentication Guide](./AUTHENTICATION_GUIDE.md) - Complete authentication system
- [OAuth Setup](./OAUTH_SETUP.md) - OAuth provider configuration
- [Database Guide](./DATABASE_GUIDE.md) - Database setup and management
- [API Documentation](./API_DOCUMENTATION.md) - Complete API reference
- [Deployment Guide](./DEPLOYMENT_GUIDE.md) - Production deployment

---

**Last Updated:** September 9, 2025  
**Maintained By:** Development Team  
**Status:** Complete and consolidated ✅
