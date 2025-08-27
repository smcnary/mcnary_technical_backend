# GitHub Environment Setup Guide

## 🚨 CRITICAL: Required Setup for Dev Deployment

The CI/CD workflows require GitHub Environments to be configured before they can deploy to staging/production.

## 📋 Step-by-Step Environment Setup

### 1. **Create Staging Environment**
1. Go to your repository on GitHub
2. Navigate to **Settings** → **Environments**
3. Click **"New environment"**
4. Enter environment name: `staging`
5. Click **"Configure environment"**
6. Add environment protection rules:
   - ✅ **Required reviewers**: Add at least one team member
   - ✅ **Wait timer**: 0 minutes (or your preference)
   - ✅ **Deployment branches**: `develop` only
7. Click **"Save protection rules"**

### 2. **Create Production Environment**
1. Click **"New environment"** again
2. Enter environment name: `production`
3. Click **"Configure environment"**
4. Add environment protection rules:
   - ✅ **Required reviewers**: Add tech lead and devops team
   - ✅ **Wait timer**: 5 minutes (recommended for production)
   - ✅ **Deployment branches**: `main` only
7. Click **"Save protection rules"**

### 3. **Configure Required Secrets**
Go to **Settings** → **Secrets and variables** → **Actions** and add:

#### **Local Testing Secrets (No Server Required)**
```bash
# Use these values for local testing while you set up real servers
STAGING_SSH_KEY=-----BEGIN OPENSSH PRIVATE KEY-----... (use the key we generated)
STAGING_DB_PASSWORD=TulsaSeo122
STAGING_HOST=localhost
STAGING_USER=smcnary
STAGING_DB_HOST=localhost
```

#### **Staging Environment Secrets (When You Get a Server)**
```bash
STAGING_SSH_KEY=your-staging-private-ssh-key
STAGING_DB_PASSWORD=TulsaSeo122
STAGING_HOST=your-staging-server-ip
STAGING_USER=smcnary
STAGING_DB_HOST=your-staging-db-server-ip
```

#### **Production Environment Secrets**
```bash
PRODUCTION_SSH_KEY=your-production-private-ssh-key
PRODUCTION_DB_PASSWORD=TulsaSeo122
PRODUCTION_HOST=your-production-server-ip
PRODUCTION_USER=smcnary
```

#### **Global Secrets**
```bash
SNYK_TOKEN=your-snyk-token-here
SLACK_WEBHOOK=your-slack-webhook-url
```

## 🔧 Environment Configuration

### **Local Testing Environment (No Server Required)**
- **Purpose**: Test deployment pipeline locally
- **Branch**: `develop`
- **Auto-deploy**: ✅ Enabled (local testing)
- **Required Reviewers**: None (local testing)
- **Wait Timer**: 0 minutes
- **Server**: localhost (your local machine)

### **Staging Environment (develop branch)**
- **Purpose**: Pre-production testing
- **Branch**: `develop`
- **Auto-deploy**: ✅ Enabled
- **Required Reviewers**: 1+ team member
- **Wait Timer**: 0 minutes
- **Server**: Your staging server (when you get one)

### **Production Environment (main branch)**
- **Purpose**: Live production
- **Branch**: `main`
- **Auto-deploy**: ✅ Enabled
- **Required Reviewers**: Tech lead + DevOps
- **Wait Timer**: 5 minutes
- **Server**: Your production server (when you get one)

## 🧪 Testing the Setup

### **Local Testing (No Server Required)**
1. **Set up local database** (MySQL/PostgreSQL on your machine)
2. **Add local testing secrets** to GitHub (see values below)
3. **Create `local` environment** in GitHub (Settings → Environments)
4. **Make a small change** to any file in `backend/`, `frontend/`, or `audit-service/`
5. **Push to `develop` branch** to test the pipeline
6. **Check Actions tab** to see workflows running

### **Test Staging Deployment (When You Get a Server)**
1. Make a change to any file in `backend/`, `frontend/`, or `audit-service/`
2. Commit and push to `develop` branch
3. Check **Actions** tab to see workflows running
4. Verify staging deployment completes successfully

### **Test Production Deployment**
1. Merge `develop` to `main` (or push directly to main)
2. Check **Actions** tab to see workflows running
3. Verify production deployment completes successfully

## 🚨 Common Issues & Solutions

### **Issue: "Environment 'staging' not found"**
**Solution**: Create the `staging` environment in GitHub Settings → Environments

### **Issue: "Environment protection rules not satisfied"**
**Solution**: Ensure you have the required reviewers and branch restrictions configured

### **Issue: "Required secrets not found"**
**Solution**: Add the required secrets in GitHub Settings → Secrets and variables → Actions

### **Issue: "Deployment branch not allowed"**
**Solution**: Check environment protection rules and ensure the correct branch is allowed

## 📊 Environment Status

After setup, you should see:
- ✅ **Staging Environment**: Ready for `develop` branch deployments
- ✅ **Production Environment**: Ready for `main` branch deployments
- ✅ **All Required Secrets**: Configured and accessible
- ✅ **Environment Protection**: Rules configured and enforced

## 🔄 Next Steps

### **For Local Testing (No Server Required):**
1. **Set up local database** (MySQL/PostgreSQL on your machine)
2. **Add these secrets to GitHub** (Settings → Secrets and variables → Actions):
   ```bash
   STAGING_SSH_KEY=[your private key from ~/.ssh/deploy_key]
   STAGING_DB_PASSWORD=TulsaSeo122
   STAGING_HOST=localhost
   STAGING_USER=smcnary
   STAGING_DB_HOST=localhost
   ```
3. **Create `local` environment** in GitHub (Settings → Environments)
4. **Test with small change**: Push to `develop` to test the pipeline
5. **Monitor Actions tab** for successful runs

### **For Real Server Deployment (Later):**
1. **Complete Environment Setup**: Follow steps 1-3 above
2. **Test with Small Change**: Push to `develop` to test staging deployment
3. **Verify All Services**: Ensure backend, frontend, and audit-service deploy
4. **Monitor Deployments**: Check Actions tab for successful runs

---

**⚠️ IMPORTANT**: Without these environments configured, the deployment jobs in your workflows will fail with "Environment not found" errors.

**🚀 Ready to deploy?** Complete the environment setup above, then push to `develop` to test your dev deployment pipeline!
