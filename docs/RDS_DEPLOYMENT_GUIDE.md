# 🚀 RDS Deployment Guide

## 📋 Overview

This guide provides comprehensive instructions for deploying the CounselRank.legal PostgreSQL database to AWS RDS (Relational Database Service). The deployment includes automated scripts, Terraform infrastructure as code, and migration tools.

## 🎯 Prerequisites

### Required Tools
- **AWS CLI** - Configured with appropriate permissions
- **PostgreSQL Client Tools** - `pg_dump`, `psql`
- **Terraform** (optional) - For infrastructure as code deployment
- **PHP 8.3+** - For running Symfony migrations
- **Composer** - For PHP dependencies

### AWS Permissions
Your AWS user/role needs the following permissions:
- `rds:*` - RDS instance management
- `ec2:*` - Security groups and VPC management
- `iam:*` - IAM roles for monitoring
- `cloudwatch:*` - CloudWatch alarms and logs

## 🚀 Deployment Options

### Option 1: Automated Script Deployment (Recommended)

The `deploy-rds.sh` script provides a fully automated deployment process.

```bash
# Navigate to the backend directory
cd backend

# Run the deployment script
./scripts/deploy-rds.sh

# Or with custom parameters
./scripts/deploy-rds.sh \
  --instance-id "counselrank-prod-db" \
  --db-name "counselrank_prod" \
  --username "counselrank_admin" \
  --password "your-secure-password" \
  --instance-class "db.t3.small" \
  --storage 50 \
  --region "us-east-1"
```

#### Script Features
- ✅ Creates security groups
- ✅ Provisions RDS PostgreSQL 16 instance
- ✅ Configures parameter groups
- ✅ Sets up monitoring and alarms
- ✅ Runs database migrations
- ✅ Updates environment configuration
- ✅ Creates initial backup

### Option 2: Terraform Infrastructure as Code

For production environments, use Terraform for infrastructure management.

```bash
# Navigate to scripts directory
cd backend/scripts

# Copy and configure variables
cp rds-terraform.tfvars.example terraform.tfvars
# Edit terraform.tfvars with your values

# Initialize Terraform
terraform init

# Plan deployment
terraform plan

# Apply deployment
terraform apply
```

#### Terraform Features
- ✅ Infrastructure as Code
- ✅ State management
- ✅ Resource tagging
- ✅ CloudWatch monitoring
- ✅ Security group management
- ✅ Parameter group configuration
- ✅ Output values for integration

### Option 3: Manual AWS Console Deployment

For learning or custom configurations, deploy manually through AWS Console.

## 🔧 Configuration

### Environment Variables

The deployment scripts automatically update your `.env.local` file with RDS configuration:

```bash
# Production Database Configuration
DATABASE_URL="postgresql://counselrank_admin:password@your-rds-endpoint.region.rds.amazonaws.com:5432/counselrank_prod?serverVersion=16&charset=utf8"
APP_ENV=prod
APP_DEBUG=false
```

### RDS Configuration

#### Instance Classes
| Environment | Instance Class | Use Case |
|-------------|----------------|----------|
| Development | `db.t3.micro` | Testing, development |
| Staging | `db.t3.small` | Pre-production testing |
| Production | `db.t3.medium+` | Production workloads |

#### Storage Configuration
- **Storage Type**: General Purpose SSD (gp2)
- **Initial Storage**: 20GB (development) to 100GB+ (production)
- **Auto-scaling**: Enabled (up to 2x initial storage)
- **Encryption**: Enabled at rest

#### High Availability
- **Multi-AZ**: Enabled for production
- **Backup Retention**: 7 days (production), 1 day (development)
- **Backup Window**: 03:00-04:00 UTC
- **Maintenance Window**: Sunday 04:00-05:00 UTC

## 🔄 Data Migration

### From Local Development Database

If you have existing data in your local PostgreSQL database:

```bash
# Run the migration script
./scripts/migrate-to-rds.sh \
  --rds-endpoint "your-rds-endpoint.region.rds.amazonaws.com" \
  --rds-password "your-rds-password"
```

#### Migration Process
1. **Backup Local Database** - Creates SQL dump of local data
2. **Test RDS Connection** - Verifies connectivity
3. **Run Migrations** - Applies schema to RDS
4. **Restore Data** - Imports data from local backup
5. **Verify Migration** - Confirms data integrity
6. **Update Configuration** - Updates environment files

### From Existing RDS Instance

For migrating from one RDS instance to another:

```bash
# Create snapshot of source instance
aws rds create-db-snapshot \
  --db-instance-identifier "source-instance" \
  --db-snapshot-identifier "migration-snapshot-$(date +%Y%m%d)"

# Restore from snapshot
aws rds restore-db-instance-from-db-snapshot \
  --db-instance-identifier "target-instance" \
  --db-snapshot-identifier "migration-snapshot-$(date +%Y%m%d)"
```

## 🔐 Security Configuration

### Network Security

#### Security Groups
The deployment creates a security group with:
- **Inbound**: PostgreSQL (port 5432) from your application servers
- **Outbound**: All traffic (for updates and monitoring)

#### VPC Configuration
- **Public Access**: Disabled for production
- **Subnet Groups**: Uses default or custom subnets
- **VPC Peering**: Recommended for cross-region access

### Database Security

#### Authentication
- **Master Username**: `counselrank_admin`
- **Password**: Auto-generated secure password
- **SSL/TLS**: Required for all connections

#### Access Control
- **IAM Database Authentication**: Available
- **Parameter Groups**: Optimized for security
- **Encryption**: Enabled at rest and in transit

## 📊 Monitoring and Alerting

### CloudWatch Integration

The deployment automatically configures:

#### Metrics
- **CPU Utilization** - Alert at 80%
- **Freeable Memory** - Alert below 100MB
- **Free Storage Space** - Alert below 2GB
- **Database Connections** - Monitor active connections
- **Read/Write IOPS** - Performance monitoring

#### Logs
- **PostgreSQL Logs** - Query and error logs
- **Performance Insights** - Query performance analysis
- **Enhanced Monitoring** - OS-level metrics

### Custom Monitoring

```bash
# Check database performance
php bin/console doctrine:query:sql "
SELECT 
  schemaname,
  tablename,
  indexname,
  idx_scan,
  idx_tup_read,
  idx_tup_fetch
FROM pg_stat_user_indexes 
ORDER BY idx_scan DESC;
"

# Monitor slow queries
php bin/console doctrine:query:sql "
SELECT 
  query,
  calls,
  total_time,
  mean_time,
  rows
FROM pg_stat_statements 
ORDER BY mean_time DESC 
LIMIT 10;
"
```

## 🛠️ Maintenance Operations

### Backup Management

#### Automated Backups
- **Retention**: 7 days (configurable)
- **Window**: 03:00-04:00 UTC
- **Point-in-time Recovery**: Available

#### Manual Snapshots
```bash
# Create manual snapshot
aws rds create-db-snapshot \
  --db-instance-identifier "counselrank-prod-db" \
  --db-snapshot-identifier "manual-backup-$(date +%Y%m%d-%H%M%S)"

# List snapshots
aws rds describe-db-snapshots \
  --db-instance-identifier "counselrank-prod-db"
```

### Performance Tuning

#### Parameter Group Optimization
```bash
# Update parameter group
aws rds modify-db-parameter-group \
  --db-parameter-group-name "counselrank-postgres-16" \
  --parameters "ParameterName=shared_buffers,ParameterValue=512MB,ApplyMethod=pending-reboot"
```

#### Connection Pooling
Consider implementing connection pooling for high-traffic applications:
- **PgBouncer** - Lightweight connection pooler
- **AWS RDS Proxy** - Managed connection pooling

### Scaling Operations

#### Vertical Scaling (Instance Class)
```bash
# Modify instance class
aws rds modify-db-instance \
  --db-instance-identifier "counselrank-prod-db" \
  --db-instance-class "db.t3.large" \
  --apply-immediately
```

#### Storage Scaling
Storage automatically scales up to the maximum allocated storage.

## 🔍 Troubleshooting

### Common Issues

#### Connection Issues
```bash
# Test connection
psql -h your-rds-endpoint.region.rds.amazonaws.com \
     -p 5432 \
     -U counselrank_admin \
     -d counselrank_prod

# Check security groups
aws ec2 describe-security-groups \
  --group-names "counselrank-db-sg"
```

#### Performance Issues
```bash
# Check slow queries
php bin/console doctrine:query:sql "
SELECT 
  query,
  calls,
  total_time,
  mean_time
FROM pg_stat_statements 
WHERE mean_time > 1000
ORDER BY mean_time DESC;
"

# Analyze table statistics
php bin/console doctrine:query:sql "ANALYZE;"
```

#### Migration Issues
```bash
# Check migration status
php bin/console doctrine:migrations:status

# Rollback last migration
php bin/console doctrine:migrations:migrate prev

# Force migration
php bin/console doctrine:migrations:migrate --no-interaction
```

### Log Analysis

#### RDS Logs
```bash
# Download PostgreSQL logs
aws rds download-db-log-file-portion \
  --db-instance-identifier "counselrank-prod-db" \
  --log-file-name "postgresql.log" \
  --starting-token 0 \
  --max-items 1000
```

#### Application Logs
```bash
# Check Symfony logs
tail -f var/log/prod.log

# Check Doctrine query logs
tail -f var/log/doctrine.log
```

## 📈 Cost Optimization

### Instance Right-sizing
- **Monitor CPU/Memory usage** for 2-4 weeks
- **Use CloudWatch metrics** to identify optimal instance class
- **Consider Reserved Instances** for production workloads

### Storage Optimization
- **Enable storage autoscaling** to avoid over-provisioning
- **Monitor storage usage** and adjust accordingly
- **Use General Purpose SSD** for most workloads

### Backup Optimization
- **Adjust retention period** based on compliance requirements
- **Use automated backups** instead of manual snapshots for regular backups
- **Delete old snapshots** to reduce storage costs

## 🔄 Disaster Recovery

### Backup Strategy
1. **Automated Backups** - Daily backups with 7-day retention
2. **Manual Snapshots** - Before major changes
3. **Cross-region Snapshots** - For disaster recovery
4. **Point-in-time Recovery** - Restore to any point within retention period

### Recovery Procedures
```bash
# Restore from snapshot
aws rds restore-db-instance-from-db-snapshot \
  --db-instance-identifier "counselrank-recovery-db" \
  --db-snapshot-identifier "backup-snapshot-20250101"

# Point-in-time recovery
aws rds restore-db-instance-to-point-in-time \
  --source-db-instance-identifier "counselrank-prod-db" \
  --target-db-instance-identifier "counselrank-recovery-db" \
  --restore-time "2025-01-01T12:00:00Z"
```

## 📚 Additional Resources

### Documentation Links
- [AWS RDS PostgreSQL Documentation](https://docs.aws.amazon.com/rds/latest/userguide/CHAP_PostgreSQL.html)
- [Symfony Doctrine Documentation](https://symfony.com/doc/current/doctrine.html)
- [PostgreSQL Performance Tuning](https://wiki.postgresql.org/wiki/Performance_Optimization)

### Monitoring Tools
- [AWS CloudWatch](https://aws.amazon.com/cloudwatch/)
- [AWS RDS Performance Insights](https://aws.amazon.com/rds/performance-insights/)
- [PostgreSQL Monitoring Tools](https://wiki.postgresql.org/wiki/Monitoring)

### Security Resources
- [AWS RDS Security Best Practices](https://docs.aws.amazon.com/rds/latest/userguide/CHAP_BestPractices.Security.html)
- [PostgreSQL Security](https://www.postgresql.org/docs/current/security.html)

---

## 🎉 Next Steps

After successful RDS deployment:

1. **Test Application** - Verify all functionality works with RDS
2. **Update Deployment Scripts** - Modify CI/CD to use RDS
3. **Set Up Monitoring** - Configure alerts and dashboards
4. **Documentation** - Update team documentation
5. **Training** - Train team on RDS management

For questions or issues, refer to the troubleshooting section or contact the development team.
