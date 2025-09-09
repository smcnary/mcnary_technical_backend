# Client Login Endpoint

## Overview
The client login endpoint allows client users (ROLE_CLIENT_ADMIN, ROLE_CLIENT_STAFF) to authenticate and access their client dashboard. This endpoint is specifically designed for client users and provides enhanced client-specific information in the response.

## Endpoint
- **URL**: `POST /api/v1/clients/login`
- **Controller**: `App\Controller\Api\V1\ClientController::clientLogin`
- **Authentication**: None required (public endpoint)

## Request Body

### Required Fields
- `email`: User's email address
- `password`: User's password

### Example Request
```json
{
  "email": "admin@acmelaw.com",
  "password": "securepassword123"
}
```

## Response

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

### Error Responses

#### 400 Bad Request
```json
{
  "error": "Validation failed",
  "details": {
    "email": "This value should not be blank."
  }
}
```

#### 401 Unauthorized
```json
{
  "error": "Invalid credentials"
}
```

#### 403 Forbidden
```json
{
  "error": "Access denied. This endpoint is for client users only."
}
```

```json
{
  "error": "Account is not active"
}
```

#### 500 Internal Server Error
```json
{
  "error": "Internal server error: [error details]"
}
```

## Security Features

### Client-Only Access
- Only users with `ROLE_CLIENT_ADMIN` or `ROLE_CLIENT_STAFF` can use this endpoint
- Agency users (ROLE_AGENCY_ADMIN, ROLE_AGENCY_STAFF) are blocked with a 403 Forbidden response
- This ensures client users can only access client-specific functionality

### Account Status Validation
- Users must have status `invited` or `active` to log in
- Inactive, suspended, or archived accounts are blocked

### Password Security
- Passwords are validated using Symfony's password hasher
- Secure credential comparison prevents timing attacks

## Business Logic

### User Authentication
1. Validates email format and password presence
2. Finds user by email address
3. Verifies password hash
4. Checks user role (must be client user)
5. Validates account status

### Client Information
- Automatically fetches associated client details
- Provides comprehensive client profile information
- Includes business details like address, phone, website, industry

### Session Management
- Generates JWT token for authenticated sessions
- Updates last login timestamp
- Returns token for subsequent authenticated requests

## Usage Flow

1. **Client Registration**: User registers through `/api/v1/clients/register`
2. **Client Login**: User logs in through `/api/v1/clients/login`
3. **Token Usage**: Frontend stores JWT token for authenticated requests
4. **Dashboard Access**: User can access client dashboard and features

## Frontend Integration

### Login Form
```javascript
const loginData = {
  email: 'admin@acmelaw.com',
  password: 'securepassword123'
};

const response = await fetch('/api/v1/clients/login', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json'
  },
  body: JSON.stringify(loginData)
});

const result = await response.json();

if (response.ok) {
  // Store token
  localStorage.setItem('authToken', result.token);
  
  // Store user and client info
  localStorage.setItem('userData', JSON.stringify(result.user));
  localStorage.setItem('clientData', JSON.stringify(result.client));
  
  // Redirect to dashboard
  window.location.href = '/dashboard';
} else {
  // Handle error
  console.error(result.error);
}
```

### Authenticated Requests
```javascript
const token = localStorage.getItem('authToken');

const response = await fetch('/api/v1/some-protected-endpoint', {
  headers: {
    'Authorization': `Bearer ${token}`,
    'Content-Type': 'application/json'
  }
});
```

## Differences from General Auth Login

| Feature | General Login (`/api/v1/auth/login`) | Client Login (`/api/v1/clients/login`) |
|---------|--------------------------------------|----------------------------------------|
| **User Types** | All users (agency + client) | Client users only |
| **Response Data** | Basic user info | Enhanced client + user info |
| **Client Details** | Not included | Full client profile |
| **Access Control** | Role-based after login | Role-based during login |
| **Use Case** | General authentication | Client-specific authentication |

## Error Handling

### Validation Errors
- Email format validation
- Required field validation
- Clear error messages for each field

### Authentication Errors
- Generic "Invalid credentials" message for security
- No distinction between invalid email vs invalid password

### Authorization Errors
- Clear messaging about client-only access
- Account status explanations

## Rate Limiting Considerations

For production use, consider implementing:
- Rate limiting per IP address
- CAPTCHA for multiple failed attempts
- Account lockout after repeated failures
- Monitoring for suspicious login patterns
