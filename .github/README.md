# GitHub Actions CI/CD Documentation

This directory contains GitHub Actions workflows for automated testing, building, and deployment of the Tulsa SEO application.

## 🚀 Available Workflows

### 1. **Backend CI/CD** (`backend.yml`)
Handles Symfony backend application pipeline:
- **PHP 8.2** environment setup
- **MySQL 8.0** test database
- **Composer** dependency management
- **Testing** with PHPUnit (if available)
- **Code Quality** with PHPStan and PHP CS Fixer
- **Security Audit** with Composer audit
- **Build** optimization for production
- **Deployment** to staging and production

### 2. **Frontend CI/CD** (`frontend.yml`)
Handles React/TypeScript frontend application pipeline:
- **Node.js 18** environment setup
- **npm** dependency management
- **TypeScript** compilation checking
- **ESLint** code quality
- **Security Audit** with npm audit and Snyk
- **Build** with Vite
- **Performance Audit** with Lighthouse CI
- **Deployment** to staging and production

### 3. **Full Stack CI/CD** (`full-stack.yml`)
Coordinates both applications in a unified pipeline:
- **Parallel testing** of both applications
- **Coordinated builds** ensuring compatibility
- **Unified deployment** to staging and production
- **Integration testing** after deployment
- **Manual trigger** support for specific environments

## 🔧 Workflow Triggers

### Automatic Triggers
- **Push** to `main` or `develop` branches
- **Pull Requests** to `main` or `develop` branches
- **Path-based filtering** (only runs when relevant files change)

### Manual Triggers
- **Workflow Dispatch** for manual deployment to specific environments
- **Environment selection** (staging/production)

## 🏗️ Pipeline Stages

### 1. **Test Stage**
```
Backend Tests:
├── PHP 8.2 + Extensions
├── MySQL 8.0 Database
├── Composer Dependencies
├── Environment Setup
├── Database Schema
├── Migrations
└── PHPUnit Tests

Frontend Tests:
├── Node.js 18
├── npm Dependencies
├── ESLint
├── TypeScript Check
└── Unit Tests (if available)
```

### 2. **Build Stage**
```
Backend Build:
├── Production Dependencies
├── Cache Clear & Warmup
├── Assets Installation
└── Build Artifact

Frontend Build:
├── Production Dependencies
├── TypeScript Compilation
├── Vite Build
└── Build Artifact
```

### 3. **Deploy Stage**
```
Staging (develop branch):
├── Download Artifacts
├── Deploy Backend
├── Deploy Frontend
└── Integration Tests

Production (main branch):
├── Download Artifacts
├── Deploy Backend
├── Deploy Frontend
├── Performance Audit
└── Integration Tests
```

## 🛠️ Prerequisites

### Backend Requirements
- **PHP 8.2+** with required extensions
- **Composer 2.x**
- **MySQL 8.0** for testing
- **PHPUnit** for testing (optional)

### Frontend Requirements
- **Node.js 18+**
- **npm** package manager
- **TypeScript 5.3+**
- **Vite 5.0+**

### GitHub Secrets Required
```yaml
# For Snyk security scanning
SNYK_TOKEN: "your-snyk-token"

# For deployment (add as needed)
DEPLOY_SSH_KEY: "private-ssh-key"
DEPLOY_HOST: "your-server-hostname"
DEPLOY_USER: "deploy-user"
```

## 📁 Artifacts Generated

### Backend Artifacts
- `backend-build.tar.gz` - Production-ready backend application
- `security-report.json` - Security audit results

### Frontend Artifacts
- `frontend-build/` - Compiled production build
- `frontend-build-info/` - Package information

## 🔒 Environment Protection

### Staging Environment
- **Branch:** `develop`
- **Auto-deploy:** Yes
- **Manual trigger:** Yes
- **Required reviewers:** None

### Production Environment
- **Branch:** `main`
- **Auto-deploy:** Yes
- **Manual trigger:** Yes
- **Required reviewers:** Configure in GitHub

## 🚨 Failure Handling

### Automatic Rollback
- Failed deployments automatically stop the pipeline
- Previous successful deployment remains active
- Team notifications sent on failure

### Manual Recovery
- Use workflow dispatch to redeploy
- Check logs for specific failure reasons
- Verify environment configuration

## 📊 Monitoring & Notifications

### Success Notifications
- ✅ Deployment completion
- 📊 Performance metrics
- 🔒 Security scan results

### Failure Notifications
- ❌ Deployment failures
- 🐛 Test failures
- ⚠️ Security vulnerabilities

## 🎯 Customization

### Adding New Environments
1. Create new environment in GitHub
2. Add deployment job to workflow
3. Configure environment-specific variables
4. Update branch protection rules

### Modifying Build Process
1. Edit relevant workflow file
2. Add/remove build steps
3. Update artifact generation
4. Test in development branch

### Adding New Tools
1. Install tool in workflow
2. Configure tool settings
3. Add to appropriate job
4. Update documentation

## 🔍 Troubleshooting

### Common Issues

#### Backend Build Failures
```bash
# Check PHP version compatibility
php --version

# Verify Composer dependencies
composer diagnose

# Check environment configuration
php bin/console debug:config
```

#### Frontend Build Failures
```bash
# Check Node.js version
node --version

# Verify npm dependencies
npm audit

# Check TypeScript configuration
npx tsc --noEmit
```

#### Deployment Failures
- Verify environment secrets
- Check server connectivity
- Review deployment scripts
- Validate artifact integrity

### Debug Mode
Enable debug logging in workflows:
```yaml
- name: Debug Information
  run: |
    echo "GitHub Event: ${{ toJSON(github.event) }}"
    echo "Runner Environment: ${{ toJSON(runner) }}"
    echo "Working Directory: $(pwd)"
    echo "Directory Contents: $(ls -la)"
```

## 📚 Additional Resources

- [GitHub Actions Documentation](https://docs.github.com/en/actions)
- [Symfony Best Practices](https://symfony.com/doc/current/best_practices.html)
- [React Deployment Guide](https://create-react-app.dev/docs/deployment/)
- [API Platform Documentation](https://api-platform.com/docs/)

## 🤝 Contributing

When modifying workflows:
1. Test changes in development branch
2. Update documentation
3. Follow conventional commit messages
4. Request review from team members

---

**Last Updated:** $(date)
**Maintained by:** Development Team
