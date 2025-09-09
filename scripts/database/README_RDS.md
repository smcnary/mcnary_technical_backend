# 🚀 RDS Deployment Quick Start

## Quick Deployment

### 1. Deploy RDS Instance
```bash
cd backend
./scripts/deploy-rds.sh
```

### 2. Test Connection
```bash
./scripts/test-rds-connection.sh
```

### 3. Migrate Data (if needed)
```bash
./scripts/migrate-to-rds.sh --rds-endpoint your-endpoint --rds-password your-password
```

## Files Created

- `deploy-rds.sh` - Automated RDS deployment script
- `migrate-to-rds.sh` - Data migration script
- `test-rds-connection.sh` - Connection testing script
- `rds-config.yaml` - Configuration template
- `rds-terraform.tf` - Terraform infrastructure as code
- `env.rds` - RDS environment template

## Prerequisites

- AWS CLI configured
- PostgreSQL client tools (`psql`, `pg_dump`)
- PHP 8.3+ and Composer

## Documentation

See `documentation/RDS_DEPLOYMENT_GUIDE.md` for complete instructions.
