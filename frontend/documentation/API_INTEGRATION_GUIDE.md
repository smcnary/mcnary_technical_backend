# Frontend API Integration Guide

This guide explains how to use the comprehensive API integration system that connects your React frontend to the Symfony backend.

## 🚀 Quick Start

The API integration is already set up and ready to use. Here's how to get started:

### 1. Environment Configuration

Make sure your `.env.local` file has the correct API base URL:

```bash
NEXT_PUBLIC_API_BASE_URL=http://localhost:8000
```

### 2. Basic Usage

Import and use the hooks in your components:

```tsx
import { useAuth } from '../hooks/useAuth';
import { useData } from '../hooks/useData';

function MyComponent() {
  const { user, isAuthenticated, login, logout } = useAuth();
  const { clients, getClients, isLoading } = useData();
  
  // Your component logic here
}
```

## 🔐 Authentication System

### useAuth Hook

The `useAuth` hook provides complete authentication functionality:

```tsx
const {
  user,                    // Current user object
  isAuthenticated,         // Boolean authentication status
  isLoading,              // Loading state
  error,                  // Error message
  login,                  // Login function
  logout,                 // Logout function
  hasRole,                // Check specific role
  isAdmin,                // Check if admin
  isClientAdmin,          // Check if client admin
  isClientStaff,          // Check if client staff
} = useAuth();
```

### Login Example

```tsx
import { useAuth } from '../hooks/useAuth';

function LoginComponent() {
  const { login, isLoading, error } = useAuth();
  
  const handleLogin = async (email: string, password: string) => {
    try {
      await login({ email, password });
      // Redirect or show success message
    } catch (error) {
      // Handle error
    }
  };
  
  return (
    <form onSubmit={handleLogin}>
      {/* Your login form */}
    </form>
  );
}
```

### Protected Routes

Use the `ProtectedRoute` component for role-based access control:

```tsx
import { ProtectedRoute, AdminOnly, ClientAdminOnly } from '../components/auth/ProtectedRoute';

// Basic protection
<ProtectedRoute>
  <AdminDashboard />
</ProtectedRoute>

// Role-based protection
<AdminOnly>
  <UserManagement />
</AdminOnly>

<ClientAdminOnly>
  <ClientDashboard />
</ClientAdminOnly>

// Custom role requirements
<ProtectedRoute requiredRoles={['ROLE_CLIENT_ADMIN', 'ROLE_CLIENT_STAFF']}>
  <CampaignManager />
</ProtectedRoute>
```

## 📊 Data Management

### useData Hook

The `useData` hook provides access to all backend data with caching and state management:

```tsx
const {
  // Data arrays
  clients,
  campaigns,
  packages,
  pages,
  mediaAssets,
  faqs,
  caseStudies,
  leads,
  users,
  
  // CRUD operations
  getClients,
  createClient,
  updateClient,
  getCampaigns,
  createCampaign,
  updateCampaign,
  
  // Utility functions
  getLoadingState,
  getErrorState,
  clearError,
  refreshAllData,
} = useData();
```

### Data Fetching Examples

#### Fetch All Clients

```tsx
const { clients, getClients, getLoadingState, getErrorState } = useData();

useEffect(() => {
  getClients();
}, []);

if (getLoadingState('clients')) {
  return <div>Loading clients...</div>;
}

if (getErrorState('clients')) {
  return <div>Error: {getErrorState('clients')}</div>;
}

return (
  <div>
    {clients.map(client => (
      <div key={client.id}>{client.name}</div>
    ))}
  </div>
);
```

#### Create New Client

```tsx
const { createClient } = useData();

const handleCreateClient = async (clientData) => {
  try {
    const newClient = await createClient({
      name: 'Acme Corp',
      website: 'https://acme.com',
      phone: '+1-555-123-4567',
      // ... other fields
    });
    console.log('Client created:', newClient);
  } catch (error) {
    console.error('Failed to create client:', error);
  }
};
```

#### Filtered Data

```tsx
const { getClients } = useData();

// Get active clients only
const activeClients = await getClients({ status: 'active' });

// Get clients with pagination
const paginatedClients = await getClients({ 
  page: 1, 
  per_page: 10 
});

// Search clients
const searchResults = await getClients({ 
  search: 'acme' 
});
```

## 🏗️ Available Data Types

### Client Management

```tsx
interface Client {
  id: string;
  name: string;
  slug?: string;
  description?: string;
  website?: string;
  phone?: string;
  address?: string;
  city?: string;
  state?: string;
  zipCode?: string;
  status?: string;
  tenantId?: string;
  metadata?: Record<string, unknown>;
  googleBusinessProfile?: {
    profileId?: string;
    rating?: number;
    reviewsCount?: number;
  };
  googleSearchConsole?: {
    property?: string;
    verificationStatus?: string;
  };
  googleAnalytics?: {
    propertyId?: string;
    trackingId?: string;
  };
  createdAt: string;
  updatedAt: string;
}
```

### Campaign Management

```tsx
interface Campaign {
  id: string;
  name: string;
  description?: string;
  type: string;
  status: string;
  clientId: string;
  startDate?: string;
  endDate?: string;
  budget?: number;
  goals?: string[];
  metrics?: string[];
  createdAt: string;
  updatedAt: string;
}
```

### Package Management

```tsx
interface Package {
  id: string;
  name: string;
  description?: string;
  price?: number;
  billingCycle?: string;
  features?: string[];
  isPopular?: boolean;
  sortOrder?: number;
  clientId?: string;
  createdAt: string;
  updatedAt: string;
}
```

### Page Management

```tsx
interface Page {
  id: string;
  title: string;
  slug: string;
  content?: string;
  type?: string;
  status?: string;
  sortOrder?: number;
  publishedAt?: string;
  createdAt: string;
  updatedAt: string;
}
```

### Media Asset Management

```tsx
interface MediaAsset {
  id: string;
  filename: string;
  originalName: string;
  mimeType: string;
  fileSize: number;
  type?: string;
  status?: string;
  clientId?: string;
  url?: string;
  createdAt: string;
  updatedAt: string;
}
```

### FAQ Management

```tsx
interface Faq {
  id: string;
  question: string;
  answer: string;
  category?: string;
  sortOrder?: number;
  isActive: boolean;
  sort: number;
  createdAt: string;
  updatedAt: string;
}
```

### Case Study Management

```tsx
interface CaseStudy {
  id: string;
  title: string;
  slug: string;
  summary?: string;
  metricsJson: Record<string, unknown>;
  heroImage?: string;
  practiceArea?: string;
  isActive: boolean;
  sort: number;
  createdAt: string;
  updatedAt: string;
}
```

### Lead Management

```tsx
interface Lead {
  id: string;
  name: string;
  email: string;
  phone?: string;
  firm?: string;
  website?: string;
  practiceAreas: string[];
  city?: string;
  state?: string;
  budget?: string;
  timeline?: string;
  notes?: string;
  consent: boolean;
  status: 'pending' | 'contacted' | 'qualified' | 'disqualified';
  createdAt: string;
  updatedAt: string;
}
```

### User Management

```tsx
interface User {
  id: string;
  username?: string;
  email: string;
  displayName?: string;
  name?: string;
  firstName?: string;
  lastName?: string;
  roles: string[];
  clientId?: string;
  tenantId?: string;
  status?: string;
  lastLoginAt?: string;
  createdAt: string;
  updatedAt: string;
}
```

## 🔄 State Management & Caching

### Automatic Caching

The data service automatically caches responses for 5 minutes:

```tsx
// First call - fetches from API
const clients = await getClients();

// Subsequent calls within 5 minutes - returns cached data
const cachedClients = await getClients();

// Clear cache for specific data type
clearCache('clients');

// Clear all cache
clearAllCache();

// Force refresh all data
await refreshAllData();
```

### Loading States

Track loading states for individual data types:

```tsx
const isLoadingClients = getLoadingState('clients');
const isLoadingCampaigns = getLoadingState('campaigns');

if (isLoadingClients) {
  return <div>Loading clients...</div>;
}
```

### Error Handling

Handle errors for individual data types:

```tsx
const clientError = getErrorState('clients');

if (clientError) {
  return (
    <div>
      Error loading clients: {clientError}
      <button onClick={() => clearError('clients')}>Dismiss</button>
    </div>
  );
}
```

## 🎯 Common Use Cases

### Dashboard with Multiple Data Sources

```tsx
function Dashboard() {
  const { user } = useAuth();
  const {
    clients,
    campaigns,
    leads,
    getClients,
    getCampaigns,
    getLeads,
    getLoadingState,
  } = useData();

  useEffect(() => {
    // Load all data on component mount
    Promise.all([
      getClients(),
      getCampaigns(),
      getLeads(),
    ]);
  }, []);

  const isLoading = getLoadingState('clients') || 
                   getLoadingState('campaigns') || 
                   getLoadingState('leads');

  if (isLoading) {
    return <div>Loading dashboard...</div>;
  }

  return (
    <div>
      <h1>Welcome, {user?.name}</h1>
      <div className="grid grid-cols-3 gap-4">
        <div>Clients: {clients.length}</div>
        <div>Campaigns: {campaigns.length}</div>
        <div>Leads: {leads.length}</div>
      </div>
    </div>
  );
}
```

### Form Submission with Error Handling

```tsx
function CreateClientForm() {
  const { createClient } = useData();
  const [formData, setFormData] = useState({});
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [error, setError] = useState(null);

  const handleSubmit = async (e) => {
    e.preventDefault();
    setIsSubmitting(true);
    setError(null);

    try {
      const newClient = await createClient(formData);
      // Handle success
      console.log('Client created:', newClient);
    } catch (error) {
      setError(error.message);
    } finally {
      setIsSubmitting(false);
    }
  };

  return (
    <form onSubmit={handleSubmit}>
      {error && <div className="error">{error}</div>}
      {/* Form fields */}
      <button type="submit" disabled={isSubmitting}>
        {isSubmitting ? 'Creating...' : 'Create Client'}
      </button>
    </form>
  );
}
```

### Role-Based Component Rendering

```tsx
function AdminPanel() {
  const { isAdmin, isClientAdmin } = useAuth();

  if (!isAdmin && !isClientAdmin) {
    return <div>Access denied</div>;
  }

  return (
    <div>
      <h1>Admin Panel</h1>
      {isAdmin && <UserManagement />}
      {isClientAdmin && <ClientManagement />}
    </div>
  );
}
```

## 🚨 Error Handling Best Practices

### 1. Always Handle Errors

```tsx
try {
  const data = await getClients();
} catch (error) {
  console.error('Failed to fetch clients:', error);
  // Show user-friendly error message
}
```

### 2. Use Loading States

```tsx
if (getLoadingState('clients')) {
  return <LoadingSpinner />;
}
```

### 3. Clear Errors When Appropriate

```tsx
// Clear error when user starts typing
const handleInputChange = (e) => {
  setValue(e.target.value);
  clearError('clients');
};
```

### 4. Provide User Feedback

```tsx
const { error, clearError } = useAuth();

if (error) {
  return (
    <Alert variant="destructive">
      <AlertDescription>{error}</AlertDescription>
      <button onClick={clearError}>Dismiss</button>
    </Alert>
  );
}
```

## 🔧 Advanced Configuration

### Custom API Base URL

```tsx
import { ApiService } from '../services/api';

// Create custom API service instance
const customApiService = new ApiService('https://api.example.com');
```

### Custom Cache Duration

```tsx
// In dataService.ts, modify the CACHE_DURATION constant
private readonly CACHE_DURATION = 10 * 60 * 1000; // 10 minutes
```

### Custom Error Handling

```tsx
// Extend the API service for custom error handling
class CustomApiService extends ApiService {
  protected async handleError(response: Response): Promise<never> {
    if (response.status === 429) {
      // Handle rate limiting
      throw new Error('Rate limit exceeded. Please try again later.');
    }
    
    // Call parent error handling
    return super.handleError(response);
  }
}
```

## 📱 Mobile Considerations

### Responsive Data Loading

```tsx
function MobileOptimizedList() {
  const { clients, getClients } = useData();
  const [page, setPage] = useState(1);
  const [hasMore, setHasMore] = useState(true);

  const loadMore = async () => {
    const newClients = await getClients({ 
      page, 
      per_page: 20 
    });
    
    if (newClients.length < 20) {
      setHasMore(false);
    }
    
    setPage(prev => prev + 1);
  };

  return (
    <div>
      {clients.map(client => (
        <ClientCard key={client.id} client={client} />
      ))}
      {hasMore && (
        <button onClick={loadMore}>Load More</button>
      )}
    </div>
  );
}
```

## 🧪 Testing

### Mock API Service for Testing

```tsx
// __mocks__/api.ts
export const mockApiService = {
  getClients: jest.fn(),
  createClient: jest.fn(),
  // ... other methods
};

// In your test
jest.mock('../services/api', () => ({
  apiService: mockApiService
}));
```

### Test Authentication State

```tsx
import { renderHook } from '@testing-library/react';
import { useAuth } from '../hooks/useAuth';

test('should return authentication state', () => {
  const { result } = renderHook(() => useAuth());
  
  expect(result.current.isAuthenticated).toBe(false);
  expect(result.current.user).toBe(null);
});
```

## 🚀 Performance Tips

### 1. Use React.memo for Expensive Components

```tsx
const ClientList = React.memo(({ clients }) => {
  return (
    <div>
      {clients.map(client => (
        <ClientCard key={client.id} client={client} />
      ))}
    </div>
  );
});
```

### 2. Implement Virtual Scrolling for Large Lists

```tsx
import { FixedSizeList as List } from 'react-window';

function VirtualizedClientList({ clients }) {
  const Row = ({ index, style }) => (
    <div style={style}>
      <ClientCard client={clients[index]} />
    </div>
  );

  return (
    <List
      height={400}
      itemCount={clients.length}
      itemSize={80}
    >
      {Row}
    </List>
  );
}
```

### 3. Debounce Search Inputs

```tsx
import { useDebouncedCallback } from 'use-debounce';

function SearchClients() {
  const { getClients } = useData();
  
  const debouncedSearch = useDebouncedCallback(
    (searchTerm) => {
      getClients({ search: searchTerm });
    },
    300
  );

  return (
    <input
      type="text"
      onChange={(e) => debouncedSearch(e.target.value)}
      placeholder="Search clients..."
    />
  );
}
```

## 🔍 Debugging

### Enable Debug Logging

```tsx
// In your component
useEffect(() => {
  console.log('Auth state:', { isAuthenticated, user });
  console.log('Data state:', { clients, campaigns });
}, [isAuthenticated, user, clients, campaigns]);
```

### Check Network Requests

Open browser DevTools → Network tab to see all API requests and responses.

### Verify Authentication Token

```tsx
const { getAuthToken } = useAuth();
console.log('Current token:', getAuthToken());
```

## 📚 Additional Resources

- [Backend API Documentation](../backend/documentation/API_ENDPOINTS.md)
- [Authentication Guide](../backend/documentation/CLIENT_AUTHENTICATION.md)
- [Database Schema](../backend/documentation/DATABASE_SCHEMA.md)

## 🆘 Troubleshooting

### Common Issues

1. **CORS Errors**: Ensure backend allows requests from your frontend domain
2. **Authentication Failures**: Check JWT token expiration and validity
3. **Data Not Loading**: Verify API endpoints are accessible and returning data
4. **Permission Errors**: Confirm user has required roles for the operation

### Getting Help

If you encounter issues:

1. Check the browser console for error messages
2. Verify your environment configuration
3. Test API endpoints directly (e.g., using Postman)
4. Check backend logs for server-side errors

---

This API integration system provides a robust foundation for building feature-rich applications. The hooks and services handle all the complexity of data fetching, caching, and state management, allowing you to focus on building great user experiences.
