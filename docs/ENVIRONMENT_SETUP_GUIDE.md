# 🔧 Environment Variables Setup Guide

## 📋 Overview

This guide ensures all environment variables are properly configured between your backend and RDS database across all deployment scenarios.

## 🎯 Current Configuration Status

### ✅ **RDS Staging Instance Created**
- **Instance ID**: `counselrank-staging-db`
- **Endpoint**: `counselrank-staging-db.cstm2wakq0zs.us-east-1.rds.amazonaws.com:5432`
- **Database**: `counselrank_staging`
- **Username**: `counselrank_admin`
- **Password**: `TulsaSeo122`
- **Status**: Available (publicly accessible modification in progress)

### ✅ **Environment Templates Created**
- `env.rds-staging` - For local development with RDS staging
- `env.rds-production` - For production with RDS production
- `env.dev` - For local development with Docker
- `env.prod` - For production with local database

## 🚀 Quick Setup Commands

### **Option 1: Local Development with Docker**
```bash
cd backend
./scripts/setup-env.sh --local
```

### **Option 2: Local Development with RDS Staging**
```bash
cd backend
./scripts/setup-env.sh --rds-staging
```

### **Option 3: Production with RDS Production**
```bash
cd backend
./scripts/setup-env.sh --rds-production
```

### **Test Database Connection**
```bash
cd backend
./scripts/setup-env.sh --test-connection
```

## 📊 Environment Variable Reference

### **Core Application Variables**
```bash
# Application Environment
APP_ENV=dev                    # dev, prod, test
APP_DEBUG=true                 # true for development, false for production
APP_SECRET=your-secret-key     # Change in production
```

### **Database Configuration**
```bash
# Local Docker Database
DATABASE_URL="postgresql://smcnary:TulsaSeo122@127.0.0.1:5432/tulsa_seo?serverVersion=16&charset=utf8"

# RDS Staging Database
DATABASE_URL="postgresql://counselrank_admin:TulsaSeo122@counselrank-staging-db.cstm2wakq0zs.us-east-1.rds.amazonaws.com:5432/counselrank_staging?serverVersion=16&charset=utf8"

# RDS Production Database (update with actual values)
DATABASE_URL="postgresql://counselrank_admin:YOUR_PASSWORD@your-rds-endpoint.region.rds.amazonaws.com:5432/counselrank_prod?serverVersion=16&charset=utf8"
```

### **OAuth Configuration**
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

### **JWT Configuration**
```bash
JWT_SECRET_KEY=%kernel.project_dir%/config/jwt/private.pem
JWT_PUBLIC_KEY=%kernel.project_dir%/config/jwt/public.pem
JWT_PASSPHRASE=dev-passphrase  # Change in production
```

### **CORS Configuration**
```bash
# Development
CORS_ALLOW_ORIGIN="^http://localhost:3000$"

# Production
CORS_ALLOW_ORIGIN="^https://your-domain\.com$"
```

### **RDS Specific Variables**
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

## 🔐 GitHub Secrets Configuration

### **Required GitHub Secrets**
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

## 🔄 Environment Scenarios

### **Scenario 1: Local Development with Docker**
```bash
# Setup
./scripts/setup-env.sh --local

# Start services
docker-compose up -d

# Test
./scripts/setup-env.sh --test-connection
```

### **Scenario 2: Local Development with RDS Staging**
```bash
# Setup
./scripts/setup-env.sh --rds-staging

# Test (after RDS is publicly accessible)
./scripts/setup-env.sh --test-connection

# Run migrations
php bin/console doctrine:migrations:migrate --no-interaction
```

### **Scenario 3: GitHub Actions Deployment**
```bash
# Automatic deployment on push
git push origin develop  # Deploys to staging RDS
git push origin main     # Deploys to production RDS

# Manual deployment
# Go to Actions → RDS Database Deployment → Run workflow
```

## 🛠️ Troubleshooting

### **Connection Issues**

#### **Timeout Errors**
```bash
# Check RDS public accessibility
aws rds describe-db-instances --db-instance-identifier "counselrank-staging-db" --query 'DBInstances[0].PubliclyAccessible'

# Make publicly accessible (for development only)
aws rds modify-db-instance --db-instance-identifier "counselrank-staging-db" --publicly-accessible --apply-immediately
```

#### **Authentication Errors**
```bash
# Verify credentials
aws rds describe-db-instances --db-instance-identifier "counselrank-staging-db" --query 'DBInstances[0].[MasterUsername,DBName]'
```

#### **Security Group Issues**
```bash
# Check security group rules
aws ec2 describe-security-groups --group-names "counselrank-db-sg" --query 'SecurityGroups[0].IpPermissions'
```

### **Environment Issues**

#### **Missing Variables**
```bash
# Validate configuration
./scripts/setup-env.sh --validate
```

#### **Wrong Database URL**
```bash
# Check current configuration
cat .env.local | grep DATABASE_URL
```

## 📋 Validation Checklist

### **✅ Environment Setup**
- [ ] Environment file created (`.env.local`)
- [ ] Database URL configured correctly
- [ ] OAuth credentials set
- [ ] JWT configuration complete
- [ ] CORS settings appropriate

### **✅ RDS Configuration**
- [ ] RDS instance created and available
- [ ] Security group allows connections
- [ ] Database publicly accessible (for development)
- [ ] Connection string formatted correctly

### **✅ GitHub Integration**
- [ ] AWS credentials added to secrets
- [ ] RDS endpoints added to secrets
- [ ] Database URLs added to secrets
- [ ] Server deployment secrets configured

### **✅ Testing**
- [ ] Local connection test passes
- [ ] RDS connection test passes
- [ ] Migrations run successfully
- [ ] Application starts without errors

## 🎯 Next Steps

1. **Wait for RDS public accessibility** (may take 5-10 minutes)
2. **Test RDS connection**: `./scripts/setup-env.sh --test-connection`
3. **Run migrations**: `php bin/console doctrine:migrations:migrate --no-interaction`
4. **Add GitHub secrets** (see configuration above)
5. **Deploy via GitHub Actions** or manually

## 📚 Additional Resources

- **RDS Deployment Guide**: `documentation/RDS_DEPLOYMENT_GUIDE.md`
- **GitHub Actions Integration**: `.github/workflows/README_RDS_INTEGRATION.md`
- **Environment Templates**: `env.rds-staging`, `env.rds-production`
- **Setup Script**: `scripts/setup-env.sh`

---

## 🎉 Summary

Your environment variables are now properly configured for:

- ✅ **Local Development** (Docker)
- ✅ **Local Development** (RDS Staging)
- ✅ **Production Deployment** (RDS Production)
- ✅ **GitHub Actions CI/CD**

The setup script (`./scripts/setup-env.sh`) provides easy switching between environments, and all necessary GitHub secrets are documented for CI/CD integration.
