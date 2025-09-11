# Complete API Documentation

This comprehensive guide provides complete documentation for the CounselRank.legal REST API v1, including all endpoints, authentication, role-based access control, security implementation, and practical examples.

## Table of Contents

1. [Overview](#overview)
2. [Authentication](#authentication)
3. [Role-Based Access Control](#role-based-access-control)
4. [Common Query Parameters](#common-query-parameters)
5. [API Endpoints](#api-endpoints)
6. [Request/Response Examples](#requestresponse-examples)
7. [Security Implementation](#security-implementation)
8. [API Platform Features](#api-platform-features)
9. [Testing the API](#testing-the-api)
10. [Rate Limiting & Performance](#rate-limiting--performance)
11. [Troubleshooting](#troubleshooting)

## Overview

The CounselRank.legal API is built on Symfony with API Platform, providing a comprehensive REST API for managing clients, users, campaigns, SEO data, and more. The API follows RESTful principles and includes automatic documentation, pagination, filtering, and sorting capabilities.

### Base URL
- **Development**: `http://localhost:8000`
- **Production**: `https://yourdomain.com`

### API Version
- **Current Version**: v1
- **API Prefix**: `/api/v1`

## Authentication

### JWT Bearer Token

All API endpoints require authentication using JWT Bearer tokens (except where noted as `PUBLIC_ACCESS`).

```bash
# Include in request headers
Authorization: Bearer <your-jwt-token>
```

### Getting a Token

#### Client Login
```bash
# Client login endpoint
POST /api/v1/clients/login
Content-Type: application/json

{
  "email": "admin@acmelaw.com",
  "password": "securepassword123"
}

# Response
{
  "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9...",
  "user": {
    "id": "uuid",
    "email": "admin@acmelaw.com",
    "name": "John Doe",
    "role": "ROLE_CLIENT_ADMIN",
    "status": "active",
    "client_id": "uuid",
    "tenant_id": "uuid",
    "organization_id": "uuid",
    "created_at": "2025-01-XX...",
    "last_login_at": "2025-01-XX..."
  },
  "client": {
    "id": "uuid",
    "name": "Acme Law Firm",
    "slug": "acme-law-firm",
    "description": "Premier legal services in downtown",
    "website": "https://acmelaw.com",
    "phone": "+1-555-0123",
    "address": "123 Main Street",
    "city": "Downtown",
    "state": "CA",
    "zip_code": "90210",
    "country": "USA",
    "industry": "law",
    "status": "active"
  }
}
```

#### General Login
```bash
# General login endpoint
POST /api/v1/auth/login
Content-Type: application/json

{
  "email": "user@example.com",
  "password": "userpassword123"
}

# Response
{
  "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9...",
  "user": {
    "id": "uuid",
    "email": "user@example.com",
    "name": "John Doe",
    "roles": ["ROLE_CLIENT_ADMIN"],
    "client_id": "uuid",
    "tenant_id": "uuid",
    "status": "active",
    "created_at": "2024-01-15T10:30:00+00:00",
    "last_login_at": "2024-01-15T10:30:00+00:00"
  }
}
```

### Token Refresh

```bash
POST /api/v1/auth/refresh
Content-Type: application/json

{
  "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9..."
}
```

### Logout

```bash
POST /api/v1/auth/logout
Authorization: Bearer <token>

# Response
{
  "message": "Logged out successfully"
}
```

## Role-Based Access Control

### User Roles

| Role | Description | Access Level |
|------|-------------|--------------|
| `ROLE_AGENCY_ADMIN` | Agency administrator | Full access to all data and operations |
| `ROLE_AGENCY_STAFF` | Agency staff member | Read access to all data, limited write access |
| `ROLE_CLIENT_ADMIN` | Client administrator | Access only to their client's data |
| `ROLE_CLIENT_STAFF` | Client staff member | Limited access to their client's data |
| `ROLE_SYSTEM_ADMIN` | System administrator | Full system access |
| `PUBLIC_ACCESS` | Public endpoints | No authentication required |

### Access Control Patterns

```php
// Agency admin or staff access
security: "is_granted('ROLE_AGENCY_ADMIN') or is_granted('ROLE_AGENCY_STAFF')"

// Client-scoped access (user can only access their client's data)
security: "is_granted('ROLE_CLIENT_ADMIN') and object.getClientId() == user.getClientId()"

// Public access (no authentication required)
security: "PUBLIC_ACCESS"
```

### Access Control Matrix

| Role | Client Dashboard | Agency Dashboard | System Admin | Client Management |
|------|------------------|------------------|--------------|-------------------|
| ROLE_CLIENT_ADMIN | ✅ Full | ❌ | ❌ | ❌ |
| ROLE_CLIENT_STAFF | ✅ Limited | ❌ | ❌ | ❌ |
| ROLE_AGENCY_ADMIN | ❌ | ✅ Full | ❌ | ✅ Full |
| ROLE_AGENCY_STAFF | ❌ | ✅ Limited | ❌ | ✅ View |
| ROLE_SYSTEM_ADMIN | ✅ Full | ✅ Full | ✅ Full | ✅ Full |

## Common Query Parameters

### Pagination
```bash
?page=1&per_page=25
```

### Sorting
```bash
?sort=createdAt&order=desc
?sort=name&order=asc
```

### Filtering
```bash
?status=active&client_id=uuid
?created_at[gte]=2025-01-01&created_at[lte]=2025-01-31
```

### Field Selection
```bash
?fields=id,name,status
```

## API Endpoints

### Authentication Endpoints

#### Get Current User
```http
GET /api/v1/me
Authorization: Bearer <token>
```

**Response:**
```json
{
  "id": "uuid",
  "email": "user@example.com",
  "name": "John Doe",
  "roles": ["ROLE_CLIENT_ADMIN"],
  "client_id": "uuid",
  "tenant_id": "uuid",
  "status": "active",
  "created_at": "2024-01-15T10:30:00+00:00",
  "last_login_at": "2024-01-15T10:30:00+00:00"
}
```

### Client Management Endpoints

#### List Clients
```http
GET /api/v1/clients?page=1&per_page=25&sort=name&order=asc
Authorization: Bearer <token>
Required Role: ROLE_AGENCY_ADMIN, ROLE_AGENCY_STAFF
```

#### Create Client
```http
POST /api/v1/clients
Authorization: Bearer <token>
Required Role: ROLE_AGENCY_ADMIN
Content-Type: application/json

{
  "name": "Acme Corporation",
  "slug": "acme-corporation",
  "description": "A leading technology company specializing in innovative solutions",
  "website": "https://www.acme.com",
  "phone": "+1-555-123-4567",
  "address": "123 Business Street",
  "city": "San Francisco",
  "state": "CA",
  "zip_code": "94105",
  "tenant_id": "550e8400-e29b-41d4-a716-446655440002"
}
```

**Minimal Request:**
```json
{
  "name": "Acme Corporation"
}
```

#### Update Client
```http
PATCH /api/v1/clients/{id}
Authorization: Bearer <token>
Required Role: ROLE_AGENCY_ADMIN
Content-Type: application/json

{
  "name": "Acme Corporation Updated",
  "description": "Updated description for Acme Corporation",
  "status": "active",
  "metadata": {
    "industry": "Technology",
    "founded": "1995",
    "employees": "1000+"
  },
  "google_business_profile": {
    "profile_id": "gcid:123456789",
    "rating": 4.8,
    "reviews_count": 150
  },
  "google_search_console": {
    "property": "https://www.acme.com",
    "verification_status": "verified"
  },
  "google_analytics": {
    "property_id": "GA4-123456789",
    "tracking_id": "G-XXXXXXXXXX"
  }
}
```

#### Get Client Locations
```http
GET /api/v1/clients/{id}/locations
Authorization: Bearer <token>
Required Role: ROLE_AGENCY_ADMIN, ROLE_AGENCY_STAFF, ROLE_CLIENT_ADMIN (own client)
```

### User Management Endpoints

#### List Users
```http
GET /api/v1/users?page=1&per_page=25&sort=createdAt&order=desc
Authorization: Bearer <token>
Required Role: ROLE_AGENCY_ADMIN, ROLE_CLIENT_ADMIN
```

#### Create User
```http
POST /api/v1/users
Authorization: Bearer <token>
Required Role: ROLE_AGENCY_ADMIN, ROLE_CLIENT_ADMIN
Content-Type: application/json

{
  "email": "newuser@example.com",
  "name": "Jane Smith",
  "role": "ROLE_CLIENT_STAFF",
  "client_id": "550e8400-e29b-41d4-a716-446655440001",
  "tenant_id": "550e8400-e29b-41d4-a716-446655440002",
  "status": "invited"
}
```

**Minimal Request:**
```json
{
  "email": "newuser@example.com",
  "name": "Jane Smith",
  "role": "ROLE_CLIENT_STAFF"
}
```

#### Update User
```http
PATCH /api/v1/users/{id}
Authorization: Bearer <token>
Required Role: ROLE_AGENCY_ADMIN, ROLE_CLIENT_ADMIN
Content-Type: application/json

{
  "name": "Jane Smith Updated",
  "role": "ROLE_CLIENT_ADMIN",
  "client_id": "550e8400-e29b-41d4-a716-446655440001",
  "status": "active",
  "metadata": {
    "department": "Marketing",
    "phone_extension": "1234",
    "hire_date": "2024-01-15"
  }
}
```

### Public Content Endpoints

#### Get Page by Slug
```http
GET /api/v1/pages?slug=about
Authorization: Bearer <token>
Required Role: PUBLIC_ACCESS
```

#### List Pages
```http
GET /api/v1/pages?page=1&per_page=15&type=blog&status=published
Authorization: Bearer <token>
Required Role: PUBLIC_ACCESS
```

#### List FAQs
```http
GET /api/v1/faqs?isActive=true&sort=sort&order=asc
Authorization: Bearer <token>
Required Role: PUBLIC_ACCESS
```

**Query Parameters:**
- `page`: Page number (default: 1)
- `per_page`: Items per page (default: 20, max: 100)
- `sort`: Sort field with direction (e.g., "sort_order", "-created_at")
- `category`: Filter by category
- `search`: Search in question/answer text
- `client_id`: Filter by client ID

#### List Packages
```http
GET /api/v1/packages?status=active&sort=name&order=asc
Authorization: Bearer <token>
Required Role: ROLE_AGENCY_ADMIN, ROLE_AGENCY_STAFF, ROLE_CLIENT_ADMIN, ROLE_CLIENT_STAFF
```

#### Get Media Asset
```http
GET /api/v1/media-assets/{id}
Authorization: Bearer <token>
Required Role: PUBLIC_ACCESS
```

#### List Media Assets
```http
GET /api/v1/media-assets?page=1&per_page=20&type=image&status=active
Authorization: Bearer <token>
Required Role: PUBLIC_ACCESS
```

### Campaign Management Endpoints

#### List Campaigns
```http
GET /api/v1/campaigns?client_id=uuid&status=active&type=seo
Authorization: Bearer <token>
Required Role: ROLE_AGENCY_ADMIN, ROLE_AGENCY_STAFF, ROLE_CLIENT_ADMIN
```

#### Create Campaign
```http
POST /api/v1/campaigns
Authorization: Bearer <token>
Required Role: ROLE_AGENCY_ADMIN, ROLE_CLIENT_ADMIN
Content-Type: application/json

{
  "name": "Q1 SEO Campaign",
  "description": "Focus on local SEO and content marketing",
  "type": "seo",
  "status": "draft",
  "clientId": "550e8400-e29b-41d4-a716-446655440000",
  "startDate": "2025-01-01T00:00:00Z",
  "endDate": "2025-03-31T23:59:59Z",
  "budget": 7500.00,
  "goals": ["increase_rankings", "generate_leads"],
  "metrics": ["organic_traffic", "conversion_rate"]
}
```

### SEO Tools Endpoints

#### List Keywords
```http
GET /api/v1/keywords?client_id=uuid&status=active&difficulty=medium
Authorization: Bearer <token>
Required Role: ROLE_AGENCY_ADMIN, ROLE_AGENCY_STAFF, ROLE_CLIENT_ADMIN
```

#### List Rankings
```http
GET /api/v1/rankings?keyword_id=uuid&from=2025-01-01&to=2025-01-31
Authorization: Bearer <token>
Required Role: ROLE_AGENCY_ADMIN, ROLE_AGENCY_STAFF, ROLE_CLIENT_ADMIN
```

#### Get Rankings Summary
```http
GET /api/v1/rankings/summary?client_id=uuid&date_from=2025-01-01&date_to=2025-01-31
Authorization: Bearer <token>
Required Role: ROLE_AGENCY_ADMIN, ROLE_AGENCY_STAFF, ROLE_CLIENT_ADMIN
```

#### List Backlinks
```http
GET /api/v1/backlinks?client_id=uuid&status=active&domain_authority[gte]=50
Authorization: Bearer <token>
Required Role: ROLE_AGENCY_ADMIN, ROLE_AGENCY_STAFF, ROLE_CLIENT_ADMIN
```

### Business Intelligence Endpoints

#### List Reviews
```http
GET /api/v1/reviews?client_id=uuid&platform=google&status=approved
Authorization: Bearer <token>
Required Role: ROLE_AGENCY_ADMIN, ROLE_AGENCY_STAFF, ROLE_CLIENT_ADMIN
```

#### List Citations
```http
GET /api/v1/citations?client_id=uuid&platform=google&status=claimed
Authorization: Bearer <token>
Required Role: ROLE_AGENCY_ADMIN, ROLE_AGENCY_STAFF, ROLE_CLIENT_ADMIN
```

### Content Management Endpoints

#### List Content Items
```http
GET /api/v1/content-items?client_id=uuid&status=review&type=blog
Authorization: Bearer <token>
Required Role: ROLE_AGENCY_ADMIN, ROLE_AGENCY_STAFF, ROLE_CLIENT_ADMIN
```

#### List Content Briefs
```http
GET /api/v1/content-briefs?content_item_id=uuid&status=approved
Authorization: Bearer <token>
Required Role: ROLE_AGENCY_ADMIN, ROLE_AGENCY_STAFF, ROLE_CLIENT_ADMIN
```

### Audit & Recommendations Endpoints

#### List Audit Runs
```http
GET /api/v1/audit-runs?client_id=uuid&type=seo&status=completed
Authorization: Bearer <token>
Required Role: ROLE_AGENCY_ADMIN, ROLE_AGENCY_STAFF, ROLE_CLIENT_ADMIN
```

#### List Audit Findings
```http
GET /api/v1/audit-findings?audit_run_id=uuid&severity=high
Authorization: Bearer <token>
Required Role: ROLE_AGENCY_ADMIN, ROLE_AGENCY_STAFF, ROLE_CLIENT_ADMIN
```

#### List Recommendations
```http
GET /api/v1/recommendations?client_id=uuid&status=todo&priority=high
Authorization: Bearer <token>
Required Role: ROLE_AGENCY_ADMIN, ROLE_AGENCY_STAFF, ROLE_CLIENT_ADMIN
```

### Billing & Subscriptions Endpoints

#### List Subscriptions
```http
GET /api/v1/subscriptions?client_id=uuid&status=active
Authorization: Bearer <token>
Required Role: ROLE_AGENCY_ADMIN, ROLE_AGENCY_STAFF, ROLE_CLIENT_ADMIN
```

#### List Invoices
```http
GET /api/v1/invoices?subscription_id=uuid&status=paid
Authorization: Bearer <token>
Required Role: ROLE_AGENCY_ADMIN, ROLE_AGENCY_STAFF, ROLE_CLIENT_ADMIN
```

### Lead Management Endpoints

#### List Leads
```http
GET /api/v1/leads?client_id=uuid&status=new&page=1&per_page=25
Authorization: Bearer <token>
Required Role: ROLE_ADMIN
```

#### Create Lead
```http
POST /api/v1/leads
Content-Type: application/json
Required Role: PUBLIC_ACCESS

{
  "name": "John Smith",
  "email": "john@example.com",
  "phone": "+1-555-123-4567",
  "company": "Acme Corp",
  "message": "Interested in your SEO services"
}
```

## Request/Response Examples

### Creating a Campaign

#### Request
```http
POST /api/v1/campaigns
Authorization: Bearer <token>
Content-Type: application/json

{
  "name": "Q1 SEO Campaign",
  "description": "Focus on local SEO and content marketing",
  "type": "seo",
  "status": "draft",
  "clientId": "550e8400-e29b-41d4-a716-446655440000",
  "startDate": "2025-01-01T00:00:00Z",
  "endDate": "2025-03-31T23:59:59Z",
  "budget": 7500.00,
  "goals": ["increase_rankings", "generate_leads"],
  "metrics": ["organic_traffic", "conversion_rate"]
}
```

#### Response
```json
{
  "id": "550e8400-e29b-41d4-a716-446655440001",
  "name": "Q1 SEO Campaign",
  "description": "Focus on local SEO and content marketing",
  "type": "seo",
  "status": "draft",
  "clientId": "550e8400-e29b-41d4-a716-446655440000",
  "startDate": "2025-01-01T00:00:00Z",
  "endDate": "2025-03-31T23:59:59Z",
  "budget": 7500.00,
  "goals": ["increase_rankings", "generate_leads"],
  "metrics": ["organic_traffic", "conversion_rate"],
  "createdAt": "2025-01-15T10:30:00Z",
  "updatedAt": "2025-01-15T10:30:00Z"
}
```

### Common Response Formats

#### Success Response with Pagination
```json
{
  "data": [...],
  "pagination": {
    "page": 1,
    "per_page": 20,
    "total": 100,
    "pages": 5
  }
}
```

#### Error Response
```json
{
  "error": "Access Denied",
  "message": "You don't have permission to access this resource",
  "code": 403,
  "details": {
    "required_role": "ROLE_AGENCY_ADMIN",
    "user_role": "ROLE_CLIENT_ADMIN"
  }
}
```

#### Validation Error Response
```json
{
  "error": "Validation failed",
  "details": {
    "email": "This value should be a valid email address.",
    "password": "This value should not be blank."
  }
}
```

## Security Implementation

### JWT Configuration

```yaml
# config/packages/lexik_jwt_authentication.yaml
lexik_jwt_authentication:
    secret_key: '%kernel.project_dir%/config/jwt/private.pem'
    public_key: '%kernel.project_dir%/config/jwt/public.pem'
    pass_phrase: '%env(JWT_PASSPHRASE)%'
    token_ttl: 3600 # 1 hour
    refresh_token_ttl: 2592000 # 30 days
```

### CORS Configuration

```yaml
# config/packages/nelmio_cors.yaml
nelmio_cors:
    defaults:
        origin_regex: true
        allow_origin: ['%env(CORS_ALLOW_ORIGIN)%']
        allow_methods: ['GET', 'OPTIONS', 'POST', 'PUT', 'PATCH', 'DELETE']
        allow_headers: ['Content-Type', 'Authorization']
        expose_headers: ['Link']
        max_age: 3600
```

### Multi-Tenancy Filter

```php
// src/EventSubscriber/TenantFilterSubscriber.php
class TenantFilterSubscriber implements EventSubscriberInterface
{
    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();
        $user = $this->security->getUser();
        
        if ($user && $this->isClientUser($user)) {
            // Filter data by client_id for client users
            $this->applyClientFilter($request);
        }
    }
}
```

## API Platform Features

### Automatic Documentation

- **Interactive API Docs**: Available at `/api`
- **OpenAPI Specification**: Available at `/api/docs.json`
- **Hydra Documentation**: Available at `/api/docs.jsonld`

### Built-in Features

- **Pagination**: Automatic pagination for collection endpoints
- **Filtering**: Search, date range, and custom filters
- **Sorting**: Multi-field sorting with order control
- **Validation**: Automatic input validation and error handling
- **Serialization**: Configurable data serialization with groups

### Custom Controllers

For complex business logic, custom controllers are used alongside API Platform:

```php
#[Route('/api/v1')]
class CampaignController extends AbstractController
{
    #[Route('/campaigns/summary', name: 'api_v1_campaigns_summary', methods: ['GET'])]
    #[IsGranted('ROLE_AGENCY_ADMIN')]
    public function getCampaignSummary(Request $request): JsonResponse
    {
        // Custom business logic here
    }
}
```

## Testing the API

### Using cURL

```bash
# Get campaigns
curl -H "Authorization: Bearer <token>" \
     "http://localhost:8000/api/v1/campaigns?client_id=uuid"

# Create campaign
curl -X POST \
     -H "Authorization: Bearer <token>" \
     -H "Content-Type: application/json" \
     -d '{"name":"Test Campaign","clientId":"uuid"}' \
     "http://localhost:8000/api/v1/campaigns"
```

### Using Postman

1. **Set Base URL**: `http://localhost:8000`
2. **Add Authorization Header**: `Authorization: Bearer <token>`
3. **Use Collection**: Import the provided Postman collection
4. **Environment Variables**: Set up environment variables for tokens and IDs

### Using the Interactive API Docs

1. Navigate to `http://localhost:8000/api`
2. Click "Authorize" and enter your JWT token
3. Test endpoints directly from the browser
4. View request/response examples

## Rate Limiting & Performance

### Rate Limits

- **Authentication**: 5 requests per minute
- **API Endpoints**: 100 requests per minute per user
- **File Uploads**: 10 requests per minute per user

### Performance Tips

1. **Use Pagination**: Always paginate large collections
2. **Filter Early**: Apply filters at the database level
3. **Selective Fields**: Use `?fields=id,name,status` to limit response size
4. **Caching**: Implement appropriate caching strategies

## Troubleshooting

### Common Issues

1. **401 Unauthorized**
   - Check JWT token is valid and not expired
   - Verify token format: `Bearer <token>`
   - Regenerate token if needed

2. **403 Forbidden**
   - Verify user has required role
   - Check client scoping for client users
   - Review RBAC configuration

3. **422 Validation Error**
   - Check required fields are provided
   - Verify data types and formats
   - Review validation constraints

4. **500 Internal Server Error**
   - Check backend logs
   - Verify database connection
   - Check entity annotations

### Debug Commands

```bash
# Check API routes
php bin/console debug:router | grep api

# Validate entities
php bin/console doctrine:schema:validate

# Check cache
php bin/console cache:clear
php bin/console cache:warmup

# Debug security
php bin/console debug:security
```

### Available Roles
- `ROLE_AGENCY_ADMIN`
- `ROLE_AGENCY_STAFF`
- `ROLE_CLIENT_ADMIN`
- `ROLE_CLIENT_STAFF`
- `ROLE_SYSTEM_ADMIN`

### Available Statuses
- `invited`
- `active`
- `inactive`
- `archived`

### UUID Format
All UUIDs should be in the format: `550e8400-e29b-41d4-a716-446655440000`

### Headers
For authenticated endpoints:
```
Authorization: Bearer {JWT_TOKEN}
Content-Type: application/json
```

## Next Steps

After understanding the API:

1. **Set up authentication** in your frontend application
2. **Implement error handling** for API responses
3. **Add request/response logging** for debugging
4. **Implement retry logic** for failed requests
5. **Set up monitoring** for API performance

## Related Documentation

- [Authentication Guide](./AUTHENTICATION_GUIDE.md) - Complete authentication system
- [OAuth Setup](./OAUTH_SETUP.md) - OAuth provider configuration
- [Setup Guide](./SETUP_GUIDE.md) - Development setup guide
- [Database Guide](./DATABASE_GUIDE.md) - Database setup and entity management
- [Deployment Guide](./DEPLOYMENT_GUIDE.md) - Production deployment

---

**Last Updated:** January 2025  
**Maintained By:** Development Team  
**Status:** Complete and consolidated ✅