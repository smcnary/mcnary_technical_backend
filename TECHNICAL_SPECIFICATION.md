# Technical Specification - McNary Technical Backend

## Overview

The McNary Technical Backend is a modern, multi-tenant API-first backend application built with Symfony 7.3 and API Platform 4.1. It provides a robust foundation for content management, user authentication, and multi-tenant operations with comprehensive security and scalability features.

## Architecture

### Technology Stack

- **Framework**: Symfony 7.3 (PHP 8.2+)
- **API Platform**: API Platform 4.1 with Doctrine ORM
- **Database**: PostgreSQL 16 with Doctrine 3.5
- **Authentication**: JWT-based authentication via LexikJWTAuthenticationBundle
- **Security**: Symfony Security Bundle with role-based access control
- **Containerization**: Docker with Docker Compose
- **CORS**: NelmioCorsBundle for cross-origin resource sharing

### Core Architecture Principles

- **API-First Design**: RESTful API endpoints with automatic documentation
- **Multi-Tenancy**: Isolated data per tenant with automatic filtering
- **Stateless Operations**: JWT-based authentication for scalable deployments
- **Entity-Driven Development**: Doctrine ORM with automatic API resource generation
- **Security by Default**: Comprehensive access control and validation

## Multi-Tenancy Implementation

### Tenant Isolation Strategy

The application implements **database-level multi-tenancy** where each tenant's data is automatically filtered based on the authenticated user's tenant association.

#### Key Components

1. **Tenant Entity** (`src/Entity/Tenant.php`)
   - Unique identifier (UUID)
   - Tenant name and slug
   - Status tracking (trial, active, suspended)
   - Timezone configuration
   - Audit timestamps

2. **Tenant Extension** (`src/ApiPlatform/TenantExtension.php`)
   - Automatic query filtering for all API operations
   - Ensures data isolation between tenants
   - Integrates with Symfony Security for user context

3. **Tenant Resolver** (`src/MultiTenancy/TenantResolver.php`)
   - Resolves tenant context from requests
   - Supports subdomain and header-based tenant identification

### Data Isolation

- **Automatic Filtering**: All entity queries automatically include tenant filtering
- **User Association**: Users are linked to specific tenants via `tenant_id` field
- **Cross-Tenant Prevention**: API operations cannot access data from other tenants

## Entity Model

### Core Entities

#### User Management
- **User** (`src/Entity/User.php`)
  - UUID-based identification
  - Email-based authentication
  - Role-based access control
  - Tenant association
  - Status tracking (invited, active, suspended)

#### Content Management
- **Site** (`src/Entity/Site.php`)
  - Multi-site support within tenants
  - SEO metadata association
  - Page and post organization

- **Page** (`src/Entity/Page.php`)
  - Static content pages
  - SEO optimization support
  - Tenant isolation

- **Post** (`src/Entity/Post.php`)
  - Dynamic content entries
  - Category and tag classification
  - SEO metadata

- **Form** (`src/Entity/Form.php`)
  - Dynamic form definitions
  - Form submission tracking
  - Tenant-specific forms

#### Lead Management
- **Lead** (`src/Entity/Lead.php`)
  - Public lead capture (POST without authentication)
  - Comprehensive lead information (contact details, practice areas, budget)
  - UTM tracking and analytics data
  - IP address and user agent capture
  - Admin-only read access with detailed information
  - Lead status tracking (pending, contacted, qualified, disqualified)

#### Marketing Content
- **CaseStudy** (`src/Entity/CaseStudy.php`)
  - Public read access for marketing purposes
  - Admin-only write operations
  - Practice area categorization
  - Metrics and performance data (JSON)
  - Hero image support
  - Sortable ordering system

- **Faq** (`src/Entity/Faq.php`)
  - Public read access for customer support
  - Admin-only write operations
  - Question-answer format
  - Active/inactive status management
  - Sortable ordering system

#### Organization
- **Category** (`src/Entity/Category.php`)
  - Content categorization
  - Hierarchical organization
  - Tenant isolation

- **Tag** (`src/Entity/Tag.php`)
  - Content tagging system
  - Flexible categorization
  - Tenant isolation

- **SEO Meta** (`src/Entity/SeoMeta.php`)
  - Search engine optimization
  - Meta tags and descriptions
  - Entity association

### Entity Relationships

```
Tenant (1) ←→ (N) User
Tenant (1) ←→ (N) Site
Tenant (1) ←→ (N) Lead
Tenant (1) ←→ (N) CaseStudy
Tenant (1) ←→ (N) Faq
Site (1) ←→ (N) Page
Site (1) ←→ (N) Post
Post (N) ←→ (N) Category
Post (N) ←→ (N) Tag
Page/Post (1) ←→ (1) SeoMeta
Site (1) ←→ (N) Form
Form (1) ←→ (N) FormSubmission
```

## API Design

### API Platform Integration

- **Automatic Resource Generation**: All entities automatically become API resources
- **RESTful Endpoints**: Standard CRUD operations with automatic validation
- **OpenAPI Documentation**: Self-documenting API with interactive testing
- **Serialization**: Automatic JSON serialization with configurable groups

### Endpoint Structure

```
/api
├── /auth/login          # JWT authentication
├── /users               # User management
├── /tenants             # Tenant management (admin only)
├── /sites               # Site management
├── /pages               # Page management
├── /posts               # Post management
├── /categories          # Category management
├── /tags                # Tag management
├── /forms               # Form management
├── /form_submissions    # Form submission tracking
├── /leads               # Lead capture (public POST, admin read)
├── /case_studies        # Case studies (public read, admin write)
└── /faqs                # FAQs (public read, admin write)
```

### API Features

- **Pagination**: Automatic pagination for collection endpoints
- **Filtering**: Dynamic filtering and search capabilities
- **Sorting**: Configurable sorting options
- **Validation**: Automatic input validation and error handling
- **Rate Limiting**: Built-in API rate limiting (configurable)

## Security Implementation

### Authentication

- **JWT Tokens**: Stateless authentication with configurable expiration
- **Password Security**: Secure password hashing with auto-detection
- **Session Management**: Stateless design for horizontal scaling

### Authorization

- **Role-Based Access Control**: User roles determine API access
- **Tenant Isolation**: Automatic data filtering prevents cross-tenant access
- **Resource-Level Security**: Fine-grained access control per entity
- **Admin Privileges**: Specialized admin role for tenant management

### Security Features

- **CORS Configuration**: Configurable cross-origin resource sharing
- **Input Validation**: Comprehensive validation with Symfony Validator
- **SQL Injection Prevention**: Doctrine ORM with parameterized queries
- **XSS Protection**: Automatic output escaping and validation

## Database Design

### PostgreSQL Configuration

- **Version**: PostgreSQL 16 (Alpine Linux)
- **Port**: 5433 (host mapping)
- **Health Checks**: Automatic database readiness verification
- **Data Persistence**: Docker volumes for data persistence

### Schema Management

- **Migrations**: Doctrine migrations for version-controlled schema changes
- **UUID Primary Keys**: Secure, globally unique identifiers
- **Audit Fields**: Automatic timestamp tracking (created_at, updated_at)
- **Indexing**: Optimized indexes for tenant filtering and common queries

### Data Integrity

- **Foreign Key Constraints**: Referential integrity enforcement
- **Unique Constraints**: Tenant-scoped uniqueness (e.g., email per tenant)
- **Validation Rules**: Database-level and application-level validation

## Development Workflow

### Environment Setup

1. **Docker Environment**
   ```bash
   docker-compose up -d
   ```

2. **Database Connection**
   ```bash
   ./connect-db.sh
   ```

3. **Dependencies**
   ```bash
   composer install
   ```

### Command Line Tools

- **User Creation**: `bin/console app:create-user`
- **Database Migrations**: `bin/console doctrine:migrations:migrate`
- **Cache Management**: `bin/console cache:clear`

### Development Features

- **Symfony Profiler**: Development debugging and profiling
- **API Testing**: Interactive API documentation and testing
- **Hot Reloading**: Automatic code reloading during development

## Deployment

### Production Considerations

- **Environment Variables**: Secure configuration management
- **Database Security**: Production-grade PostgreSQL configuration
- **SSL/TLS**: HTTPS enforcement for all API endpoints
- **Monitoring**: Health checks and performance monitoring
- **Backup Strategy**: Automated database backup procedures

### Scaling Strategy

- **Horizontal Scaling**: Stateless design supports multiple instances
- **Load Balancing**: API-first design enables easy load balancer integration
- **Database Scaling**: Read replicas and connection pooling support
- **Caching**: Redis integration for performance optimization

## Performance & Optimization

### Caching Strategy

- **HTTP Caching**: Configurable cache headers and ETags
- **Database Query Optimization**: Efficient tenant filtering
- **API Response Optimization**: Selective field loading and pagination

### Monitoring & Observability

- **Health Checks**: Database and application health monitoring
- **Performance Metrics**: API response time tracking
- **Error Logging**: Comprehensive error logging and alerting

## Future Enhancements

### Planned Features

- **Webhook System**: Real-time event notifications
- **File Management**: Document and media file handling
- **Advanced Analytics**: Usage tracking and reporting
- **Multi-Language Support**: Internationalization features
- **API Versioning**: Backward-compatible API evolution

### Integration Opportunities

- **Frontend Frameworks**: React, Vue.js, Angular integration
- **Mobile Applications**: Native mobile app support
- **Third-Party Services**: CRM, marketing automation integration
- **E-commerce**: Shopping cart and payment processing

## Support & Maintenance

### Documentation

- **API Documentation**: Self-updating OpenAPI documentation
- **Code Documentation**: Comprehensive PHPDoc annotations
- **Setup Guides**: Step-by-step installation and configuration

### Maintenance Procedures

- **Regular Updates**: Security patches and dependency updates
- **Database Maintenance**: Regular optimization and cleanup
- **Performance Tuning**: Continuous monitoring and optimization
- **Security Audits**: Regular security assessments and updates

---

*This technical specification is a living document that should be updated as the application evolves and new features are implemented.*
