# 🚀 Deployment Setup Guide

## SSH Authentication Issue Resolution

The CI/CD pipeline is failing with "Permission denied (publickey)" error. This guide will help you set up proper SSH authentication for automated deployments.

## 🔧 Quick Fix Steps

### 1. Generate SSH Key Pair

```bash
# Generate a new SSH key pair for GitHub Actions
ssh-keygen -t ed25519 -C "github-actions-staging" -f staging_key

# This creates:
# - staging_key (private key) - add to GitHub secrets
# - staging_key.pub (public key) - add to server
```

### 2. Add Public Key to Staging Server

```bash
# Option A: Use ssh-copy-id (recommended)
ssh-copy-id -i staging_key.pub deploy@your-staging-server.com

# Option B: Manual method
cat staging_key.pub | ssh deploy@your-staging-server.com "mkdir -p ~/.ssh && cat >> ~/.ssh/authorized_keys && chmod 600 ~/.ssh/authorized_keys && chmod 700 ~/.ssh"
```

### 3. Configure GitHub Repository Secrets

Go to your GitHub repository → **Settings** → **Secrets and variables** → **Actions** and add:

| Secret Name | Value | Description |
|-------------|-------|-------------|
| `STAGING_SSH_KEY` | Content of `staging_key` file | Private SSH key for staging server |
| `STAGING_USER` | `deploy` (or your server username) | SSH username for staging server |
| `STAGING_HOST` | `your-staging-server.com` | Hostname/IP of staging server |
| `AWS_ACCESS_KEY_ID` | Your AWS access key | AWS credentials |
| `AWS_SECRET_ACCESS_KEY` | Your AWS secret key | AWS credentials |
| `AWS_REGION` | `us-east-1` | AWS region |

### 4. Test SSH Connection

```bash
# Test the SSH connection
ssh -i staging_key deploy@your-staging-server.com

# If successful, you should see a shell prompt
```

## 🖥️ Server Setup Requirements

### Required Software

```bash
# Install PHP 8.2 and extensions
sudo apt update
sudo apt install -y php8.2-fpm php8.2-cli php8.2-pgsql php8.2-xml php8.2-mbstring php8.2-curl php8.2-zip php8.2-intl php8.2-opcache

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Install Nginx
sudo apt install -y nginx

# Install PostgreSQL client
sudo apt install -y postgresql-client
```

### Directory Setup

```bash
# Create deployment directory
sudo mkdir -p /var/www/tulsa-seo-backend
sudo chown -R $USER:$USER /var/www/tulsa-seo-backend

# Set proper permissions
chmod -R 755 /var/www/tulsa-seo-backend
```

### Nginx Configuration

Create `/etc/nginx/sites-available/tulsa-seo-backend`:

```nginx
server {
    listen 80;
    server_name your-staging-domain.com;
    root /var/www/tulsa-seo-backend/public;
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

    error_log /var/log/nginx/tulsa-seo-backend_error.log;
    access_log /var/log/nginx/tulsa-seo-backend_access.log;
}
```

Enable the site:

```bash
sudo ln -s /etc/nginx/sites-available/tulsa-seo-backend /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

## 🔍 Troubleshooting

### Run the Troubleshooting Script

```bash
# Make the script executable
chmod +x .github/scripts/deploy-troubleshoot.sh

# Run troubleshooting (set environment variables first)
export STAGING_USER="deploy"
export STAGING_HOST="your-staging-server.com"
export STAGING_SSH_KEY="$(cat staging_key)"

./.github/scripts/deploy-troubleshoot.sh
```

### Common Issues and Solutions

#### 1. "Permission denied (publickey)"

**Causes:**
- SSH key not added to GitHub secrets
- Public key not added to server's authorized_keys
- Wrong username or hostname
- Incorrect SSH key format

**Solutions:**
```bash
# Check if public key is in authorized_keys
ssh deploy@your-server.com "cat ~/.ssh/authorized_keys"

# Test SSH connection with verbose output
ssh -v -i staging_key deploy@your-staging-server.com
```

#### 2. "Connection refused"

**Causes:**
- Server is not running
- Firewall blocking SSH
- Wrong port (SSH uses port 22 by default)

**Solutions:**
```bash
# Check if SSH service is running
sudo systemctl status ssh

# Check if port 22 is open
nmap -p 22 your-staging-server.com
```

#### 3. "No such file or directory" during deployment

**Causes:**
- Deployment directory doesn't exist
- Wrong permissions on deployment directory

**Solutions:**
```bash
# Create deployment directory
sudo mkdir -p /var/www/tulsa-seo-backend
sudo chown -R $USER:$USER /var/www/tulsa-seo-backend
```

## 🚀 Testing the Fix

### 1. Test SSH Connection Locally

```bash
ssh -i staging_key deploy@your-staging-server.com
```

### 2. Test File Transfer

```bash
echo "test" > test.txt
scp -i staging_key test.txt deploy@your-staging-server.com:/tmp/
ssh -i staging_key deploy@your-staging-server.com "cat /tmp/test.txt"
```

### 3. Trigger Deployment

```bash
# Push a small change to trigger the workflow
git commit --allow-empty -m "test: trigger deployment"
git push origin develop
```

### 4. Monitor GitHub Actions

- Go to your GitHub repository
- Click on "Actions" tab
- Watch the "Backend CI/CD" workflow
- Check the "Deploy to Staging" job

## 📋 Deployment Checklist

- [ ] SSH key pair generated
- [ ] Public key added to server's authorized_keys
- [ ] Private key added to GitHub secrets
- [ ] All required GitHub secrets configured
- [ ] Server has required software installed
- [ ] Deployment directory created with proper permissions
- [ ] Nginx configured and enabled
- [ ] SSH connection tested locally
- [ ] File transfer tested
- [ ] GitHub Actions workflow triggered

## 🔒 Security Best Practices

1. **Use dedicated deployment user**: Create a `deploy` user with limited permissions
2. **Restrict SSH access**: Use key-based authentication only
3. **Rotate keys regularly**: Generate new SSH keys periodically
4. **Monitor access**: Log SSH access attempts
5. **Use strong passphrases**: Protect SSH keys with passphrases
6. **Limit sudo access**: Only grant necessary sudo permissions to deploy user

## 📞 Support

If you continue to have issues:

1. Check the GitHub Actions logs for detailed error messages
2. Run the troubleshooting script
3. Verify all secrets are correctly configured
4. Test SSH connection manually
5. Check server logs: `/var/log/auth.log`, `/var/log/nginx/error.log`

---

**Next Steps:** Once SSH authentication is working, the deployment should complete successfully and your staging environment will be updated automatically on every push to the `develop` branch.
