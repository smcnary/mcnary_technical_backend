# 🔌 API Reference

## 📋 Overview

This guide provides complete documentation for the CounselRank.legal REST API v1, including all endpoints, authentication, role-based access control, and security implementation.

## 🔐 Authentication

### JWT Bearer Token

All API endpoints require authentication using JWT Bearer tokens (except where noted as `PUBLIC_ACCESS`).

```bash
# Include in request headers
Authorization: Bearer <your-jwt-token>
```

### Getting a Token

```bash
# Login endpoint
POST /api/auth/login
Content-Type: application/json

{
  "username": "your_username",
  "password": "your_password"
}

# Response
{
  "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9...",
  "refresh_token": "refresh_token_here",
  "user": {
    "id": "uuid",
    "username": "your_username",
    "roles": ["ROLE_AGENCY_ADMIN"]
  }
}
```

### Token Refresh

```bash
POST /api/auth/refresh
Content-Type: application/json

{
  "refresh_token": "your_refresh_token"
}
```

## 👥 Role-Based Access Control (RBAC)

### User Roles

| Role | Description | Access Level |
|------|-------------|--------------|
| `ROLE_AGENCY_ADMIN` | Agency administrator | Full access to all data and operations |
| `ROLE_AGENCY_STAFF` | Agency staff member | Read access to all data, limited write access |
| `ROLE_CLIENT_ADMIN` | Client administrator | Access only to their client's data |
| `ROLE_CLIENT_STAFF` | Client staff member | Limited access to their client's data |
| `ROLE_USER` | Basic user | Limited access based on permissions |
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

## 📊 Common Query Parameters

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

## 🔗 API Endpoints

### Authentication Endpoints

#### Login
```http
POST /api/auth/login
Content-Type: application/json

{
  "username": "string",
  "password": "string"
}
```

#### Get Current User
```http
GET /api/v1/me
Authorization: Bearer <token>
```

#### Refresh Token
```http
POST /api/auth/refresh
Content-Type: application/json

{
  "refresh_token": "string"
}
```

### User Management

#### List Users
```http
GET /api/v1/users?page=1&per_page=25&sort=createdAt&order=desc
Authorization: Bearer <token>
Required Role: ROLE_AGENCY_ADMIN
```

#### Create User
```http
POST /api/v1/users
Authorization: Bearer <token>
Required Role: ROLE_AGENCY_ADMIN
Content-Type: application/json

{
  "username": "string",
  "email": "string",
  "displayName": "string",
  "roles": ["ROLE_CLIENT_ADMIN"],
  "clientId": "uuid"
}
```

#### Update User
```http
PATCH /api/v1/users/{id}
Authorization: Bearer <token>
Required Role: ROLE_AGENCY_ADMIN
Content-Type: application/json

{
  "displayName": "string",
  "roles": ["ROLE_CLIENT_ADMIN"]
}
```

### Client Management

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
  "name": "string",
  "email": "string",
  "phone": "string",
  "address": "string",
  "city": "string",
  "state": "string",
  "zipCode": "string",
  "website": "string"
}
```

#### Get Client Locations
```http
GET /api/v1/clients/{id}/locations
Authorization: Bearer <token>
Required Role: ROLE_AGENCY_ADMIN, ROLE_AGENCY_STAFF, ROLE_CLIENT_ADMIN (own client)
```

### Public Content

#### Get Page by Slug
```http
GET /api/v1/pages?slug=about
Authorization: Bearer <token>
Required Role: PUBLIC_ACCESS
```

#### List FAQs
```http
GET /api/v1/faqs?isActive=true&sort=sort&order=asc
Authorization: Bearer <token>
Required Role: PUBLIC_ACCESS
```

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

### Lead Management

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
  "name": "string",
  "email": "string",
  "phone": "string",
  "company": "string",
  "message": "string"
}
```

### Campaign Management

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
  "name": "string",
  "description": "string",
  "type": "seo",
  "status": "draft",
  "clientId": "uuid",
  "startDate": "2025-01-01T00:00:00Z",
  "endDate": "2025-12-31T23:59:59Z",
  "budget": 5000.00
}
```

### SEO Tools

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

### Business Intelligence

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

### Content Management

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

### Audit & Recommendations

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

### Billing & Subscriptions

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

## 🔒 Security Implementation

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

## 📝 Request/Response Examples

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

### Error Response

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

## 🚀 API Platform Features

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

## 🔍 Testing the API

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

## 📊 Rate Limiting & Performance

### Rate Limits

- **Authentication**: 5 requests per minute
- **API Endpoints**: 100 requests per minute per user
- **File Uploads**: 10 requests per minute per user

### Performance Tips

1. **Use Pagination**: Always paginate large collections
2. **Filter Early**: Apply filters at the database level
3. **Selective Fields**: Use `?fields=id,name,status` to limit response size
4. **Caching**: Implement appropriate caching strategies

## 🆘 Troubleshooting

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

## 📚 Next Steps

After understanding the API:

1. **Set up authentication** in your frontend application
2. **Implement error handling** for API responses
3. **Add request/response logging** for debugging
4. **Implement retry logic** for failed requests
5. **Set up monitoring** for API performance

For more detailed information, refer to:
- **[DATABASE_GUIDE.md](./DATABASE_GUIDE.md)** - Database setup and entity management
- **[QUICK_START.md](./QUICK_START.md)** - Development setup guide
- **[DEPLOYMENT_GUIDE.md](./DEPLOYMENT_GUIDE.md)** - Production deployment
