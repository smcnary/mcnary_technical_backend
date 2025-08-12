# Quick Setup Guide for GitHub Actions

## 🚀 Getting Started

### 1. **Enable GitHub Actions**
- Go to your repository on GitHub
- Navigate to **Settings** → **Actions** → **General**
- Ensure "Allow all actions and reusable workflows" is selected
- Save the changes

### 2. **Set Up Environments** (Optional but Recommended)
- Go to **Settings** → **Environments**
- Create environments: `staging` and `production`
- Add environment protection rules as needed

### 3. **Configure Secrets** (Required for Security & Deployment)
Go to **Settings** → **Secrets and variables** → **Actions** and add:

#### **Security Secrets**
```bash
SNYK_TOKEN=your-snyk-token-here
```

#### **Deployment Secrets** (if using SSH deployment)
```bash
STAGING_SSH_KEY=your-staging-private-ssh-key
PRODUCTION_SSH_KEY=your-production-private-ssh-key
STAGING_DB_PASSWORD=your-staging-db-password
PRODUCTION_DB_PASSWORD=your-production-db-password
```

#### **Notification Secrets** (optional)
```bash
SLACK_WEBHOOK=your-slack-webhook-url
```

### 4. **Branch Protection** (Recommended)
- Go to **Settings** → **Branches**
- Add rule for `main` branch:
  - ✅ Require a pull request before merging
  - ✅ Require status checks to pass before merging
  - ✅ Require branches to be up to date before merging
  - ✅ Include administrators

## 🧪 Testing the Workflows

### **Test Individual Workflows**
1. Make a small change to a file in `backend/` or `frontend/`
2. Commit and push to `develop` branch
3. Check **Actions** tab to see workflows running

### **Test Full Stack Workflow**
1. Push to `main` branch
2. Watch the full pipeline execute
3. Verify all stages complete successfully

## 🔧 Customization

### **Modify Deployment Logic**
1. Edit `.github/scripts/deploy.sh`
2. Update server paths and commands
3. Test in staging environment first

### **Add New Tools**
1. Edit relevant workflow file
2. Add new job or step
3. Update documentation

### **Environment-Specific Settings**
1. Edit `.github/config/environments.yml`
2. Update server details and credentials
3. Modify environment variables

## 📊 Monitoring

### **Workflow Status**
- **Actions** tab shows all workflow runs
- **Environments** tab shows deployment status
- **Insights** tab shows workflow analytics

### **Notifications**
- Email notifications for workflow results
- Slack notifications (if configured)
- GitHub notifications in your feed

## 🚨 Troubleshooting

### **Common Issues**

#### **Workflow Not Triggering**
- Check file paths in workflow triggers
- Verify branch names match
- Ensure Actions are enabled

#### **Build Failures**
- Check dependency versions
- Verify environment setup
- Review error logs in Actions tab

#### **Deployment Failures**
- Verify secrets are configured
- Check server connectivity
- Review deployment script permissions

### **Debug Mode**
Add debug information to workflows:
```yaml
- name: Debug Info
  run: |
    echo "Event: ${{ toJSON(github.event) }}"
    echo "Context: ${{ toJSON(github.context) }}"
    echo "Runner: ${{ toJSON(runner) }}"
```

## 📚 Next Steps

### **Immediate Actions**
1. ✅ Push these workflow files to your repository
2. ✅ Configure required secrets
3. ✅ Test with a small change
4. ✅ Verify all stages work

### **Advanced Setup**
1. 🔧 Customize deployment scripts
2. 🔧 Configure monitoring and alerts
3. 🔧 Set up branch protection rules
4. 🔧 Configure environment-specific settings

### **Production Readiness**
1. 🚀 Test full deployment pipeline
2. 🚀 Configure production environment
3. 🚀 Set up monitoring and alerting
4. 🚀 Document deployment procedures

## 🆘 Support

### **GitHub Actions Help**
- [GitHub Actions Documentation](https://docs.github.com/en/actions)
- [GitHub Community](https://github.com/orgs/community/discussions)
- [GitHub Support](https://support.github.com/)

### **Project-Specific Help**
- Check workflow logs in Actions tab
- Review error messages and stack traces
- Consult team members or maintainers

---

**Ready to deploy?** 🚀

Push these files to your repository and watch the magic happen!
