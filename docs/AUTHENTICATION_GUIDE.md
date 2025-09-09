# Authentication System Guide

## Overview

This comprehensive guide covers the complete authentication system for CounselRank.legal, including client registration, login, OAuth integration, and security features.

## Table of Contents

1. [System Architecture](#system-architecture)
2. [Client Registration](#client-registration)
3. [Client Login](#client-login)
4. [User Roles & Access Control](#user-roles--access-control)
5. [OAuth Integration](#oauth-integration)
6. [Security Features](#security-features)
7. [Frontend Integration](#frontend-integration)
8. [Error Handling](#error-handling)
9. [Production Considerations](#production-considerations)
10. [Troubleshooting](#troubleshooting)

## System Architecture

### Multi-Tenant Design
- **Organization**: Parent entity for the client
- **Tenant**: Multi-tenancy instance for data isolation
- **Client**: Business entity with all details
- **User**: Individual user accounts with role-based access

### Authentication Flow
```
Registration → Organization + Tenant + Client + Admin User → Login → JWT Token → Authenticated Requests
```

## Client Registration

### Endpoint
- **URL**: `POST /api/v1/clients/register`
- **Controller**: `App\Controller\Api\V1\ClientController::registerClient`
- **Authentication**: None required (public endpoint)

### Request Body

#### Required Fields
- `organization_name`: Name of the organization
- `client_name`: Name of the client business
- `admin_email`: Email address for the admin user
- `admin_password`: Password for the admin user (minimum 8 characters)

#### Optional Fields
- `organization_domain`: Website domain for the organization
- `client_slug`: Custom slug for the client (auto-generated if not provided)
- `client_description`: Description of the client business
- `client_website`: Client's website URL
- `client_phone`: Client's phone number
- `client_address`: Client's street address
- `client_city`: Client's city
- `client_state`: Client's state
- `client_zip_code`: Client's postal code
- `client_country`: Client's country
- `client_industry`: Industry type (law, healthcare, real_estate, finance, other)
- `admin_first_name`: Admin user's first name
- `admin_last_name`: Admin user's last name
- `tenant_name`: Custom tenant name (defaults to organization name)
- `tenant_slug`: Custom tenant slug (auto-generated if not provided)

### Example Request
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

### Success Response (201 Created)
```json
{
  "message": "Client registration successful",
  "organization": {
    "id": "uuid",
    "name": "Acme Law Firm",
    "domain": "acmelaw.com"
  },
  "tenant": {
    "id": "uuid",
    "name": "Acme Law Firm",
    "slug": "acme-law-firm"
  },
  "client": {
    "id": "uuid",
    "name": "Acme Law Firm",
    "slug": "acme-law-firm",
    "status": "active"
  },
  "admin_user": {
    "id": "uuid",
    "email": "admin@acmelaw.com",
    "role": "ROLE_CLIENT_ADMIN",
    "status": "active"
  }
}
```

### What Gets Created
1. **Organization**: The parent organization for the client
2. **Tenant**: A tenant instance for multi-tenancy support
3. **Client**: The client business entity with all provided details
4. **Admin User**: A user account with ROLE_CLIENT_ADMIN privileges

## Client Login

### Endpoint
- **URL**: `POST /api/v1/clients/login`
- **Controller**: `App\Controller\Api\V1\ClientController::clientLogin`
- **Authentication**: None required (public endpoint)

### Request Body
```json
{
  "email": "admin@acmelaw.com",
  "password": "securepassword123"
}
```

### Success Response (200 OK)
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

### Client-Only Access
- Only users with `ROLE_CLIENT_ADMIN` or `ROLE_CLIENT_STAFF` can use this endpoint
- Agency users (ROLE_AGENCY_ADMIN, ROLE_AGENCY_STAFF) are blocked with a 403 Forbidden response
- This ensures client users can only access client-specific functionality

## User Roles & Access Control

### Client User Roles
- **ROLE_CLIENT_ADMIN**: Full access to client dashboard and settings
- **ROLE_CLIENT_STAFF**: Limited access based on permissions

### Agency User Roles
- **ROLE_AGENCY_ADMIN**: Can create and manage clients
- **ROLE_AGENCY_STAFF**: Can view and assist clients

### System User Roles
- **ROLE_SYSTEM_ADMIN**: Full system access

### Access Control Matrix

| Role | Client Dashboard | Agency Dashboard | System Admin | Client Management |
|------|------------------|------------------|--------------|-------------------|
| ROLE_CLIENT_ADMIN | ✅ Full | ❌ | ❌ | ❌ |
| ROLE_CLIENT_STAFF | ✅ Limited | ❌ | ❌ | ❌ |
| ROLE_AGENCY_ADMIN | ❌ | ✅ Full | ❌ | ✅ Full |
| ROLE_AGENCY_STAFF | ❌ | ✅ Limited | ❌ | ✅ View |
| ROLE_SYSTEM_ADMIN | ✅ Full | ✅ Full | ✅ Full | ✅ Full |

## OAuth Integration

### Supported Providers
- **Google OAuth**: Google Business Profile, Search Console, Analytics
- **Microsoft OAuth**: Microsoft 365, Azure AD

### OAuth Flow
1. User initiates OAuth connection
2. Redirect to provider authorization
3. Provider returns authorization code
4. Exchange code for access/refresh tokens
5. Store tokens securely
6. Use tokens for API access

### OAuth Endpoints
- `POST /api/v1/oauth/google/connect` - Connect Google account
- `POST /api/v1/oauth/microsoft/connect` - Connect Microsoft account
- `GET /api/v1/oauth/{provider}/callback` - OAuth callback handler

## Security Features

### Registration Security
- Input validation for all fields
- Password strength requirements (minimum 8 characters)
- Email format validation
- URL format validation for websites
- Duplicate checking for domains, slugs, and emails
- Automatic password hashing

### Login Security
- Client-only access (blocks agency users)
- Account status validation
- Secure password verification
- JWT token generation
- Last login tracking

### Multi-Tenancy
- Each client gets their own tenant
- Organization-level isolation
- Client-specific data access

### JWT Security
- RSA256 signed tokens
- Configurable expiration times
- Secure token storage recommendations

## Frontend Integration

### Registration Form
```javascript
const registerClient = async (formData) => {
  try {
    const response = await fetch('/api/v1/clients/register', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(formData)
    });
    
    if (response.ok) {
      const result = await response.json();
      // Show success message
      // Redirect to login or auto-login
    } else {
      const error = await response.json();
      // Handle validation errors
    }
  } catch (error) {
    // Handle network errors
  }
};
```

### Login Form
```javascript
const loginClient = async (credentials) => {
  try {
    const response = await fetch('/api/v1/clients/login', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(credentials)
    });
    
    if (response.ok) {
      const result = await response.json();
      
      // Store authentication data
      localStorage.setItem('authToken', result.token);
      localStorage.setItem('userData', JSON.stringify(result.user));
      localStorage.setItem('clientData', JSON.stringify(result.client));
      
      // Redirect to dashboard
      window.location.href = '/dashboard';
    } else {
      const error = await response.json();
      // Handle login errors
    }
  } catch (error) {
    // Handle network errors
  }
};
```

### Authenticated Requests
```javascript
const makeAuthenticatedRequest = async (url, options = {}) => {
  const token = localStorage.getItem('authToken');
  
  const response = await fetch(url, {
    ...options,
    headers: {
      ...options.headers,
      'Authorization': `Bearer ${token}`,
      'Content-Type': 'application/json'
    }
  });
  
  return response;
};
```

## Error Handling

### Common Error Scenarios
- **Validation Errors**: Field-specific error messages
- **Authentication Errors**: Generic "Invalid credentials" for security
- **Authorization Errors**: Clear access control messaging
- **Business Logic Errors**: Duplicate entity conflicts

### Error Response Format
```json
{
  "error": "Error message",
  "details": {
    "field_name": "Specific field error"
  }
}
```

### HTTP Status Codes
- **200 OK**: Successful login
- **201 Created**: Successful registration
- **400 Bad Request**: Validation errors
- **401 Unauthorized**: Invalid credentials
- **403 Forbidden**: Access denied
- **409 Conflict**: Duplicate entity
- **500 Internal Server Error**: Server error

## Production Considerations

### Security Enhancements
- Implement rate limiting
- Add CAPTCHA for registration
- Enable account lockout after failed attempts
- Monitor for suspicious activity patterns
- Use HTTPS for all authentication endpoints

### Performance Optimization
- Cache client data where appropriate
- Optimize database queries
- Implement connection pooling
- Use Redis for session storage

### Monitoring and Logging
- Track registration and login attempts
- Monitor authentication failures
- Log security events
- Set up alerts for unusual patterns
- Monitor JWT token usage

## Troubleshooting

### Common Issues
- **Cache Issues**: Clear Symfony cache after changes
- **Database Issues**: Check entity relationships and migrations
- **JWT Issues**: Verify JWT bundle configuration
- **Validation Issues**: Check constraint definitions
- **OAuth Issues**: Verify provider configuration

### Debug Mode
Enable debug mode in development to see detailed error messages:
```yaml
# config/packages/dev/framework.yaml
debug: true
```

### Testing
- Test registration with valid data
- Test registration with invalid data
- Test login with valid credentials
- Test login with invalid credentials
- Test access control for different user types
- Test OAuth flows

## Related Documentation

- [OAuth Setup Guide](./OAUTH_SETUP.md) - Detailed OAuth provider configuration
- [API Documentation](./API_DOCUMENTATION.md) - Complete API reference
- [Database Guide](./DATABASE_GUIDE.md) - Database setup and management
- [Deployment Guide](./DEPLOYMENT_GUIDE.md) - Production deployment

---

**Last Updated:** September 9, 2025  
**Maintained By:** Development Team  
**Status:** Complete and consolidated ✅
