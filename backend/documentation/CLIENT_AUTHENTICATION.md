# Client Authentication System

## Overview
This document describes the complete client authentication system, including registration and login endpoints specifically designed for client users.

## Endpoints

### 1. Client Registration
- **URL**: `POST /api/v1/clients/register`
- **Purpose**: Allow new clients to register and create their account
- **Authentication**: None required (public endpoint)

### 2. Client Login
- **URL**: `POST /api/v1/clients/login`
- **Purpose**: Authenticate client users and provide access to their dashboard
- **Authentication**: None required (public endpoint)

## Complete Authentication Flow

### Step 1: Client Registration
```bash
curl -X POST http://localhost:8000/api/v1/clients/register \
  -H "Content-Type: application/json" \
  -d '{
    "organization_name": "Acme Law Firm",
    "client_name": "Acme Law Firm",
    "admin_email": "admin@acmelaw.com",
    "admin_password": "securepass123",
    "admin_first_name": "John",
    "admin_last_name": "Doe"
  }'
```

**Response**: Creates organization, tenant, client, and admin user

### Step 2: Client Login
```bash
curl -X POST http://localhost:8000/api/v1/clients/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "admin@acmelaw.com",
    "password": "securepass123"
  }'
```

**Response**: Returns JWT token and user/client information

### Step 3: Authenticated Requests
```bash
curl -H "Authorization: Bearer JWT_TOKEN_HERE" \
  http://localhost:8000/api/v1/protected-endpoint
```

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

## User Roles and Access

### Client User Roles
- **ROLE_CLIENT_ADMIN**: Full access to client dashboard and settings
- **ROLE_CLIENT_STAFF**: Limited access based on permissions

### Agency User Roles
- **ROLE_AGENCY_ADMIN**: Can create and manage clients
- **ROLE_AGENCY_STAFF**: Can view and assist clients

## Data Structure

### What Gets Created During Registration
1. **Organization**: Parent entity for the client
2. **Tenant**: Multi-tenancy instance
3. **Client**: Business entity with all details
4. **Admin User**: User account with client admin privileges

### Login Response Data
- JWT authentication token
- Complete user profile information
- Full client business details
- Organization and tenant information

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

## Production Considerations

### Security Enhancements
- Implement rate limiting
- Add CAPTCHA for registration
- Enable account lockout after failed attempts
- Monitor for suspicious activity patterns

### Performance Optimization
- Cache client data where appropriate
- Optimize database queries
- Implement connection pooling

### Monitoring and Logging
- Track registration and login attempts
- Monitor authentication failures
- Log security events
- Set up alerts for unusual patterns

## Testing

### Test Scripts
- `test-client-registration.php`: Tests registration endpoint
- `test-client-login.php`: Tests login endpoint

### Manual Testing
1. Test registration with valid data
2. Test registration with invalid data
3. Test login with valid credentials
4. Test login with invalid credentials
5. Test access control for different user types

## Troubleshooting

### Common Issues
- **Cache Issues**: Clear Symfony cache after changes
- **Database Issues**: Check entity relationships and migrations
- **JWT Issues**: Verify JWT bundle configuration
- **Validation Issues**: Check constraint definitions

### Debug Mode
Enable debug mode in development to see detailed error messages:
```yaml
# config/packages/dev/framework.yaml
debug: true
```

## API Reference

### Registration Endpoint
See [CLIENT_REGISTRATION.md](./CLIENT_REGISTRATION.md) for detailed API documentation.

### Login Endpoint
See [CLIENT_LOGIN.md](./CLIENT_LOGIN.md) for detailed API documentation.

## Support

For technical support or questions about the client authentication system, please refer to the development team or create an issue in the project repository.
