# API Endpoints Documentation

## Authentication Endpoints

### POST /api/v1/auth/login
**Description:** Authenticate user and receive JWT token

**Request Body:**
```json
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

### POST /api/v1/auth/refresh
**Description:** Refresh JWT token (not implemented yet)

**Request Body:**
```json
{
  "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9..."
}
```

### POST /api/v1/auth/logout
**Description:** Logout user (stateless - client removes token)

**Request Body:** None required

**Response:**
```json
{
  "message": "Logged out successfully"
}
```

## Client Management Endpoints

### POST /api/v1/clients
**Description:** Create a new client

**Request Body:**
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

**Minimal Request Body:**
```json
{
  "name": "Acme Corporation"
}
```

### PATCH /api/v1/clients/{id}
**Description:** Update an existing client

**Request Body:**
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

### POST /api/v1/clients/{id}/locations
**Description:** Create additional locations for a client (not implemented yet)

**Request Body:** Not applicable (endpoint returns 501 Not Implemented)

## FAQ Management Endpoints

### GET /api/v1/faqs
**Description:** List FAQs with filtering and pagination

**Query Parameters:**
- `page`: Page number (default: 1)
- `per_page`: Items per page (default: 20, max: 100)
- `sort`: Sort field with direction (e.g., "sort_order", "-created_at")
- `category`: Filter by category
- `search`: Search in question/answer text
- `client_id`: Filter by client ID

**Example Request:**
```
GET /api/v1/faqs?page=1&per_page=10&category=general&search=password
```

### GET /api/v1/faqs/{id}
**Description:** Get a specific FAQ by ID

**Path Parameter:**
- `id`: FAQ ID

## Media Assets Endpoints

### GET /api/v1/media-assets
**Description:** List media assets with filtering and pagination

**Query Parameters:**
- `page`: Page number (default: 1)
- `per_page`: Items per page (default: 20, max: 100)
- `sort`: Sort field with direction (e.g., "created_at", "-file_size")
- `type`: Filter by media type (e.g., "image", "video", "document")
- `status`: Filter by status (default: "active")
- `client_id`: Filter by client ID

**Example Request:**
```
GET /api/v1/media-assets?page=1&per_page=20&type=image&status=active
```

### GET /api/v1/media-assets/{id}
**Description:** Get a specific media asset by ID

**Path Parameter:**
- `id`: Media asset UUID

## Package Management Endpoints

### GET /api/v1/packages
**Description:** List packages with filtering and pagination

**Query Parameters:**
- `page`: Page number (default: 1)
- `per_page`: Items per page (default: 20, max: 100)
- `sort`: Sort field with direction (e.g., "sort_order", "-price")
- `client_id`: Filter by client ID
- `popular`: Filter by popularity (true/false)
- `billing_cycle`: Filter by billing cycle (e.g., "monthly", "yearly")

**Example Request:**
```
GET /api/v1/packages?page=1&per_page=10&popular=true&billing_cycle=monthly
```

### GET /api/v1/packages/{id}
**Description:** Get a specific package by ID

**Path Parameter:**
- `id`: Package ID

## Page Management Endpoints

### GET /api/v1/pages
**Description:** List pages with filtering and pagination

**Query Parameters:**
- `page`: Page number (default: 1)
- `per_page`: Items per page (default: 20, max: 100)
- `sort`: Sort field with direction (e.g., "sort_order", "-published_at")
- `type`: Filter by page type (e.g., "blog", "service", "about")
- `status`: Filter by status (default: "published")
- `slug`: Filter by specific slug

**Example Request:**
```
GET /api/v1/pages?page=1&per_page=15&type=blog&status=published
```

### GET /api/v1/pages/{id}
**Description:** Get a specific page by ID

**Path Parameter:**
- `id`: Page ID

## User Management Endpoints

### GET /api/v1/me
**Description:** Get current authenticated user information

**Headers Required:**
```
Authorization: Bearer {JWT_TOKEN}
```

### GET /api/v1/users
**Description:** List users with filtering and pagination (Admin only)

**Query Parameters:**
- `page`: Page number (default: 1)
- `per_page`: Items per page (default: 20, max: 100)
- `sort`: Sort field with direction (e.g., "created_at", "-last_login_at")
- `search`: Search in name/email
- `role`: Filter by role
- `status`: Filter by status

**Example Request:**
```
GET /api/v1/users?page=1&per_page=20&role=ROLE_CLIENT_ADMIN&status=active
```

### POST /api/v1/users
**Description:** Create a new user (Admin only)

**Request Body:**
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

**Minimal Request Body:**
```json
{
  "email": "newuser@example.com",
  "name": "Jane Smith",
  "role": "ROLE_CLIENT_STAFF"
}
```

### PATCH /api/v1/users/{id}
**Description:** Update an existing user (Admin only)

**Request Body:**
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

## Common Response Formats

### Success Response
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

### Error Response
```json
{
  "error": "Error message",
  "details": {
    "field_name": "Specific field error message"
  }
}
```

### Validation Error Response
```json
{
  "error": "Validation failed",
  "details": {
    "email": "This value should be a valid email address.",
    "password": "This value should not be blank."
  }
}
```

## Authentication

All protected endpoints require a valid JWT token in the Authorization header:

```
Authorization: Bearer {JWT_TOKEN}
```

## Rate Limiting

API endpoints are subject to rate limiting. Please implement appropriate throttling in your client applications.

## Pagination

All list endpoints support pagination with the following parameters:
- `page`: Page number (starts from 1)
- `per_page`: Items per page (maximum 100)

## Sorting

Sorting is supported on most list endpoints using the `sort` parameter:
- `field_name`: Ascending order
- `-field_name`: Descending order
- Multiple fields: `sort=name,-created_at`

## Filtering

Most list endpoints support filtering using query parameters. Check individual endpoint documentation for available filters.
