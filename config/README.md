# Configuration Directory

This directory contains shared configuration files and templates.

## Environments (`/environments`)
Environment configuration templates for different deployment stages:

- `env-template.txt` - Base environment template
- `env.dev` - Development environment
- `env.prod` - Production environment
- `env.rds` - RDS database configuration
- `env.rds-production` - Production RDS configuration
- `env.rds-staging` - Staging RDS configuration
- `env.db-setup` - Database setup configuration

## Docker (`/docker`)
Shared Docker configurations and compose files for multi-service development and deployment.

## Usage
1. Copy the appropriate environment template to your service directory
2. Rename to `.env` and customize for your local setup
3. Never commit actual environment files with secrets
4. Use environment-specific templates for different deployment stages
