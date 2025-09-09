# Complete Database Guide

## Overview

This comprehensive guide covers everything you need to know about working with databases in the CounselRank.legal platform, including setup, connection, entity creation, migrations, and management.

## Table of Contents

1. [Quick Start](#quick-start)
2. [Database Connections](#database-connections)
3. [Entity Creation](#entity-creation)
4. [Database Migrations](#database-migrations)
5. [Database Schema](#database-schema)
6. [Performance Optimization](#performance-optimization)
7. [Backup and Recovery](#backup-and-recovery)
8. [Troubleshooting](#troubleshooting)

## Quick Start

### 1. Start the Database

#### Using Docker Compose (Recommended for Development)
```bash
# Start PostgreSQL database
docker-compose up -d

# Database will be available at:
# Host: localhost
# Port: 5434
# Database: tulsa_seo
# Username: smcnary
# Password: TulsaSeo122
```

#### Using RDS (Production/Staging)
```bash
# Deploy RDS staging instance
cd backend/scripts
./deploy-rds.sh --staging

# Test RDS connection
./test-rds-connection.sh --staging
```

### 2. Run Migrations
```bash
cd backend
php bin/console doctrine:migrations:migrate --no-interaction
```

### 3. Verify Connection
```bash
php bin/console doctrine:query:sql 'SELECT version()'
php bin/console doctrine:schema:validate
```

## Database Connections

### Local Development (Docker)
```bash
# Connection Details
Host: 127.0.0.1:5434
Database: tulsa_seo
Username: smcnary
Password: TulsaSeo122
Port: 5434 (mapped from container port 5432)

# Environment Variable
DATABASE_URL="postgresql://smcnary:TulsaSeo122@127.0.0.1:5434/tulsa_seo?serverVersion=16&charset=utf8"
```

### RDS Staging
```bash
# Connection Details
Host: counselrank-staging-db.cstm2wakq0zs.us-east-1.rds.amazonaws.com
Database: counselrank_staging
Username: counselrank_admin
Password: TulsaSeo122
Port: 5432

# Environment Variable
DATABASE_URL="postgresql://counselrank_admin:TulsaSeo122@counselrank-staging-db.cstm2wakq0zs.us-east-1.rds.amazonaws.com:5432/counselrank_staging?serverVersion=16&charset=utf8"
```

### RDS Production
```bash
# Connection Details
Host: counselrank-production-db.cstm2wakq0zs.us-east-1.rds.amazonaws.com
Database: counselrank_production
Username: counselrank_admin
Password: [SECURE_PASSWORD]
Port: 5432

# Environment Variable
DATABASE_URL="postgresql://counselrank_admin:[SECURE_PASSWORD]@counselrank-production-db.cstm2wakq0zs.us-east-1.rds.amazonaws.com:5432/counselrank_production?serverVersion=16&charset=utf8"
```

### Docker Services Configuration
```yaml
# compose.yaml
services:
  database:
    image: postgres:16-alpine
    environment:
      POSTGRES_DB: tulsa_seo
      POSTGRES_PASSWORD: TulsaSeo122
      POSTGRES_USER: smcnary
    ports:
      - "5434:5432"
    healthcheck:
      test: ["CMD", "pg_isready", "-d", "tulsa_seo", "-U", "smcnary"]
      timeout: 5s
      retries: 5
    volumes:
      - postgres_data:/var/lib/postgresql/data

volumes:
  postgres_data:
```

## Entity Creation

### What Are Entities?

Entities are PHP classes that represent database tables. They use Doctrine ORM annotations to define:
- Table structure
- Field types and constraints
- Relationships between tables
- API endpoints (via API Platform)

### Creating a New Entity

#### 1. Basic Entity Structure
```php
<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
#[ORM\Table(name: 'products')]
#[ApiResource]
class Product
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private string $id;

    #[ORM\Column(name: 'tenant_id', type: 'uuid', nullable: true)]
    private ?string $tenantId = null;

    #[ORM\Column(name: 'created_at', type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(name: 'updated_at', type: 'datetime_immutable')]
    private \DateTimeImmutable $updatedAt;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank]
    private string $name;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    #[Assert\NotBlank]
    #[Assert\Positive]
    private string $price;

    public function __construct()
    {
        $this->id = Uuid::v4()->toRfc4122();
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    // Getters and setters
    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    // ... other getters and setters
}
```

#### 2. Entity Relationships
```php
// One-to-Many relationship
#[ORM\OneToMany(mappedBy: 'client', targetEntity: User::class, cascade: ['persist'], orphanRemoval: true)]
private Collection $users;

// Many-to-One relationship
#[ORM\ManyToOne(targetEntity: Client::class, inversedBy: 'users')]
#[ORM\JoinColumn(name: 'client_id', nullable: false)]
private Client $client;

// Many-to-Many relationship
#[ORM\ManyToMany(targetEntity: Tag::class, inversedBy: 'products')]
#[ORM\JoinTable(name: 'product_tags')]
private Collection $tags;
```

#### 3. Entity Validation
```php
#[ORM\Column(type: 'string', length: 255)]
#[Assert\NotBlank(message: 'Name cannot be blank')]
#[Assert\Length(min: 2, max: 255, minMessage: 'Name must be at least 2 characters', maxMessage: 'Name cannot exceed 255 characters')]
private string $name;

#[ORM\Column(type: 'string', length: 255)]
#[Assert\Email(message: 'Please enter a valid email address')]
private string $email;

#[ORM\Column(type: 'string', length: 20)]
#[Assert\Regex(pattern: '/^\+?[1-9]\d{1,14}$/', message: 'Please enter a valid phone number')]
private string $phone;
```

### Entity Best Practices

1. **Always use UUIDs** for primary keys
2. **Include tenant_id** for multi-tenancy support
3. **Add timestamps** (created_at, updated_at)
4. **Use proper validation** constraints
5. **Define relationships** clearly
6. **Add API Platform annotations** for REST endpoints

## Database Migrations

### Creating Migrations

#### 1. Generate Migration
```bash
# Create a new migration
php bin/console make:migration

# Or create migration for specific entity
php bin/console doctrine:migrations:diff
```

#### 2. Review Generated Migration
```php
<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250115000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create products table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE products (
            id UUID NOT NULL,
            tenant_id UUID DEFAULT NULL,
            created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
            updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
            name VARCHAR(255) NOT NULL,
            description TEXT DEFAULT NULL,
            price NUMERIC(10, 2) NOT NULL,
            PRIMARY KEY(id)
        )');
        
        $this->addSql('CREATE INDEX IDX_PRODUCTS_TENANT_ID ON products (tenant_id)');
        $this->addSql('CREATE INDEX IDX_PRODUCTS_CREATED_AT ON products (created_at)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE products');
    }
}
```

### Running Migrations

#### 1. Check Migration Status
```bash
php bin/console doctrine:migrations:status
```

#### 2. Run Migrations
```bash
# Run all pending migrations
php bin/console doctrine:migrations:migrate --no-interaction

# Run specific migration
php bin/console doctrine:migrations:execute --up Version20250115000000

# Rollback last migration
php bin/console doctrine:migrations:migrate prev
```

#### 3. Validate Schema
```bash
php bin/console doctrine:schema:validate
```

### Migration Best Practices

1. **Always review** generated migrations before running
2. **Test migrations** on development database first
3. **Backup database** before running migrations in production
4. **Use transactions** for complex migrations
5. **Add indexes** for performance-critical queries

## Database Schema

### Core Tables

#### Organizations
```sql
CREATE TABLE organizations (
    id UUID PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    domain VARCHAR(255) UNIQUE,
    created_at TIMESTAMP NOT NULL,
    updated_at TIMESTAMP NOT NULL
);
```

#### Tenants
```sql
CREATE TABLE tenants (
    id UUID PRIMARY KEY,
    organization_id UUID REFERENCES organizations(id),
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    created_at TIMESTAMP NOT NULL,
    updated_at TIMESTAMP NOT NULL
);
```

#### Clients
```sql
CREATE TABLE clients (
    id UUID PRIMARY KEY,
    agency_id UUID REFERENCES agencies(id),
    tenant_id UUID REFERENCES tenants(id),
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    description TEXT,
    website VARCHAR(255),
    phone VARCHAR(20),
    address TEXT,
    city VARCHAR(100),
    state VARCHAR(50),
    zip_code VARCHAR(20),
    country VARCHAR(50),
    industry VARCHAR(50),
    status VARCHAR(20) DEFAULT 'active',
    metadata JSONB,
    google_business_profile JSONB,
    google_search_console JSONB,
    google_analytics JSONB,
    created_at TIMESTAMP NOT NULL,
    updated_at TIMESTAMP NOT NULL
);
```

#### Users
```sql
CREATE TABLE users (
    id UUID PRIMARY KEY,
    organization_id UUID REFERENCES organizations(id),
    agency_id UUID REFERENCES agencies(id),
    tenant_id UUID REFERENCES tenants(id),
    email VARCHAR(255) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    first_name VARCHAR(100),
    last_name VARCHAR(100),
    role VARCHAR(50) NOT NULL,
    status VARCHAR(20) DEFAULT 'active',
    last_login_at TIMESTAMP,
    metadata JSONB,
    created_at TIMESTAMP NOT NULL,
    updated_at TIMESTAMP NOT NULL
);
```

### Audit System Tables

#### Audit Intakes
```sql
CREATE TABLE audit_intakes (
    id UUID PRIMARY KEY,
    client_id UUID REFERENCES clients(id),
    requested_by UUID REFERENCES users(id),
    website_url VARCHAR(255) NOT NULL,
    business_name VARCHAR(255) NOT NULL,
    industry VARCHAR(100),
    target_keywords JSONB,
    competitors JSONB,
    goals JSONB,
    budget_range VARCHAR(50),
    timeline VARCHAR(50),
    contact_email VARCHAR(255),
    contact_phone VARCHAR(20),
    status VARCHAR(20) DEFAULT 'pending',
    metadata JSONB,
    created_at TIMESTAMP NOT NULL,
    updated_at TIMESTAMP NOT NULL
);
```

#### Audit Runs
```sql
CREATE TABLE audit_runs (
    id UUID PRIMARY KEY,
    audit_intake_id UUID REFERENCES audit_intakes(id),
    client_id UUID REFERENCES clients(id),
    initiated_by UUID REFERENCES users(id),
    status VARCHAR(20) DEFAULT 'pending',
    scheduled_at TIMESTAMP,
    started_at TIMESTAMP,
    completed_at TIMESTAMP,
    parameters JSONB,
    results JSONB,
    created_at TIMESTAMP NOT NULL,
    updated_at TIMESTAMP NOT NULL
);
```

### Indexes for Performance

```sql
-- Client indexes
CREATE INDEX idx_clients_agency_id ON clients(agency_id);
CREATE INDEX idx_clients_tenant_id ON clients(tenant_id);
CREATE INDEX idx_clients_status ON clients(status);
CREATE INDEX idx_clients_created_at ON clients(created_at);

-- User indexes
CREATE INDEX idx_users_organization_id ON users(organization_id);
CREATE INDEX idx_users_agency_id ON users(agency_id);
CREATE INDEX idx_users_tenant_id ON users(tenant_id);
CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_users_role ON users(role);
CREATE INDEX idx_users_status ON users(status);

-- Audit indexes
CREATE INDEX idx_audit_intakes_client_id ON audit_intakes(client_id);
CREATE INDEX idx_audit_intakes_status ON audit_intakes(status);
CREATE INDEX idx_audit_runs_client_id ON audit_runs(client_id);
CREATE INDEX idx_audit_runs_status ON audit_runs(status);
```

## Performance Optimization

### Query Optimization

#### 1. Use Proper Indexes
```sql
-- Add indexes for frequently queried columns
CREATE INDEX idx_clients_name ON clients(name);
CREATE INDEX idx_users_last_login ON users(last_login_at);
CREATE INDEX idx_audit_runs_scheduled_at ON audit_runs(scheduled_at);
```

#### 2. Optimize Queries
```php
// Use DQL for complex queries
$query = $entityManager->createQuery('
    SELECT c, u 
    FROM App\Entity\Client c 
    JOIN c.users u 
    WHERE c.status = :status 
    AND u.role = :role
');
$query->setParameter('status', 'active');
$query->setParameter('role', 'ROLE_CLIENT_ADMIN');
```

#### 3. Use Query Builder
```php
$qb = $entityManager->createQueryBuilder();
$qb->select('c', 'u')
   ->from(Client::class, 'c')
   ->join('c.users', 'u')
   ->where('c.status = :status')
   ->andWhere('u.role = :role')
   ->setParameter('status', 'active')
   ->setParameter('role', 'ROLE_CLIENT_ADMIN');
```

### Connection Pooling

#### 1. Configure Connection Pool
```yaml
# config/packages/doctrine.yaml
doctrine:
    dbal:
        connections:
            default:
                url: '%env(DATABASE_URL)%'
                options:
                    pool_size: 10
                    pool_timeout: 30
```

#### 2. Use Read Replicas
```yaml
# config/packages/doctrine.yaml
doctrine:
    dbal:
        connections:
            default:
                url: '%env(DATABASE_URL)%'
            read:
                url: '%env(DATABASE_READ_URL)%'
```

## Backup and Recovery

### Automated Backups

#### 1. RDS Automated Backups
```bash
# Enable automated backups for RDS
aws rds modify-db-instance \
    --db-instance-identifier counselrank-staging-db \
    --backup-retention-period 7 \
    --preferred-backup-window "03:00-04:00" \
    --preferred-maintenance-window "sun:04:00-sun:05:00"
```

#### 2. Manual Backup Script
```bash
#!/bin/bash
# backup-database.sh

DB_HOST="localhost"
DB_NAME="tulsa_seo"
DB_USER="smcnary"
BACKUP_DIR="/backups"
DATE=$(date +%Y%m%d_%H%M%S)

# Create backup
pg_dump -h $DB_HOST -U $DB_USER -d $DB_NAME > $BACKUP_DIR/backup_$DATE.sql

# Compress backup
gzip $BACKUP_DIR/backup_$DATE.sql

# Remove old backups (keep last 7 days)
find $BACKUP_DIR -name "backup_*.sql.gz" -mtime +7 -delete
```

### Recovery Procedures

#### 1. Restore from Backup
```bash
# Restore from backup
gunzip backup_20250115_120000.sql.gz
psql -h localhost -U smcnary -d tulsa_seo < backup_20250115_120000.sql
```

#### 2. Point-in-Time Recovery (RDS)
```bash
# Restore to specific time
aws rds restore-db-instance-to-point-in-time \
    --source-db-instance-identifier counselrank-staging-db \
    --target-db-instance-identifier counselrank-staging-db-restored \
    --restore-time 2025-01-15T12:00:00Z
```

## Troubleshooting

### Common Issues

#### 1. Connection Issues
```bash
# Test database connection
php bin/console doctrine:query:sql 'SELECT 1'

# Check database status
php bin/console doctrine:schema:validate

# Test specific connection
php bin/console doctrine:query:sql 'SELECT version()'
```

#### 2. Migration Issues
```bash
# Check migration status
php bin/console doctrine:migrations:status

# Reset migrations (development only)
php bin/console doctrine:migrations:version --delete --all
php bin/console doctrine:migrations:migrate --no-interaction

# Fix migration conflicts
php bin/console doctrine:migrations:sync-metadata-storage
```

#### 3. Performance Issues
```bash
# Analyze slow queries
php bin/console doctrine:query:sql 'SELECT * FROM pg_stat_activity WHERE state = '\''active'\'''

# Check table sizes
php bin/console doctrine:query:sql 'SELECT schemaname,tablename,pg_size_pretty(pg_total_relation_size(schemaname||'\''.'\''||tablename)) as size FROM pg_tables ORDER BY pg_total_relation_size(schemaname||'\''.'\''||tablename) DESC;'
```

#### 4. Schema Validation Issues
```bash
# Validate schema
php bin/console doctrine:schema:validate

# Update schema from entities
php bin/console doctrine:schema:update --dump-sql
php bin/console doctrine:schema:update --force
```

### Debug Commands

```bash
# Database information
php bin/console doctrine:query:sql 'SELECT current_database(), current_user, version()'

# Table information
php bin/console doctrine:query:sql 'SELECT table_name FROM information_schema.tables WHERE table_schema = '\''public'\'' ORDER BY table_name'

# Index information
php bin/console doctrine:query:sql 'SELECT indexname, tablename FROM pg_indexes WHERE schemaname = '\''public'\'' ORDER BY tablename, indexname'

# Connection information
php bin/console doctrine:query:sql 'SELECT * FROM pg_stat_activity WHERE datname = current_database()'
```

## Related Documentation

- [Setup Guide](./SETUP_GUIDE.md) - Development environment setup
- [API Documentation](./API_DOCUMENTATION.md) - Complete API reference
- [Authentication Guide](./AUTHENTICATION_GUIDE.md) - Authentication system
- [Deployment Guide](./DEPLOYMENT_GUIDE.md) - Production deployment

---

**Last Updated:** September 9, 2025  
**Maintained By:** Development Team  
**Status:** Complete and consolidated ✅
