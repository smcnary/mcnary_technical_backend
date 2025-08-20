# Client Registration Endpoint

## Overview
The client registration endpoint allows new clients to register themselves in the system, creating a complete setup including organization, tenant, client, and admin user.

## Endpoint
- **URL**: `POST /api/v1/clients/register`
- **Controller**: `App\Controller\Api\V1\ClientController::registerClient`
- **Authentication**: None required (public endpoint)

## Request Body

### Required Fields
- `organization_name`: Name of the organization
- `client_name`: Name of the client business
- `admin_email`: Email address for the admin user
- `admin_password`: Password for the admin user (minimum 8 characters)

### Optional Fields
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

## Example Request

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

## Response

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

### Error Responses

#### 400 Bad Request
```json
{
  "error": "Validation failed",
  "details": {
    "admin_password": "This value is too short. It should have 8 characters or more."
  }
}
```

#### 409 Conflict
```json
{
  "error": "Organization with this domain already exists"
}
```

#### 500 Internal Server Error
```json
{
  "error": "Internal server error: [error details]"
}
```

## What Gets Created

1. **Organization**: The parent organization for the client
2. **Tenant**: A tenant instance for multi-tenancy support
3. **Client**: The client business entity with all provided details
4. **Admin User**: A user account with ROLE_CLIENT_ADMIN privileges

## Business Logic

- The client is automatically set to 'active' status
- The admin user is automatically set to 'active' status
- The admin user is automatically associated with the client and tenant
- All entities are properly linked through relationships
- Slugs are auto-generated if not provided
- Industry defaults to 'law' if not specified

## Security Notes

- This is a public endpoint that doesn't require authentication
- Consider implementing rate limiting and CAPTCHA for production use
- Passwords are properly hashed using Symfony's password hasher
- Email addresses are validated for format
- Domain names are validated for URL format

## Usage Flow

1. Client fills out registration form
2. Frontend sends POST request to this endpoint
3. System validates all input data
4. System creates organization, tenant, client, and admin user
5. Client receives confirmation with all created entity IDs
6. Client can immediately log in using admin_email and admin_password
7. Client has full access to their dashboard and settings
