# GitHub Actions Workflows

This directory contains the CI/CD workflows for the CounselRank.legal project.

## 📋 Workflow Overview

### 1. **Backend CI/CD** (`backend.yml`)
- **Purpose**: Handles backend (Symfony) testing, building, and deployment
- **Triggers**: Changes to `backend/**` files or workflow file
- **Features**:
  - PHP 8.2 testing with PostgreSQL
  - Composer dependency management
  - Database schema creation and migrations
  - PHPUnit testing (if available)
  - Static analysis with PHPStan and PHP CS Fixer
  - Security auditing
  - Production builds and deployments

### 2. **Frontend CI/CD** (`frontend.yml`)
- **Purpose**: Handles frontend (React) testing, building, and deployment
- **Triggers**: Changes to `frontend/**` files or workflow file
- **Features**:
  - Node.js 18 testing
  - npm dependency management
  - TypeScript type checking
  - ESLint code quality checks
  - Build optimization
  - Lighthouse performance auditing
  - Production deployments

### 3. **Full Stack Integration** (`full-stack.yml`)
- **Purpose**: Coordinates integration testing between backend and frontend
- **Triggers**: Any push/PR to main/develop branches
- **Features**:
  - Integration testing with both backend and frontend
  - Database setup for integration tests
  - Deployment coordination
  - Cross-component validation

## 🚀 Workflow Usage

### ⚠️ **REQUIRED SETUP FIRST**
Before workflows can deploy, you **MUST** set up GitHub Environments:
- See [`.github/ENVIRONMENT_SETUP.md`](../ENVIRONMENT_SETUP.md) for complete setup instructions
- Create `staging` and `production` environments in GitHub Settings → Environments
- Configure required secrets and protection rules

### Automatic Triggers
- **Push to `main`**: Triggers production deployment workflows
- **Push to `develop`**: Triggers staging deployment workflows  
- **Pull Requests**: Triggers testing workflows only

### Manual Triggers
- **Workflow Dispatch**: Manually trigger deployments with environment selection
- **Path-based**: Workflows only run when relevant files change

## 🔧 Configuration

### Environment Variables
- **Backend**: Uses `.env.test` for testing, `.env` for production
- **Frontend**: Uses `VITE_*` environment variables
- **Database**: PostgreSQL 16 for all environments

### Secrets Required
- `SNYK_TOKEN`: For Snyk security scanning (optional)
- Environment-specific deployment secrets (configure in GitHub)

## 📊 Workflow Dependencies

```
Backend Workflow ──┐
                   ├── Full Stack Integration ─── Deploy Coordination
Frontend Workflow ─┘
```

## 🧪 Testing Strategy

### Backend Testing
1. **Unit Tests**: PHPUnit with PostgreSQL test database
2. **Schema Validation**: Doctrine ORM schema creation and validation
3. **Migration Testing**: Run all migrations in test environment
4. **Static Analysis**: PHPStan and PHP CS Fixer code quality checks

### Frontend Testing
1. **Type Checking**: TypeScript compilation validation
2. **Linting**: ESLint code quality and style checks
3. **Build Testing**: Production build verification
4. **Performance**: Lighthouse CI performance auditing

### Integration Testing
1. **Database Integration**: Backend database operations
2. **API Integration**: Frontend-backend communication
3. **Build Integration**: Full stack build verification

## 🚨 Troubleshooting

### Common Issues

1. **Database Connection Failures**
   - Check PostgreSQL service health
   - Verify connection strings in test environment files
   - Ensure proper permissions for test database

2. **Dependency Installation Failures**
   - Check Composer/npm configuration
   - Verify lock file integrity
   - Check for version conflicts

3. **Test Failures**
   - Review test environment configuration
   - Check database schema creation
   - Verify migration execution

4. **Build Failures**
   - Check for syntax errors
   - Verify environment variable configuration
   - Review build script configuration

### Debug Commands

```bash
# Check workflow status
gh run list --workflow=backend.yml

# View workflow logs
gh run view <run-id> --log

# Rerun failed workflow
gh run rerun <run-id>
```

## 🔒 Security Considerations

- **Secrets Management**: All sensitive data stored in GitHub Secrets
- **Environment Isolation**: Test and production environments completely separated
- **Dependency Scanning**: Regular security audits of dependencies
- **Access Control**: Workflows only run on protected branches

## 📚 Best Practices

1. **Keep Workflows Focused**: Each workflow should have a single responsibility
2. **Use Path Filters**: Only run workflows when relevant files change
3. **Fail Fast**: Stop workflows early if critical steps fail
4. **Clear Notifications**: Provide clear feedback on success/failure
5. **Artifact Management**: Clean up artifacts to save storage

## 🔄 Workflow Updates

When updating workflows:

1. **Test Changes**: Use workflow dispatch to test changes
2. **Update Documentation**: Keep this README current
3. **Version Control**: Use semantic versioning for workflow changes
4. **Rollback Plan**: Have a plan to revert problematic changes

---

**Note**: These workflows are designed to work together to provide comprehensive CI/CD coverage for the full stack application.
