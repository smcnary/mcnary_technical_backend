# Complete API Documentation

## Overview

This comprehensive guide covers the complete CounselRank.legal REST API v1, including all endpoints, authentication, role-based access control, security implementation, and practical examples.

## Table of Contents

1. [Authentication](#authentication)
2. [Role-Based Access Control](#role-based-access-control)
3. [Common Query Parameters](#common-query-parameters)
4. [API Endpoints](#api-endpoints)
5. [Request/Response Examples](#requestresponse-examples)
6. [Error Handling](#error-handling)
7. [Rate Limiting](#rate-limiting)
8. [Testing](#testing)

## Authentication

### JWT Bearer Token

All API endpoints require authentication using JWT Bearer tokens (except where noted as `PUBLIC_ACCESS`).

```bash
# Include in request headers
Authorization: Bearer <your-jwt-token>
```

### Getting a Token

#### General Login
```bash
POST /api/v1/auth/login
Content-Type: application/json

{
  "email": "user@example.com",
  "password": "userpassword123"
}
```

**Response:**
```json
{
  "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9...",
  "user": {
    "id": "550e8400-e29b-41d4-a716-446655440000",
    "email": "user@example.com",
    "name": "John Doe",
    "roles": ["ROLE_CLIENT_ADMIN"],
    "client_id": "550e8400-e29b-41d4-a716-446655440001",
    "tenant_id": "550e8400-e29b-41d4-a716-446655440002",
    "status": "active",
    "created_at": "2024-01-15T10:30:00+00:00",
    "last_login_at": "2024-01-15T10:30:00+00:00"
  }
}
```

#### Client-Specific Login
```bash
POST /api/v1/clients/login
Content-Type: application/json

{
  "email": "admin@acmelaw.com",
  "password": "securepassword123"
}
```

**Response:**
```json
{
  "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9...",
  "user": {
    "id": "uuid",
    "email": "admin@acmelaw.com",
    "name": "John Doe",
    "first_name": "John",
    "last_name": "Doe",
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
Authorization: Bearer <your-jwt-token>
```

**Response:**
```json
{
  "message": "Logged out successfully"
}
```

## Role-Based Access Control

### User Roles

| Role | Description | Access Level |
|------|-------------|--------------|
| `ROLE_SYSTEM_ADMIN` | System administrator | Full access to all data and operations |
| `ROLE_AGENCY_ADMIN` | Agency administrator | Full access to all data and operations |
| `ROLE_AGENCY_STAFF` | Agency staff member | Read access to all data, limited write access |
| `ROLE_CLIENT_ADMIN` | Client administrator | Access only to their client's data |
| `ROLE_CLIENT_STAFF` | Client staff member | Limited access to their client's data |
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

## API Endpoints

### Authentication Endpoints

#### POST /api/v1/auth/login
- **Description**: Authenticate user and receive JWT token
- **Access**: Public
- **Request Body**:
  ```json
  {
    "email": "user@example.com",
    "password": "userpassword123"
  }
  ```

#### POST /api/v1/clients/login
- **Description**: Client-specific authentication with enhanced response
- **Access**: Public
- **Request Body**:
  ```json
  {
    "email": "admin@acmelaw.com",
    "password": "securepassword123"
  }
  ```

#### POST /api/v1/clients/register
- **Description**: Register new client with organization, tenant, and admin user
- **Access**: Public
- **Request Body**:
  ```json
  {
    "organization_name": "Acme Law Firm",
    "organization_domain": "acmelaw.com",
    "client_name": "Acme Law Firm",
    "client_description": "Premier legal services in downtown",
    "client_website": "https://acmelaw.com",
    "client_phone": "+1-555-0123",
    "client_address": "123 Main Street",
    "client_city": "Downtown",
    "client_state": "CA",
    "client_zip_code": "90210",
    "client_country": "USA",
    "client_industry": "law",
    "admin_email": "admin@acmelaw.com",
    "admin_password": "securepassword123",
    "admin_first_name": "John",
    "admin_last_name": "Doe"
  }
  ```

### Client Management Endpoints

#### POST /api/v1/clients
- **Description**: Create a new client
- **Access**: Agency Admin/Staff
- **Request Body**:
  ```json
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

#### GET /api/v1/clients
- **Description**: List all clients
- **Access**: Agency Admin/Staff
- **Query Parameters**: `page`, `per_page`, `sort`, `order`, `status`

#### GET /api/v1/clients/{id}
- **Description**: Get specific client
- **Access**: Agency Admin/Staff, Client Admin (own client only)

#### PATCH /api/v1/clients/{id}
- **Description**: Update an existing client
- **Access**: Agency Admin/Staff, Client Admin (own client only)
- **Request Body**:
  ```json
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

#### DELETE /api/v1/clients/{id}
- **Description**: Delete a client
- **Access**: Agency Admin only

### User Management Endpoints

#### POST /api/v1/users
- **Description**: Create a new user
- **Access**: Agency Admin/Staff, Client Admin (own client only)
- **Request Body**:
  ```json
  {
    "email": "newuser@example.com",
    "name": "Jane Smith",
    "role": "ROLE_CLIENT_STAFF",
    "client_id": "550e8400-e29b-41d4-a716-446655440001",
    "tenant_id": "550e8400-e29b-41d4-a716-446655440002",
    "status": "invited"
  }
  ```

#### GET /api/v1/users
- **Description**: List all users
- **Access**: Agency Admin/Staff, Client Admin (own client users only)

#### GET /api/v1/users/{id}
- **Description**: Get specific user
- **Access**: Agency Admin/Staff, Client Admin (own client users only), Self

#### PATCH /api/v1/users/{id}
- **Description**: Update an existing user
- **Access**: Agency Admin/Staff, Client Admin (own client users only), Self
- **Request Body**:
  ```json
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

#### DELETE /api/v1/users/{id}
- **Description**: Delete a user
- **Access**: Agency Admin only

### Audit Management Endpoints

#### POST /api/v1/audit_intakes
- **Description**: Create a new audit intake
- **Access**: Agency Admin/Staff, Client Admin (own client only)
- **Request Body**:
  ```json
  {
    "client_id": "550e8400-e29b-41d4-a716-446655440001",
    "website_url": "https://example.com",
    "business_name": "Example Business",
    "industry": "Technology",
    "target_keywords": ["SEO", "marketing", "digital"],
    "competitors": ["competitor1.com", "competitor2.com"],
    "goals": ["Increase organic traffic", "Improve rankings"],
    "budget_range": "5000-10000",
    "timeline": "3-6 months",
    "contact_email": "contact@example.com",
    "contact_phone": "+1-555-123-4567"
  }
  ```

#### GET /api/v1/audit_intakes
- **Description**: List all audit intakes
- **Access**: Agency Admin/Staff, Client Admin (own client intakes only)

#### GET /api/v1/audit_intakes/{id}
- **Description**: Get specific audit intake
- **Access**: Agency Admin/Staff, Client Admin (own client intakes only)

#### PATCH /api/v1/audit_intakes/{id}
- **Description**: Update an existing audit intake
- **Access**: Agency Admin/Staff, Client Admin (own client intakes only)

#### DELETE /api/v1/audit_intakes/{id}
- **Description**: Delete an audit intake
- **Access**: Agency Admin only

### Audit Run Endpoints

#### POST /api/v1/audit_runs
- **Description**: Create a new audit run
- **Access**: Agency Admin/Staff
- **Request Body**:
  ```json
  {
    "audit_intake_id": "550e8400-e29b-41d4-a716-446655440001",
    "client_id": "550e8400-e29b-41d4-a716-446655440002",
    "status": "pending",
    "scheduled_at": "2025-01-15T10:00:00Z",
    "parameters": {
      "crawl_depth": 3,
      "max_pages": 1000,
      "include_subdomains": true
    }
  }
  ```

#### GET /api/v1/audit_runs
- **Description**: List all audit runs
- **Access**: Agency Admin/Staff, Client Admin (own client runs only)

#### GET /api/v1/audit_runs/{id}
- **Description**: Get specific audit run
- **Access**: Agency Admin/Staff, Client Admin (own client runs only)

#### PATCH /api/v1/audit_runs/{id}
- **Description**: Update an existing audit run
- **Access**: Agency Admin/Staff

#### DELETE /api/v1/audit_runs/{id}
- **Description**: Delete an audit run
- **Access**: Agency Admin only

### OAuth Endpoints

#### GET /api/v1/auth/google
- **Description**: Initiate Google OAuth flow
- **Access**: Public

#### GET /api/v1/auth/google/callback
- **Description**: Handle Google OAuth callback
- **Access**: Public

#### GET /api/v1/auth/microsoft
- **Description**: Initiate Microsoft OAuth flow
- **Access**: Public

#### GET /api/v1/auth/microsoft/callback
- **Description**: Handle Microsoft OAuth callback
- **Access**: Public

#### POST /api/v1/oauth/google/link
- **Description**: Link Google account to current user
- **Access**: Authenticated users

#### POST /api/v1/oauth/microsoft/link
- **Description**: Link Microsoft account to current user
- **Access**: Authenticated users

## Request/Response Examples

### Complete Client Creation Example

```bash
curl -X POST "http://localhost:8000/api/v1/clients" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Tech Startup Inc",
    "slug": "tech-startup-inc",
    "description": "Innovative technology solutions for modern businesses",
    "website": "https://techstartup.com",
    "phone": "+1-555-987-6543",
    "address": "456 Innovation Drive",
    "city": "Austin",
    "state": "TX",
    "zip_code": "73301",
    "country": "USA",
    "industry": "technology",
    "metadata": {
      "founded": "2020",
      "employees": "50-100",
      "funding_round": "Series A"
    }
  }'
```

**Response:**
```json
{
  "id": "550e8400-e29b-41d4-a716-446655440003",
  "name": "Tech Startup Inc",
  "slug": "tech-startup-inc",
  "description": "Innovative technology solutions for modern businesses",
  "website": "https://techstartup.com",
  "phone": "+1-555-987-6543",
  "address": "456 Innovation Drive",
  "city": "Austin",
  "state": "TX",
  "zip_code": "73301",
  "country": "USA",
  "industry": "technology",
  "status": "active",
  "metadata": {
    "founded": "2020",
    "employees": "50-100",
    "funding_round": "Series A"
  },
  "created_at": "2025-01-15T10:30:00Z",
  "updated_at": "2025-01-15T10:30:00Z"
}
```

### Paginated List Example

```bash
curl -X GET "http://localhost:8000/api/v1/clients?page=1&per_page=10&sort=created_at&order=desc" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN"
```

**Response:**
```json
{
  "data": [
    {
      "id": "550e8400-e29b-41d4-a716-446655440003",
      "name": "Tech Startup Inc",
      "slug": "tech-startup-inc",
      "status": "active",
      "created_at": "2025-01-15T10:30:00Z"
    }
  ],
  "pagination": {
    "page": 1,
    "per_page": 10,
    "total": 1,
    "total_pages": 1
  }
}
```

## Error Handling

### HTTP Status Codes

| Code | Description |
|------|-------------|
| 200 | OK - Request successful |
| 201 | Created - Resource created successfully |
| 400 | Bad Request - Invalid request data |
| 401 | Unauthorized - Authentication required |
| 403 | Forbidden - Insufficient permissions |
| 404 | Not Found - Resource not found |
| 409 | Conflict - Resource already exists |
| 422 | Unprocessable Entity - Validation failed |
| 500 | Internal Server Error - Server error |

### Error Response Format

```json
{
  "error": "Error message",
  "details": {
    "field_name": "Specific field error"
  },
  "code": "ERROR_CODE",
  "timestamp": "2025-01-15T10:30:00Z"
}
```

### Common Error Examples

#### Validation Error (400)
```json
{
  "error": "Validation failed",
  "details": {
    "email": "This value should not be blank.",
    "password": "This value is too short. It should have 8 characters or more."
  }
}
```

#### Authentication Error (401)
```json
{
  "error": "Invalid credentials"
}
```

#### Authorization Error (403)
```json
{
  "error": "Access denied. Insufficient permissions."
}
```

#### Not Found Error (404)
```json
{
  "error": "Resource not found",
  "details": {
    "resource": "Client",
    "id": "550e8400-e29b-41d4-a716-446655440000"
  }
}
```

## Rate Limiting

### Current Implementation
- No rate limiting currently implemented
- Recommended for production: 100 requests per minute per IP
- Consider implementing for authentication endpoints

### Recommended Rate Limits
```yaml
# config/packages/rate_limiter.yaml
rate_limiter:
  auth_limiter:
    policy: 'token_bucket'
    limit: 10
    interval: '1 minute'
  
  api_limiter:
    policy: 'token_bucket'
    limit: 100
    interval: '1 minute'
```

## Testing

### Manual Testing

#### Test Authentication
```bash
# Test login
curl -X POST "http://localhost:8000/api/v1/auth/login" \
  -H "Content-Type: application/json" \
  -d '{"email": "test@example.com", "password": "password123"}'

# Test client login
curl -X POST "http://localhost:8000/api/v1/clients/login" \
  -H "Content-Type: application/json" \
  -d '{"email": "admin@acmelaw.com", "password": "securepassword123"}'
```

#### Test Client Management
```bash
# Create client
curl -X POST "http://localhost:8000/api/v1/clients" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"name": "Test Client"}'

# List clients
curl -X GET "http://localhost:8000/api/v1/clients" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN"
```

### Automated Testing

#### PHPUnit Tests
```bash
cd backend
php bin/phpunit tests/
```

#### API Testing Script
```bash
# Test all endpoints
php bin/console app:test-api-endpoints
```

## Related Documentation

- [Authentication Guide](./AUTHENTICATION_GUIDE.md) - Complete authentication system
- [OAuth Setup](./OAUTH_SETUP.md) - OAuth provider configuration
- [Setup Guide](./SETUP_GUIDE.md) - Development environment setup
- [Database Guide](./DATABASE_GUIDE.md) - Database setup and management

---

**Last Updated:** September 9, 2025  
**Maintained By:** Development Team  
**Status:** Complete and consolidated ✅
