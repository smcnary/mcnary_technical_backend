# Frontend Client Authentication Integration

## Overview
This guide explains how to integrate the client authentication components with your React frontend. The components have been updated to work with the backend API endpoints for client registration and login.

## Updated Components

### 1. ClientLoginModal.tsx
- **Purpose**: Modal for client user authentication
- **API Endpoint**: `POST /api/v1/clients/login`
- **Features**:
  - Email and password validation
  - JWT token storage
  - User and client data storage
  - Error handling with specific messages

### 2. ClientRegisterModal.tsx
- **Purpose**: Complete client registration form
- **API Endpoint**: `POST /api/v1/clients/register`
- **Features**:
  - Multi-section form (Organization, Client, Admin User)
  - Comprehensive validation
  - Password strength meter
  - Success/error handling

### 3. API Utility (lib/api.ts)
- **Purpose**: Centralized API functions and type definitions
- **Features**:
  - Type-safe API calls
  - Authentication token management
  - Error handling
  - Data storage utilities

## Usage Examples

### Basic Login Modal Usage
```tsx
import { useState } from "react";
import ClientLoginModal from "./components/ClientLoginModal";
import { type LoginResponse } from "./lib/api";

function App() {
  const [showLogin, setShowLogin] = useState(false);

  const handleLoginSuccess = (response: LoginResponse) => {
    console.log("User logged in:", response.user);
    console.log("Client data:", response.client);
    // Redirect to dashboard
    window.location.href = "/dashboard";
  };

  return (
    <div>
      <button onClick={() => setShowLogin(true)}>
        Login
      </button>
      
      <ClientLoginModal
        open={showLogin}
        onClose={() => setShowLogin(false)}
        onSuccess={handleLoginSuccess}
      />
    </div>
  );
}
```

### Basic Registration Usage
```tsx
import ClientRegisterModal from "./components/ClientRegisterModal";

function RegisterPage() {
  return (
    <div>
      <ClientRegisterModal />
    </div>
  );
}
```

### Complete Authentication Flow
```tsx
import { useState, useEffect } from "react";
import ClientLoginModal from "./components/ClientLoginModal";
import ClientRegisterModal from "./components/ClientRegisterModal";
import { getAuthToken, clearAuthData } from "./lib/api";

function AuthenticatedApp() {
  const [isAuthenticated, setIsAuthenticated] = useState(false);
  const [showLogin, setShowLogin] = useState(false);
  const [showRegister, setShowRegister] = useState(false);

  useEffect(() => {
    // Check if user is already authenticated
    const token = getAuthToken();
    setIsAuthenticated(!!token);
  }, []);

  const handleLoginSuccess = () => {
    setIsAuthenticated(true);
    setShowLogin(false);
  };

  const handleLogout = () => {
    clearAuthData();
    setIsAuthenticated(false);
  };

  if (!isAuthenticated) {
    return (
      <div>
        <h1>Welcome to Client Portal</h1>
        <button onClick={() => setShowLogin(true)}>Login</button>
        <button onClick={() => setShowRegister(true)}>Register</button>
        
        <ClientLoginModal
          open={showLogin}
          onClose={() => setShowLogin(false)}
          onSuccess={handleLoginSuccess}
        />
        
        {showRegister && <ClientRegisterModal />}
      </div>
    );
  }

  return (
    <div>
      <h1>Client Dashboard</h1>
      <button onClick={handleLogout}>Logout</button>
      {/* Dashboard content */}
    </div>
  );
}
```

## API Integration Details

### Authentication Flow
1. **Registration**: User fills out registration form → Creates organization, tenant, client, and admin user
2. **Login**: User authenticates → Receives JWT token and user/client data
3. **Storage**: Token and data stored in localStorage for session persistence
4. **Authenticated Requests**: Include JWT token in Authorization header

### Data Storage
The components automatically store the following in localStorage:

```typescript
// After successful login:
localStorage.setItem("authToken", response.token);
localStorage.setItem("userData", JSON.stringify(response.user));
localStorage.setItem("clientData", JSON.stringify(response.client));
```

### Making Authenticated Requests
Use the `authenticatedFetch` utility for API calls that require authentication:

```typescript
import { authenticatedFetch } from "./lib/api";

const fetchUserData = async () => {
  const response = await authenticatedFetch("/api/v1/user/profile");
  const data = await response.json();
  return data;
};
```

## Type Definitions

### LoginResponse
```typescript
interface LoginResponse {
  token: string;
  user: {
    id: string;
    email: string;
    name?: string;
    first_name?: string;
    last_name?: string;
    role: string;
    status: string;
    client_id?: string;
    tenant_id?: string;
    organization_id: string;
    created_at: string;
    last_login_at?: string;
  };
  client?: {
    id: string;
    name: string;
    slug: string;
    description?: string;
    website?: string;
    phone?: string;
    address?: string;
    city?: string;
    state?: string;
    zip_code?: string;
    country?: string;
    industry: string;
    status: string;
  };
}
```

### RegistrationResponse
```typescript
interface RegistrationResponse {
  message: string;
  organization: {
    id: string;
    name: string;
    domain?: string;
  };
  tenant: {
    id: string;
    name: string;
    slug: string;
  };
  client: {
    id: string;
    name: string;
    slug: string;
    status: string;
  };
  admin_user: {
    id: string;
    email: string;
    role: string;
    status: string;
  };
}
```

## Error Handling

### Login Errors
- **401**: Invalid credentials
- **403**: Account access denied (wrong user type or inactive account)
- **400**: Validation errors
- **500**: Server errors

### Registration Errors
- **409**: Duplicate organization/client/user
- **400**: Validation errors
- **500**: Server errors

### Frontend Error Display
Both components handle errors gracefully:
- Form-level errors display at the top
- Field-level errors display below each input
- Network errors show generic retry message

## Styling and Customization

### CSS Classes Used
The components use Tailwind CSS classes. Key classes include:
- `input-field`: Custom input styling
- `btn-primary`: Primary button styling
- Form validation states use color-coded borders

### Customization Options
1. **Styling**: Modify CSS classes to match your design system
2. **Validation**: Update validation rules in the `validate()` functions
3. **Redirects**: Customize post-login/registration redirects
4. **Success Messages**: Modify success message text and behavior

## Security Considerations

### Token Storage
- Tokens are stored in localStorage (consider httpOnly cookies for production)
- Tokens are included in Authorization header for authenticated requests
- Clear tokens on logout

### Validation
- Client-side validation prevents malformed requests
- Server-side validation provides final security layer
- Passwords are never stored in plain text

### HTTPS
Ensure all API calls use HTTPS in production to protect sensitive data.

## Deployment Considerations

### Environment Configuration
```typescript
// Consider using environment variables for API URLs
const API_BASE_URL = process.env.NEXT_PUBLIC_API_URL || 'http://localhost:8000';
```

### Error Monitoring
Consider integrating error monitoring (Sentry, LogRocket) to track authentication issues.

### Analytics
Track authentication events for user engagement insights:
```typescript
// Example: Track successful registration
const handleLoginSuccess = (response: LoginResponse) => {
  // Analytics tracking
  analytics.track('User Login', {
    userId: response.user.id,
    userRole: response.user.role,
    clientId: response.client?.id
  });
  
  // Redirect to dashboard
  window.location.href = "/dashboard";
};
```

## Testing

### Unit Tests
Test individual components with mock API responses:

```typescript
// Example test
import { render, screen, fireEvent, waitFor } from '@testing-library/react';
import ClientLoginModal from './ClientLoginModal';

test('handles successful login', async () => {
  const mockOnSuccess = jest.fn();
  
  render(
    <ClientLoginModal
      open={true}
      onClose={() => {}}
      onSuccess={mockOnSuccess}
    />
  );
  
  // Fill form and submit
  fireEvent.change(screen.getByLabelText(/email/i), {
    target: { value: 'test@example.com' }
  });
  fireEvent.change(screen.getByLabelText(/password/i), {
    target: { value: 'password123' }
  });
  fireEvent.click(screen.getByRole('button', { name: /log in/i }));
  
  await waitFor(() => {
    expect(mockOnSuccess).toHaveBeenCalled();
  });
});
```

### Integration Tests
Test the complete authentication flow with a test backend.

## Troubleshooting

### Common Issues

1. **CORS Errors**: Ensure backend CORS is configured for your frontend domain
2. **Token Expiry**: Handle JWT token expiration and refresh
3. **Network Errors**: Implement retry logic for failed requests
4. **State Management**: Consider using React Context or Redux for authentication state

### Debug Mode
Enable debug logging in development:

```typescript
const DEBUG = process.env.NODE_ENV === 'development';

if (DEBUG) {
  console.log('Login response:', response);
}
```
