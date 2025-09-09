# API Integration Guide

Complete guide for integrating the React frontend with the Symfony backend API.

## 🚀 Quick Start

### Environment Setup
```bash
# .env.local
NEXT_PUBLIC_API_BASE_URL=http://localhost:8000
```

### Basic Usage
```tsx
import { useAuth } from '@/hooks/useAuth';
import { useData } from '@/hooks/useData';

function MyComponent() {
  const { user, isAuthenticated, login, logout } = useAuth();
  const { clients, getClients, isLoading } = useData();
  
  // Component logic here
}
```

## 🔐 Authentication

### useAuth Hook
```tsx
const {
  user, isAuthenticated, isLoading, error,
  login, logout, hasRole, isAdmin, isClientAdmin, isClientStaff
} = useAuth();
```

### Login Implementation
```tsx
function LoginComponent() {
  const { login, isLoading, error } = useAuth();
  
  const handleLogin = async (email: string, password: string) => {
    try {
      await login({ email, password });
      // Handle success
    } catch (error) {
      // Handle error
    }
  };
  
  return <form onSubmit={handleLogin}>{/* Form fields */}</form>;
}
```

### Route Protection
```tsx
import { ProtectedRoute, AdminOnly, ClientAdminOnly } from '@/components/auth/ProtectedRoute';

// Basic protection
<ProtectedRoute><AdminDashboard /></ProtectedRoute>

// Role-based protection
<AdminOnly><UserManagement /></AdminOnly>
<ClientAdminOnly><ClientDashboard /></ClientAdminOnly>

// Custom roles
<ProtectedRoute requiredRoles={['ROLE_CLIENT_ADMIN', 'ROLE_CLIENT_STAFF']}>
  <CampaignManager />
</ProtectedRoute>
```

## 📊 Data Management

### useData Hook
```tsx
const {
  // Data arrays
  clients, campaigns, packages, pages, mediaAssets, faqs, caseStudies, leads, users,
  
  // CRUD operations
  getClients, createClient, updateClient, getCampaigns, createCampaign, updateCampaign,
  
  // Utility functions
  getLoadingState, getErrorState, clearError, refreshAllData,
} = useData();
```

### Data Fetching Examples

#### Fetch Clients
```tsx
const { clients, getClients, getLoadingState, getErrorState } = useData();

useEffect(() => { getClients(); }, []);

if (getLoadingState('clients')) return <div>Loading...</div>;
if (getErrorState('clients')) return <div>Error: {getErrorState('clients')}</div>;

return (
  <div>
    {clients.map(client => (
      <div key={client.id}>{client.name}</div>
    ))}
  </div>
);
```

#### Create Client
```tsx
const { createClient } = useData();

const handleCreateClient = async (clientData) => {
  try {
    const newClient = await createClient({
      name: 'Acme Corp',
      website: 'https://acme.com',
      phone: '+1-555-123-4567',
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

// Active clients only
const activeClients = await getClients({ status: 'active' });

// Pagination
const paginatedClients = await getClients({ page: 1, per_page: 10 });

// Search
const searchResults = await getClients({ search: 'acme' });
```

## 🏗️ Data Types

### Core Entities
- **Client**: Business information, contact details, Google integrations
- **Campaign**: Marketing campaigns with goals, budget, and metrics
- **Package**: Service packages with pricing and features
- **Page**: Website pages with content and SEO metadata
- **MediaAsset**: File uploads with metadata and URLs
- **Faq**: Frequently asked questions with categories
- **CaseStudy**: Success stories with metrics and practice areas
- **Lead**: Potential clients with contact information and preferences
- **User**: System users with roles and permissions

### Key Fields
- All entities have `id`, `createdAt`, `updatedAt`
- Clients include Google Business Profile, Search Console, Analytics
- Campaigns have `clientId`, `type`, `status`, `budget`
- Users have `roles`, `clientId`, `tenantId` for multi-tenancy
- Leads track `status`, `practiceAreas`, `consent`

> **Note**: Full TypeScript interfaces are available in `src/types/`

## 🔄 State Management & Caching

### Automatic Caching (5 minutes)
```tsx
// First call - fetches from API
const clients = await getClients();

// Subsequent calls - returns cached data
const cachedClients = await getClients();

// Cache management
clearCache('clients');        // Clear specific cache
clearAllCache();              // Clear all cache
await refreshAllData();       // Force refresh
```

### Loading States
```tsx
const isLoadingClients = getLoadingState('clients');
if (isLoadingClients) return <div>Loading...</div>;
```

### Error Handling
```tsx
const clientError = getErrorState('clients');
if (clientError) {
  return (
    <div>
      Error: {clientError}
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
  const { clients, campaigns, leads, getClients, getCampaigns, getLeads, getLoadingState } = useData();

  useEffect(() => {
    Promise.all([getClients(), getCampaigns(), getLeads()]);
  }, []);

  const isLoading = getLoadingState('clients') || getLoadingState('campaigns') || getLoadingState('leads');
  if (isLoading) return <div>Loading dashboard...</div>;

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

### Always Handle Errors
```tsx
try {
  const data = await getClients();
} catch (error) {
  console.error('Failed to fetch clients:', error);
  // Show user-friendly error message
}
```

### Use Loading States
```tsx
if (getLoadingState('clients')) return <LoadingSpinner />;
```

### Clear Errors When Appropriate
```tsx
const handleInputChange = (e) => {
  setValue(e.target.value);
  clearError('clients');
};
```

### Provide User Feedback
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
import { ApiService } from '@/services/api';
const customApiService = new ApiService('https://api.example.com');
```

### Custom Cache Duration
```tsx
// In dataService.ts
private readonly CACHE_DURATION = 10 * 60 * 1000; // 10 minutes
```

### Custom Error Handling
```tsx
class CustomApiService extends ApiService {
  protected async handleError(response: Response): Promise<never> {
    if (response.status === 429) {
      throw new Error('Rate limit exceeded. Please try again later.');
    }
    return super.handleError(response);
  }
}
```

## 🧪 Testing

### Mock API Service
```tsx
// __mocks__/api.ts
export const mockApiService = {
  getClients: jest.fn(),
  createClient: jest.fn(),
};

jest.mock('@/services/api', () => ({
  apiService: mockApiService
}));
```

### Test Authentication State
```tsx
import { renderHook } from '@testing-library/react';
import { useAuth } from '@/hooks/useAuth';

test('should return authentication state', () => {
  const { result } = renderHook(() => useAuth());
  expect(result.current.isAuthenticated).toBe(false);
  expect(result.current.user).toBe(null);
});
```

## 🚀 Performance Tips

### React.memo for Expensive Components
```tsx
const ClientList = React.memo(({ clients }) => (
  <div>
    {clients.map(client => (
      <ClientCard key={client.id} client={client} />
    ))}
  </div>
));
```

### Virtual Scrolling for Large Lists
```tsx
import { FixedSizeList as List } from 'react-window';

function VirtualizedClientList({ clients }) {
  const Row = ({ index, style }) => (
    <div style={style}>
      <ClientCard client={clients[index]} />
    </div>
  );

  return (
    <List height={400} itemCount={clients.length} itemSize={80}>
      {Row}
    </List>
  );
}
```

### Debounce Search Inputs
```tsx
import { useDebouncedCallback } from 'use-debounce';

function SearchClients() {
  const { getClients } = useData();
  
  const debouncedSearch = useDebouncedCallback(
    (searchTerm) => getClients({ search: searchTerm }),
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

## 🆘 Troubleshooting

### Common Issues
1. **CORS Errors**: Ensure backend allows requests from your frontend domain
2. **Authentication Failures**: Check JWT token expiration and validity
3. **Data Not Loading**: Verify API endpoints are accessible and returning data
4. **Permission Errors**: Confirm user has required roles for the operation

### Getting Help
1. Check the browser console for error messages
2. Verify your environment configuration
3. Test API endpoints directly (e.g., using Postman)
4. Check backend logs for server-side errors

## 📚 Additional Resources

- [Backend API Documentation](../backend/documentation/API_ENDPOINTS.md)
- [Authentication Guide](../backend/documentation/CLIENT_AUTHENTICATION.md)
- [Database Schema](../backend/documentation/DATABASE_SCHEMA.md)

---

This API integration system provides a robust foundation for building feature-rich applications. The hooks and services handle all the complexity of data fetching, caching, and state management, allowing you to focus on building great user experiences.
