# Complete Setup Guide

This comprehensive guide covers setting up the CounselRank.legal application for development, staging, and production environments.

## Table of Contents

1. [Quick Start](#quick-start)
2. [Prerequisites](#prerequisites)
3. [Environment Setup](#environment-setup)
4. [Database Configuration](#database-configuration)
5. [Development Environment](#development-environment)
6. [Staging Environment](#staging-environment)
7. [Production Environment](#production-environment)
8. [Environment Variables](#environment-variables)
9. [Build Scripts](#build-scripts)
10. [Troubleshooting](#troubleshooting)

## Quick Start

### Get Both Applications Running in 3 Steps

#### Prerequisites
- **PHP 8.2+** with extensions: `pdo_pgsql`, `mbstring`, `intl`, `xml`, `zip`, `gd`, `curl`, `iconv`
- **Node.js 18+** and npm
- **PostgreSQL 16+** running locally
- **Composer** (PHP package manager)
- **Git**

**macOS users**: Install with `brew install php node postgresql composer`

#### Step 1: Setup Environment
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

#### Step 2: Install Dependencies
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

#### Step 3: Start Both Applications

##### Option A: Automatic (Recommended)
```bash
# macOS users
./dev-start-macos.sh

# Linux/Windows users
./dev-start.sh
```

##### Option B: Manual
```bash
# Terminal 1 - Backend
cd backend
symfony server:start --port=8000

# Terminal 2 - Frontend
cd frontend
npm run dev
```

### What You Get
- **Frontend**: http://localhost:5173 (React + Vite)
- **Backend API**: http://localhost:8000 (Symfony + API Platform)
- **API Docs**: http://localhost:8000/api

## Prerequisites

### System Requirements
- **PHP 8.2+** with required extensions
- **Node.js 18+** and npm
- **PostgreSQL 16+**
- **Composer** (PHP package manager)
- **Git**

### Required PHP Extensions
```bash
# Check installed extensions
php -m | grep -E "(pdo_pgsql|mbstring|intl|xml|zip|gd|curl|iconv)"
```

### Installation Commands

#### macOS (using Homebrew)
```bash
brew install php node postgresql composer
```

#### Ubuntu/Debian
```bash
sudo apt update
sudo apt install php8.2 php8.2-pgsql php8.2-mbstring php8.2-intl php8.2-xml php8.2-zip php8.2-gd php8.2-curl php8.2-iconv composer nodejs npm postgresql postgresql-contrib
```

#### Windows
- Install PHP from [php.net](https://www.php.net/downloads.php)
- Install Node.js from [nodejs.org](https://nodejs.org/)
- Install PostgreSQL from [postgresql.org](https://www.postgresql.org/download/windows/)
- Install Composer from [getcomposer.org](https://getcomposer.org/download/)

## Environment Setup

### Environment Files Overview
- `env.dev` - Development environment template
- `env.prod` - Production environment template
- `env.rds-staging` - RDS staging environment template
- `env.rds-production` - RDS production environment template
- `env.db-setup` - Database setup configuration
- `.env.local` - Your actual environment configuration (created from templates)

### Quick Setup Commands

#### Option 1: Local Development with Docker
```bash
cd backend
./scripts/setup-env.sh --local
```

#### Option 2: Local Development with RDS Staging
```bash
cd backend
./scripts/setup-env.sh --rds-staging
```

#### Option 3: Production with RDS Production
```bash
cd backend
./scripts/setup-env.sh --rds-production
```

#### Test Database Connection
```bash
cd backend
./scripts/setup-env.sh --test-connection
```

### Environment Switching
Use the `switch-env.sh` script to easily switch between environments:

```bash
# Switch to development
./switch-env.sh dev

# Switch to production
./switch-env.sh prod

# Switch to cloud/RDS
./switch-env.sh cloud
```

## Database Configuration

### Local PostgreSQL Setup
```bash
# Connect to PostgreSQL
psql -U postgres -h localhost

# Create database
CREATE DATABASE mcnary_marketing;

# Create user (optional)
CREATE USER mcnary_user WITH PASSWORD 'your_password';
GRANT ALL PRIVILEGES ON DATABASE mcnary_marketing TO mcnary_user;
```

### Database URLs by Environment

#### Local Docker Database
```bash
DATABASE_URL="postgresql://smcnary:TulsaSeo122@127.0.0.1:5432/tulsa_seo?serverVersion=16&charset=utf8"
```

#### RDS Staging Database
```bash
DATABASE_URL="postgresql://counselrank_admin:TulsaSeo122@counselrank-staging-db.cstm2wakq0zs.us-east-1.rds.amazonaws.com:5432/counselrank_staging?serverVersion=16&charset=utf8"
```

#### RDS Production Database
```bash
DATABASE_URL="postgresql://counselrank_admin:YOUR_PASSWORD@your-rds-endpoint.region.rds.amazonaws.com:5432/counselrank_prod?serverVersion=16&charset=utf8"
```

### First Time Database Setup
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

## Development Environment

### Development Build (`build-dev.sh`)
- Installs all dependencies (including dev dependencies)
- Sets up development environment
- Generates JWT keys
- Starts Docker database (if available)
- Sets up smcnary user with admin privileges
- Clears and warms up development cache
- Runs database migrations

### Development Configuration
```bash
# Switch to development environment
./switch-env.sh dev

# Build and start development environment
./build-dev.sh

# Start development server
php bin/console server:start 0.0.0.0:8000
```

### Development Environment Variables
```env
APP_ENV=dev
APP_DEBUG=true
APP_SECRET=your-secret-key-here
DATABASE_URL="postgresql://username:password@127.0.0.1:5432/mcnary_marketing?serverVersion=16&charset=utf8"
JWT_SECRET_KEY=%kernel.project_dir%/config/jwt/private.pem
JWT_PUBLIC_KEY=%kernel.project_dir%/config/jwt/public.pem
JWT_PASSPHRASE=ChangeThis
CORS_ALLOW_ORIGIN="^https?://(localhost|127\.0\.0\.1)(:[0-9]+)?$"
```

### Frontend API Configuration
```typescript
// src/services/api.ts
const API_BASE_URL = 'http://localhost:8000';
```

## Staging Environment

### RDS Staging Configuration
- **Instance ID**: `counselrank-staging-db`
- **Endpoint**: `counselrank-staging-db.cstm2wakq0zs.us-east-1.rds.amazonaws.com:5432`
- **Database**: `counselrank_staging`
- **Username**: `counselrank_admin`
- **Password**: `TulsaSeo122`
- **Status**: Available (publicly accessible)

### Staging Setup
```bash
# Setup staging environment
./scripts/setup-env.sh --rds-staging

# Test connection
./scripts/setup-env.sh --test-connection

# Run migrations
php bin/console doctrine:migrations:migrate --no-interaction
```

## Production Environment

### Production Build (`build-prod.sh`)
- Installs only production dependencies
- Sets up production environment
- Generates JWT keys
- Clears and warms up production cache
- Runs database migrations
- Sets proper file permissions

### Production Configuration
```bash
# Switch to production environment
./switch-env.sh prod

# Build production environment
./build-prod.sh

# Start production server
APP_ENV=prod php bin/console server:start 0.0.0.0:8000
```

### Production Environment Variables
```env
APP_ENV=prod
APP_DEBUG=false
APP_SECRET=your-production-secret-key
DATABASE_URL="postgresql://counselrank_admin:YOUR_PASSWORD@your-rds-endpoint.region.rds.amazonaws.com:5432/counselrank_prod?serverVersion=16&charset=utf8"
JWT_SECRET_KEY=%kernel.project_dir%/config/jwt/private.pem
JWT_PUBLIC_KEY=%kernel.project_dir%/config/jwt/public.pem
JWT_PASSPHRASE=your-production-passphrase
CORS_ALLOW_ORIGIN="^https://your-domain\.com$"
```

## Environment Variables

### Core Application Variables
```bash
# Application Environment
APP_ENV=dev                    # dev, prod, test
APP_DEBUG=true                 # true for development, false for production
APP_SECRET=your-secret-key     # Change in production
```

### OAuth Configuration
```bash
# Google OAuth (Development)
GOOGLE_OAUTH_CLIENT_ID="39911293854-f6g93cn6t9egr3pnvtqrrdg4rf2530vc.apps.googleusercontent.com"
GOOGLE_OAUTH_CLIENT_SECRET="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"
GOOGLE_OAUTH_REDIRECT_URI="http://localhost:8000/api/v1/auth/google/callback"

# Microsoft OAuth (Development)
MICROSOFT_OAUTH_CLIENT_ID="your_microsoft_oauth_client_id"
MICROSOFT_OAUTH_CLIENT_SECRET="your_microsoft_oauth_client_secret"
MICROSOFT_OAUTH_REDIRECT_URI="http://localhost:8000/api/v1/auth/microsoft/callback"
```

### JWT Configuration
```bash
JWT_SECRET_KEY=%kernel.project_dir%/config/jwt/private.pem
JWT_PUBLIC_KEY=%kernel.project_dir%/config/jwt/public.pem
JWT_PASSPHRASE=dev-passphrase  # Change in production
```

### CORS Configuration
```bash
# Development
CORS_ALLOW_ORIGIN="^http://localhost:3000$"

# Production
CORS_ALLOW_ORIGIN="^https://your-domain\.com$"
```

### RDS Specific Variables
```bash
# SSL/TLS Configuration
DATABASE_SSL_MODE=require
DATABASE_SSL_CERT_PATH=/path/to/rds-ca-2019-root.pem

# Connection Pooling
DATABASE_POOL_SIZE=10
DATABASE_POOL_TIMEOUT=30

# Performance Monitoring
DATABASE_LOGGING=true
DATABASE_PROFILING=false

# Backup Configuration
DATABASE_BACKUP_ENABLED=true
DATABASE_BACKUP_RETENTION_DAYS=7
```

## Build Scripts

### Database User Setup (`setup-db-user.sh`)
- Creates `tulsa_seo` database
- Creates `smcnary` user with password `TulsaSeo122`
- Grants system admin privileges
- Creates system admin user in Symfony
- Tests database connectivity

### Cloud Migration (`migrate-to-cloud.sh`)
- Interactive cloud database setup
- Creates `smcnary` user on cloud database
- Updates environment files for cloud
- Runs migrations on cloud database
- Generates cloud-specific configuration

### GitHub Secrets Configuration
Add these to your repository: **Settings** → **Secrets and variables** → **Actions**

```bash
# AWS Credentials
AWS_ACCESS_KEY_ID=your-access-key
AWS_SECRET_ACCESS_KEY=your-secret-key
AWS_REGION=us-east-1

# RDS Staging
RDS_STAGING_ENDPOINT=counselrank-staging-db.cstm2wakq0zs.us-east-1.rds.amazonaws.com
RDS_STAGING_PASSWORD=TulsaSeo122
RDS_STAGING_DATABASE_URL=postgresql://counselrank_admin:TulsaSeo122@counselrank-staging-db.cstm2wakq0zs.us-east-1.rds.amazonaws.com:5432/counselrank_staging?serverVersion=16&charset=utf8

# RDS Production (update when created)
RDS_PRODUCTION_ENDPOINT=your-production-endpoint.region.rds.amazonaws.com
RDS_PRODUCTION_PASSWORD=your-production-password
RDS_PRODUCTION_DATABASE_URL=postgresql://counselrank_admin:your-production-password@your-production-endpoint.region.rds.amazonaws.com:5432/counselrank_prod?serverVersion=16&charset=utf8

# Server Deployment (existing)
STAGING_HOST=your-staging-server-ip
STAGING_USER=your-staging-user
STAGING_SSH_KEY=your-staging-ssh-private-key
PRODUCTION_HOST=your-production-server-ip
PRODUCTION_USER=your-production-user
PRODUCTION_SSH_KEY=your-production-ssh-private-key
```

## Troubleshooting

### Common Issues

#### 1. Database Connection Failed
- Check PostgreSQL is running: `sudo systemctl status postgresql` (Linux) or `brew services start postgresql` (macOS)
- Verify connection string in `.env.local`
- Check firewall settings

#### 2. Port Already in Use
- Backend: `symfony server:stop` then restart
- Frontend: Kill process on port 5173 or change port in `vite.config.ts`

#### 3. CORS Issues
- Ensure backend CORS configuration allows frontend origin
- Check `CORS_ALLOW_ORIGIN` in backend `.env.local`

#### 4. JWT Issues
- Generate JWT keys: `php bin/console lexik:jwt:generate-keypair`
- Check JWT configuration in `config/packages/lexik_jwt_authentication.yaml`

#### 5. Permission Denied
```bash
chmod +x *.sh
```

#### 6. Cache Issues
```bash
php bin/console cache:clear --env=dev
php bin/console cache:clear --env=prod
```

### Connection Issues

#### Timeout Errors
```bash
# Check RDS public accessibility
aws rds describe-db-instances --db-instance-identifier "counselrank-staging-db" --query 'DBInstances[0].PubliclyAccessible'

# Make publicly accessible (for development only)
aws rds modify-db-instance --db-instance-identifier "counselrank-staging-db" --publicly-accessible --apply-immediately
```

#### Authentication Errors
```bash
# Verify credentials
aws rds describe-db-instances --db-instance-identifier "counselrank-staging-db" --query 'DBInstances[0].[MasterUsername,DBName]'
```

#### Security Group Issues
```bash
# Check security group rules
aws ec2 describe-security-groups --group-names "counselrank-db-sg" --query 'SecurityGroups[0].IpPermissions'
```

### Environment Issues

#### Missing Variables
```bash
# Validate configuration
./scripts/setup-env.sh --validate
```

#### Wrong Database URL
```bash
# Check current configuration
cat .env.local | grep DATABASE_URL
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
```

## Environment Scenarios

### Scenario 1: Local Development with Docker
```bash
# Setup
./scripts/setup-env.sh --local

# Start services
docker-compose up -d

# Test
./scripts/setup-env.sh --test-connection
```

### Scenario 2: Local Development with RDS Staging
```bash
# Setup
./scripts/setup-env.sh --rds-staging

# Test (after RDS is publicly accessible)
./scripts/setup-env.sh --test-connection

# Run migrations
php bin/console doctrine:migrations:migrate --no-interaction
```

### Scenario 3: GitHub Actions Deployment
```bash
# Automatic deployment on push
git push origin develop  # Deploys to staging RDS
git push origin main     # Deploys to production RDS

# Manual deployment
# Go to Actions → RDS Database Deployment → Run workflow
```

## Validation Checklist

### Environment Setup
- [ ] Environment file created (`.env.local`)
- [ ] Database URL configured correctly
- [ ] OAuth credentials set
- [ ] JWT configuration complete
- [ ] CORS settings appropriate

### RDS Configuration
- [ ] RDS instance created and available
- [ ] Security group allows connections
- [ ] Database publicly accessible (for development)
- [ ] Connection string formatted correctly

### GitHub Integration
- [ ] AWS credentials added to secrets
- [ ] RDS endpoints added to secrets
- [ ] Database URLs added to secrets
- [ ] Server deployment secrets configured

### Testing
- [ ] Local connection test passes
- [ ] RDS connection test passes
- [ ] Migrations run successfully
- [ ] Application starts without errors

## Development Workflow

1. **Start both applications** using the setup above
2. **Make changes** to backend (PHP) or frontend (React)
3. **Backend auto-reloads** with Symfony server
4. **Frontend auto-reloads** with Vite HMR
5. **Test API endpoints** at http://localhost:8000/api
6. **Test frontend** at http://localhost:5173

## Security Notes

### Development
- Uses default secrets (safe for local development)
- Debug mode enabled
- Verbose logging

### Production
- **ALWAYS change default secrets**
- Update `APP_SECRET`
- Update `JWT_PASSPHRASE`
- Disable debug mode
- Use HTTPS URLs

## Next Steps

1. **Wait for RDS public accessibility** (may take 5-10 minutes)
2. **Test RDS connection**: `./scripts/setup-env.sh --test-connection`
3. **Run migrations**: `php bin/console doctrine:migrations:migrate --no-interaction`
4. **Add GitHub secrets** (see configuration above)
5. **Deploy via GitHub Actions** or manually

## Related Documentation

- [Authentication Guide](./AUTHENTICATION_GUIDE.md) - Complete authentication system
- [OAuth Setup](./OAUTH_SETUP.md) - OAuth provider configuration
- [API Documentation](./API_DOCUMENTATION.md) - Complete API reference
- [Database Guide](./DATABASE_GUIDE.md) - Database setup and management
- [Deployment Guide](./DEPLOYMENT_GUIDE.md) - Production deployment

---

**Last Updated:** January 2025  
**Maintained By:** Development Team  
**Status:** Complete and consolidated ✅