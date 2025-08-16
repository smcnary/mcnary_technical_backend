# API JSON Examples - Quick Reference

## Authentication

### POST /api/v1/auth/login
```json
{
  "email": "user@example.com",
  "password": "userpassword123"
}
```

### POST /api/v1/auth/refresh
```json
{
  "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9..."
}
```

## Client Management

### POST /api/v1/clients
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

**Minimal:**
```json
{
  "name": "Acme Corporation"
}
```

### PATCH /api/v1/clients/{id}
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

## User Management

### POST /api/v1/users
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

**Minimal:**
```json
{
  "email": "newuser@example.com",
  "name": "Jane Smith",
  "role": "ROLE_CLIENT_STAFF"
}
```

### PATCH /api/v1/users/{id}
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

## Available Roles
- `ROLE_AGENCY_ADMIN`
- `ROLE_AGENCY_STAFF`
- `ROLE_CLIENT_ADMIN`
- `ROLE_CLIENT_STAFF`

## Available Statuses
- `invited`
- `active`
- `inactive`
- `archived`

## UUID Format
All UUIDs should be in the format: `550e8400-e29b-41d4-a716-446655440000`

## Headers
For authenticated endpoints:
```
Authorization: Bearer {JWT_TOKEN}
Content-Type: application/json
```
