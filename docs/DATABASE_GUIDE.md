# Complete Database Guide

This comprehensive guide covers everything you need to know about working with databases in the CounselRank.legal platform, including setup, connection, entity creation, migrations, and frontend integration.

## Table of Contents

1. [Quick Start](#quick-start)
2. [Database Connections](#database-connections)
3. [Entity Creation](#entity-creation)
4. [Database Migration Workflow](#database-migration-workflow)
5. [Frontend Database Integration](#frontend-database-integration)
6. [Useful Commands](#useful-commands)
7. [Security Considerations](#security-considerations)
8. [Troubleshooting](#troubleshooting)

## Quick Start

### 1. Start the Database
```bash
# Using Docker Compose (recommended for development)
docker-compose up -d

# Or connect to existing PostgreSQL server
source connect-db.sh
```

### 2. Run Migrations
```bash
cd backend
php bin/console doctrine:migrations:migrate
```

### 3. Verify Connection
```bash
php bin/console doctrine:query:sql 'SELECT version()'
```

## Database Connections

### Local Development (Docker)
```bash
# Connection Details
Host: 127.0.0.1:5433
Database: mcnary_marketing
Username: postgres
Password: postgres
Port: 5433 (mapped from container port 5432)

# Environment Variable
export DATABASE_URL="postgresql://postgres:postgres@127.0.0.1:5433/mcnary_marketing?serverVersion=16&charset=utf8"
```

### Remote Database (counselrank.legal)
```bash
# Update connect-counselrank-db.sh with your credentials
export DATABASE_URL="postgresql://username:password@host:port/database_name?serverVersion=16&charset=utf8"

# Or create .env.local
DATABASE_URL="postgresql://username:password@host:port/database_name?serverVersion=16&charset=utf8"
```

### RDS Staging Database
```bash
# RDS Staging Database
DATABASE_URL="postgresql://counselrank_admin:TulsaSeo122@counselrank-staging-db.cstm2wakq0zs.us-east-1.rds.amazonaws.com:5432/counselrank_staging?serverVersion=16&charset=utf8"
```

### RDS Production Database
```bash
# RDS Production Database
DATABASE_URL="postgresql://counselrank_admin:YOUR_PASSWORD@your-rds-endpoint.region.rds.amazonaws.com:5432/counselrank_prod?serverVersion=16&charset=utf8"
```

### Docker Services Configuration
```yaml
# compose.yaml
services:
  database:
    image: postgres:16-alpine
    environment:
      POSTGRES_DB: mcnary_marketing
      POSTGRES_PASSWORD: postgres
      POSTGRES_USER: postgres
    ports:
      - "5434:5432"
    healthcheck:
      test: ["CMD", "pg_isready", "-d", "mcnary_marketing", "-U", "postgres"]
      timeout: 5s
      retries: 5
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
    private string $id;

    #[ORM\Column(name: 'tenant_id', type: 'uuid', nullable: true)]
    private ?string $tenantId = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank]
    private string $name;

    #[ORM\Column(name: 'created_at', type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(name: 'updated_at', type: 'datetime_immutable')]
    private \DateTimeImmutable $updatedAt;

    public function __construct()
    {
        $this->id = Uuid::v4()->toRfc4122();
        $now = new \DateTimeImmutable();
        $this->createdAt = $now;
        $this->updatedAt = $now;
    }

    // Getters and setters for all properties
}
```

#### 2. Essential Annotations

**Entity Declaration**
```php
#[ORM\Entity]                                    // Marks class as entity
#[ORM\Table(name: 'table_name')]                // Custom table name
#[ORM\UniqueConstraint(columns: ['field1', 'field2'])]  // Unique constraints
```

**Field Types**
```php
#[ORM\Column(type: 'string', length: 255)]      // String with max length
#[ORM\Column(type: 'text', nullable: true)]     // Text field, can be null
#[ORM\Column(type: 'integer')]                  // Integer field
#[ORM\Column(type: 'boolean', options: ['default' => false])]  // Boolean with default
#[ORM\Column(type: 'datetime_immutable')]       // DateTime field
#[ORM\Column(type: 'jsonb')]                    // JSON field (PostgreSQL)
#[ORM\Column(type: 'uuid')]                     // UUID field
```

**API Platform Integration**
```php
#[ApiResource(
    operations: [
        new Get(security: "is_granted('ROLE_USER')"),
        new GetCollection(security: "is_granted('ROLE_USER')"),
        new Post(security: "is_granted('ROLE_ADMIN')"),
        new Put(security: "is_granted('ROLE_ADMIN')"),
        new Delete(security: "is_granted('ROLE_ADMIN')")
    ]
)]
#[ApiFilter(SearchFilter::class, properties: ['name' => 'partial'])]
#[ApiFilter(OrderFilter::class, properties: ['createdAt' => 'DESC'])]
```

**Validation Constraints**
```php
#[Assert\NotBlank]
#[Assert\Length(min: 2, max: 255)]
#[Assert\Email]
#[Assert\Choice(['active', 'inactive'])]
#[Assert\Range(min: 0, max: 100)]
```

#### 3. Multi-Tenancy Support
```php
#[ORM\Column(name: 'tenant_id', type: 'uuid', nullable: true)]
private ?string $tenantId = null;

#[ORM\Column(name: 'client_id', type: 'uuid')]
private string $clientId;
```

#### 4. Timestamps and Lifecycle
```php
#[ORM\Column(name: 'created_at', type: 'datetime_immutable')]
private \DateTimeImmutable $createdAt;

#[ORM\Column(name: 'updated_at', type: 'datetime_immutable')]
private \DateTimeImmutable $updatedAt;

#[ORM\PreUpdate]
public function setUpdatedAt(): void
{
    $this->updatedAt = new \DateTimeImmutable();
}
```

## Database Migration Workflow

### Development Environment

1. **Create a new migration:**
   ```bash
   php bin/console make:migration
   ```

2. **Review the generated migration file** in `migrations/` directory

3. **Run migrations:**
   ```bash
   php bin/console doctrine:migrations:migrate
   ```

4. **Check migration status:**
   ```bash
   php bin/console doctrine:migrations:status
   ```

### Production Deployment

1. **Generate production migration:**
   ```bash
   php bin/console make:migration --env=prod
   ```

2. **Deploy migration files** to production server

3. **Run migrations safely:**
   ```bash
   php bin/console doctrine:migrations:migrate --env=prod --no-interaction
   ```

4. **Verify migration success:**
   ```bash
   php bin/console doctrine:migrations:status --env=prod
   ```

### Migration Best Practices

- **Always backup** production database before running migrations
- **Test migrations** in staging environment first
- **Use transactions** for complex migrations
- **Version control** all migration files
- **Document breaking changes** in migration files

### Rollback Procedures

1. **Check migration history:**
   ```bash
   php bin/console doctrine:migrations:list
   ```

2. **Rollback to specific version:**
   ```bash
   php bin/console doctrine:migrations:migrate prev
   ```

3. **Rollback to specific migration:**
   ```bash
   php bin/console doctrine:migrations:migrate VERSION_NUMBER
   ```

### New Entities That Will Be Created

The following new database tables will be created when you run the migrations:

#### Core Business Entities
- **campaigns** - Marketing campaigns for clients
- **keywords** - SEO keywords and their metrics
- **rankings** - Search engine rankings for keywords
- **reviews** - Customer reviews from various platforms
- **citations** - Business directory citations
- **content_items** - Blog posts, articles, and other content
- **content_briefs** - Content creation briefs
- **audit_runs** - SEO and technical audit runs
- **audit_findings** - Individual findings from audits
- **recommendations** - Actionable recommendations for clients
- **backlinks** - Incoming links to client websites
- **subscriptions** - Client subscription plans
- **invoices** - Billing invoices

#### Existing Entities (Already in Database)
- **users** - System users and staff
- **clients** - Client companies
- **pages** - Website pages
- **faqs** - Frequently asked questions
- **packages** - Service packages
- **media_assets** - Images and other media files
- **leads** - Lead information
- **posts** - Blog posts (legacy)

## Frontend Database Integration

### Overview

The frontend doesn't directly connect to the database. Instead, it communicates with the Symfony backend API, which handles all database operations. This architecture provides:

- **Security**: Database credentials are never exposed to the client
- **Scalability**: Backend can handle multiple frontend instances
- **Maintainability**: Database logic is centralized in the backend
- **Performance**: Backend can implement caching and optimization

### API Service Layer

#### 1. Base API Configuration

Create `src/services/api.ts`:

```typescript
import axios, { AxiosInstance, AxiosResponse } from 'axios';

class ApiService {
  private api: AxiosInstance;

  constructor() {
    this.api = axios.create({
      baseURL: import.meta.env.VITE_API_BASE_URL || 'http://localhost:8000/api',
      timeout: 10000,
      headers: {
        'Content-Type': 'application/json',
      },
    });

    // Request interceptor for authentication
    this.api.interceptors.request.use(
      (config) => {
        const token = localStorage.getItem('auth_token');
        if (token) {
          config.headers.Authorization = `Bearer ${token}`;
        }
        return config;
      },
      (error) => Promise.reject(error)
    );

    // Response interceptor for error handling
    this.api.interceptors.response.use(
      (response: AxiosResponse) => response,
      (error) => {
        if (error.response?.status === 401) {
          // Handle unauthorized access
          localStorage.removeItem('auth_token');
          window.location.href = '/login';
        }
        return Promise.reject(error);
      }
    );
  }

  // Generic CRUD methods
  async get<T>(endpoint: string): Promise<T> {
    const response = await this.api.get<T>(endpoint);
    return response.data;
  }

  async post<T>(endpoint: string, data: any): Promise<T> {
    const response = await this.api.post<T>(endpoint, data);
    return response.data;
  }

  async put<T>(endpoint: string, data: any): Promise<T> {
    const response = await this.api.put<T>(endpoint, data);
    return response.data;
  }

  async delete<T>(endpoint: string): Promise<T> {
    const response = await this.api.delete<T>(endpoint);
    return response.data;
  }
}

export const apiService = new ApiService();
```

#### 2. Entity-Specific Services

Create `src/services/leads.ts`:

```typescript
import { apiService } from './api';

export interface Lead {
  id: string;
  name: string;
  email: string;
  phone?: string;
  company?: string;
  message: string;
  status: 'new' | 'contacted' | 'qualified' | 'converted';
  createdAt: string;
  updatedAt: string;
}

export class LeadService {
  static async getLeads(params?: any): Promise<Lead[]> {
    const queryString = new URLSearchParams(params).toString();
    return apiService.get<Lead[]>(`/leads?${queryString}`);
  }

  static async getLead(id: string): Promise<Lead> {
    return apiService.get<Lead>(`/leads/${id}`);
  }

  static async createLead(data: Partial<Lead>): Promise<Lead> {
    return apiService.post<Lead>('/leads', data);
  }

  static async updateLead(id: string, data: Partial<Lead>): Promise<Lead> {
    return apiService.put<Lead>(`/leads/${id}`, data);
  }

  static async deleteLead(id: string): Promise<void> {
    return apiService.delete<void>(`/leads/${id}`);
  }
}
```

#### 3. React Component Usage

```typescript
import React, { useState, useEffect } from 'react';
import { LeadService, Lead } from '../services/leads';

export const LeadsList: React.FC = () => {
  const [leads, setLeads] = useState<Lead[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const fetchLeads = async () => {
      try {
        const data = await LeadService.getLeads();
        setLeads(data);
      } catch (error) {
        console.error('Failed to fetch leads:', error);
      } finally {
        setLoading(false);
      }
    };

    fetchLeads();
  }, []);

  if (loading) return <div>Loading...</div>;

  return (
    <div>
      {leads.map(lead => (
        <div key={lead.id}>
          <h3>{lead.name}</h3>
          <p>{lead.email}</p>
          <p>Status: {lead.status}</p>
        </div>
      ))}
    </div>
  );
};
```

## Useful Commands

### Database Management
```bash
# Check database status
docker-compose ps

# View logs
docker-compose logs database

# Stop database
docker-compose down

# Reset database
docker-compose down -v && docker-compose up -d

# Check schema
php bin/console doctrine:schema:validate

# Update schema
php bin/console doctrine:schema:update --dump-sql
```

### Entity Management
```bash
# Validate entities
php bin/console doctrine:schema:validate

# Clear cache
php bin/console cache:clear

# Debug routes
php bin/console debug:router

# Check entity mapping
php bin/console doctrine:mapping:info
```

### Migration Commands
```bash
# Check migration status
php bin/console doctrine:migrations:status

# Run migrations
php bin/console doctrine:migrations:migrate

# Generate new migration
php bin/console make:migration

# List migration history
php bin/console doctrine:migrations:list
```

## Security Considerations

### Multi-Tenancy
- All entities include `tenant_id` and `client_id` fields
- API endpoints enforce client scoping
- Users can only access data from their assigned client

### API Security
- JWT authentication required for all endpoints
- Role-based access control (RBAC) enforced
- CORS configured for authorized origins only

### Database Security
- Use environment variables for sensitive data
- Implement proper user permissions
- Regular security updates and backups

### SSL/TLS Configuration
```bash
# SSL/TLS Configuration for RDS
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

## Troubleshooting

### Common Issues

1. **Connection refused:**
   - Check if Docker container is running
   - Verify port mapping in `compose.yaml`

2. **Migration fails:**
   - Check database connection
   - Verify entity annotations
   - Check for syntax errors in migration files

3. **Schema validation errors:**
   - Run `php bin/console doctrine:schema:validate`
   - Check entity mapping annotations
   - Verify database table structure

4. **Frontend API errors:**
   - Verify backend server is running
   - Check CORS configuration
   - Verify authentication tokens

5. **Connection Issues:**
   - Verify the host IP address is correct
   - Check if the database server allows connections from your IP
   - Ensure the database name, username, and password are correct
   - Check if the database server is running and accessible

6. **Permission Issues:**
   - Ensure your database user has CREATE, ALTER, and DROP permissions
   - Check if the database exists and is accessible

### Performance Optimization

1. **Database Indexes**
   - Add indexes for frequently queried fields
   - Use composite indexes for complex queries
   - Monitor query performance

2. **Caching**
   - Implement Redis caching for frequently accessed data
   - Use Symfony cache for API responses
   - Frontend caching for static data

3. **Query Optimization**
   - Use Doctrine query builder for complex queries
   - Implement pagination for large datasets
   - Use eager loading to avoid N+1 queries

### Debug Steps

1. **Test Database Connection:**
   ```bash
   php bin/console doctrine:query:sql 'SELECT version()'
   ```

2. **Check Migration Status:**
   ```bash
   php bin/console doctrine:migrations:status
   ```

3. **Verify Tables Created:**
   ```bash
   php bin/console doctrine:query:sql "SELECT table_name FROM information_schema.tables WHERE table_schema = 'public' ORDER BY table_name;"
   ```

4. **Clear Symfony Cache:**
   ```bash
   php bin/console cache:clear
   ```

## Next Steps

After setting up your database and entities:

1. **Test API endpoints** using the new entities
2. **Create sample data** for development and testing
3. **Set up proper indexes** for performance optimization
4. **Configure backup and monitoring** for production
5. **Implement caching strategies** for better performance

## Related Documentation

- [Database Schema](./DATABASE_SCHEMA.md) - Complete database schema reference
- [Entity Relationship Diagram](./ENTITY_RELATIONSHIP_DIAGRAM.md) - Visual relationship mappings
- [API Documentation](./API_DOCUMENTATION.md) - Complete API reference
- [Setup Guide](./SETUP_GUIDE.md) - Development setup guide
- [Deployment Guide](./DEPLOYMENT_GUIDE.md) - Production deployment

---

**Last Updated:** January 2025  
**Maintained By:** Development Team  
**Status:** Complete and consolidated ✅