# User Profile API

## Overview

The User Profile API provides dynamic user information for the frontend UserGreeting component, including user details, agency information, and personalized greeting data.

## Endpoint

### GET `/api/v1/user-profile/greeting`

Retrieves the current authenticated user's profile information and greeting data.

**Authentication Required:** Yes (JWT token or session-based)

**Method:** GET

**Headers:**
```
Content-Type: application/json
Authorization: Bearer {token} (if using JWT)
```

## Response Format

### Success Response (200 OK)

```json
{
  "user": {
    "id": "uuid-string",
    "email": "user@example.com",
    "firstName": "John",
    "lastName": "Doe",
    "name": "John Doe",
    "role": "ROLE_CLIENT_USER",
    "status": "active",
    "lastLoginAt": "2025-01-20T10:30:00+00:00"
  },
  "agency": {
    "id": "uuid-string",
    "name": "McNary Legal Services",
    "domain": "mcnarylegal.com",
    "description": "Professional legal services"
  },
  "client": {
    "id": "uuid-string",
    "name": "Client Company",
    "slug": "client-company",
    "description": "Client description",
    "status": "active"
  },
  "greeting": {
    "displayName": "John Doe",
    "organizationName": "McNary Legal Services",
    "userRole": "Client User",
    "timeBasedGreeting": "Good morning"
  }
}
```

### Error Responses

#### 401 Unauthorized
```json
{
  "error": "Access denied",
  "status_code": 401
}
```

#### 404 Not Found
```json
{
  "error": "User has no associated agency",
  "status_code": 404
}
```

#### 500 Internal Server Error
```json
{
  "error": "Internal server error",
  "status_code": 500
}
```

## Data Structure

### User Object
- **id**: Unique user identifier (UUID)
- **email**: User's email address
- **firstName**: User's first name (nullable)
- **lastName**: User's last name (nullable)
- **name**: Computed full name or fallback
- **role**: User's role constant
- **status**: User account status
- **lastLoginAt**: Last login timestamp (ISO 8601)

### Agency Object
- **id**: Agency identifier (UUID)
- **name**: Agency name
- **domain**: Agency domain (nullable)
- **description**: Agency description (nullable)

### Client Object (nullable)
- **id**: Client identifier (UUID)
- **name**: Client company name
- **slug**: URL-friendly client identifier
- **description**: Client description (nullable)
- **status**: Client status

### Greeting Object
- **displayName**: User-friendly display name
- **organizationName**: Agency name for display
- **userRole**: Human-readable role description
- **timeBasedGreeting**: Time-appropriate greeting

## Business Logic

### Display Name Generation
1. Uses `firstName + lastName` if both are available
2. Falls back to `firstName` or `lastName` if only one is available
3. Final fallback to email prefix (e.g., "john" from "john@example.com")

### Role Display Mapping
- `ROLE_SYSTEM_ADMIN` → "System Administrator"
- `ROLE_AGENCY_ADMIN` → "Agency Administrator"
- `ROLE_CLIENT_USER` → "Client User"
- `ROLE_READ_ONLY` → "Read Only User"

### Time-Based Greeting
- 5:00 AM - 11:59 AM: "Good morning"
- 12:00 PM - 4:59 PM: "Good afternoon"
- 5:00 PM - 8:59 PM: "Good evening"
- 9:00 PM - 4:59 AM: "Good night"

## Security

- **Authentication Required**: Users must be fully authenticated
- **Authorization**: Users can only access their own profile data
- **Data Isolation**: Agency and client data is filtered by user permissions

## Usage Examples

### Frontend Integration

```typescript
import { fetchUserGreeting } from '../services/userProfile';

// Fetch greeting data
const greetingData = await fetchUserGreeting();

// Use in component
<UserGreeting 
  fallbackData={{
    userName: "User",
    organizationName: "Organization", 
    userRole: "User"
  }}
/>
```

### Error Handling

```typescript
try {
  const profile = await fetchUserProfile();
  // Use profile data
} catch (error) {
  // Handle error - use fallback data
  console.error('Failed to load profile:', error);
}
```

## Caching

The frontend service includes a 5-minute cache to reduce API calls:
- Cache is automatically invalidated after 5 minutes
- Expired cache data is used as fallback if API fails
- Cache can be manually cleared with `clearUserProfileCache()`

## Dependencies

- **Backend**: Symfony 6+, Doctrine ORM, JWT Authentication
- **Frontend**: React 18+, TypeScript
- **Database**: PostgreSQL with JSONB support

## Testing

Use the `UserProfileTest` component to test the API integration:

```tsx
import UserProfileTest from '../components/portal/UserProfileTest';

// In your test page
<UserProfileTest />
```

This component will display all the fetched data and allow you to refresh the profile information.
