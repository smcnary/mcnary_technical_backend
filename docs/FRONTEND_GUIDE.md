# Complete Frontend Guide

This comprehensive guide covers everything you need to know about the CounselRank.legal frontend application, including setup, development, authentication integration, UI specifications, and deployment.

## Table of Contents

1. [Quick Start](#quick-start)
2. [Project Structure](#project-structure)
3. [Development Setup](#development-setup)
4. [Authentication Integration](#authentication-integration)
5. [UI Specifications](#ui-specifications)
6. [Component Architecture](#component-architecture)
7. [API Integration](#api-integration)
8. [Styling and Theming](#styling-and-theming)
9. [Testing](#testing)
10. [Deployment](#deployment)
11. [Troubleshooting](#troubleshooting)

## Quick Start

### 1. Install Dependencies

```bash
cd frontend
npm install
```

### 2. Configure Environment

Copy the environment template and configure it:

```bash
cp env.example .env.local
```

Edit `.env.local` with your backend configuration:

```env
# API Configuration
VITE_API_BASE_URL=http://localhost:8000

# App Configuration
VITE_APP_NAME="CounselRank.legal"
VITE_APP_VERSION=1.0.0

# Feature Flags
VITE_ENABLE_ANALYTICS=false
VITE_ENABLE_DEBUG=true
```

### 3. Start Development Server

```bash
npm run dev
```

The frontend will be available at: http://localhost:3000

## Project Structure

```
frontend/
├── src/
│   ├── components/          # Reusable UI components
│   │   ├── auth/           # Authentication components
│   │   ├── forms/          # Form components
│   │   ├── layout/         # Layout components
│   │   └── ui/             # Basic UI components
│   ├── pages/              # Page components
│   │   ├── public/         # Public marketing pages
│   │   └── portal/         # Client portal pages
│   ├── services/           # API services and utilities
│   ├── hooks/              # Custom React hooks
│   ├── types/              # TypeScript type definitions
│   ├── utils/              # Utility functions
│   └── styles/             # Global styles and themes
├── public/                 # Static assets
├── tests/                  # Test files
└── docs/                   # Frontend documentation
```

## Development Setup

### Prerequisites
- **Node.js 18+** and npm
- **Backend API** running on port 8000
- **Git** for version control

### Backend Connection

The frontend is configured to connect to the Symfony backend at `http://localhost:8000` by default. This can be changed via the `VITE_API_BASE_URL` environment variable.

### Vite Proxy Configuration

The Vite development server includes a proxy configuration that forwards `/api` requests to the backend:

```typescript
// vite.config.ts
server: {
  proxy: {
    '/api': {
      target: 'http://localhost:8000',
      changeOrigin: true,
      secure: false,
    }
  }
}
```

### Development Workflow

#### 1. Start Both Applications

**Terminal 1 - Backend:**
```bash
cd backend
php -S localhost:8000 -t public/
```

**Terminal 2 - Frontend:**
```bash
cd frontend
npm run dev
```

#### 2. Make Changes

- **Backend changes** - PHP server auto-reloads
- **Frontend changes** - Vite HMR updates automatically
- **API changes** - Frontend will reflect new endpoints

#### 3. Test Endpoints

Use the API Test component or check the browser's Network tab to verify API calls are working correctly.

## Authentication Integration

### Updated Components

#### 1. ClientLoginModal.tsx
- **Purpose**: Modal for client user authentication
- **API Endpoint**: `POST /api/v1/clients/login`
- **Features**:
  - Email and password validation
  - JWT token storage
  - User and client data storage
  - Error handling with specific messages

#### 2. ClientRegisterModal.tsx
- **Purpose**: Complete client registration form
- **API Endpoint**: `POST /api/v1/clients/register`
- **Features**:
  - Multi-section form (Organization, Client, Admin User)
  - Comprehensive validation
  - Password strength meter
  - Success/error handling

#### 3. API Utility (lib/api.ts)
- **Purpose**: Centralized API functions and type definitions
- **Features**:
  - Type-safe API calls
  - Authentication token management
  - Error handling
  - Data storage utilities

### Authentication Flow

1. **Registration**: User fills out registration form → Creates organization, tenant, client, and admin user
2. **Login**: User authenticates → Receives JWT token and user/client data
3. **Storage**: Token and data stored in localStorage for session persistence
4. **Authenticated Requests**: Include JWT token in Authorization header

### Usage Examples

#### Basic Login Modal Usage
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

#### Complete Authentication Flow
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

## UI Specifications

### Global UI Foundations

#### Typography & Layout
- Clean, high-contrast headings
- Max content width 1200px
- Comfortable line-height
- Generous whitespace

#### Color & Theme
- Professional legal palette (navy/ink, slate, white) with accent (teal/cyan)
- High-contrast states for accessibility (WCAG AA+)

#### Navigation
- Sticky header
- Skip-to-content link
- Responsive drawer on mobile

#### Feedback
- Toasts for success/info/error
- Inline validation
- Skeleton loaders
- Empty states

#### Accessibility
- Landmarks (header/nav/main/aside/footer)
- Focus rings
- Proper aria-* for disclosure/accordion, dialogs, and charts

#### State Management
- Query caching for lists
- Optimistic updates where safe (lead notes, task status)
- Suspense-friendly loaders

### Public Site Layout

#### Header / Top Navigation
```
+-----------------------------------------------------------------------+
| Logo        Services  Pricing  Case Studies  Blog  FAQ  About  Contact|
|                                                 [Client Login] [CTA]  |
+-----------------------------------------------------------------------+
```

**UX**
- Desktop mega-menu for Services; mobile hamburger → full-screen drawer
- CTA = "Book Demo" opens modal scheduler or scrolls to contact form

**Business Rules**
- Highlight current page
- Show "Client Login" only if not authenticated

#### Hero + Primary CTA
```
+---------------------------------------------------------------+
|  Headline: Legal SEO that wins cases                          |
|  Subhead: Local + AI-first SEO for law firms                  |
|  [Book Demo]  [See Pricing]                                   |
|  Trust badges  ★★★★★  (Google, Clutch, etc.)                  |
+---------------------------------------------------------------+
```

**UX**
- Primary CTA sticky on mobile
- Trust badges collapse into carousel on small screens

**Business Rules**
- CTA logs marketing event; if user is authenticated (client), change CTA → "Open Portal"

#### Services Overview
```
+------------------+  +------------------+  +------------------+
| Local SEO        |  | Content & AEO    |  | GBP & Reviews    |
| bullets...       |  | bullets...       |  | bullets...       |
| [Learn more]     |  | [Learn more]     |  | [Learn more]     |
+------------------+  +------------------+  +------------------+
```

**UX**
- Cards with concise bullets and iconography
- Hover → subtle lift; keyboard focus visible

#### Pricing (Packages)
```
+-----------+     +-----------+     +-----------+
| Starter   |     | Growth    |     | Premium   |
| $3k/mo    |     | $6k/mo    |     | $12k/mo   |
| ✓ features|     | ✓         |     | ✓         |
| [Select]  |     | [Select]  |     | [Select]  |
+-----------+     +-----------+     +-----------+
```

**UX**
- Highlight middle plan
- Toggle monthly/annual
- Feature comparison table

**Business Rules**
- Selecting plan → opens contact/lead capture with chosen `package_id`

#### Contact / Lead Capture
```
+----------------------------------------+
|  Name  [__________]                    |
|  Email [__________]                    |
|  Phone [__________]                    |
|  Firm   [__________]  Practice [v]     |
|  Message [__________________________]  |
|  [ I agree to terms ]                  |
|  [ I'm interested in: plan radios ]    |
|  hCaptcha                              |
|  [Submit]                              |
+----------------------------------------+
```

**UX**
- Real-time validation
- Success screen with calendar link
- Privacy/consent copy

**Business Rules**
- Required: name+one of (email, phone), consent checkbox, hCaptcha
- UTM captured from querystring + referrer and stored on Lead

### Client Portal Layout

#### Layout & Navigation
```
+-----------------+----------------------------------------------+
| Logo            |  Topbar: Client Switcher  Search  Profile    |
| Dashboard       |----------------------------------------------|
| Leads           |  [View renders here]                         |
| Reviews         |                                              |
| Keywords        |                                              |
| Rankings        |                                              |
| Content         |                                              |
| Audits          |                                              |
| Recs/Tasks      |                                              |
| Backlinks       |                                              |
| Citations        |                                              |
| Billing         |                                              |
| Settings        |                                              |
+-----------------+----------------------------------------------+
```

**UX**
- Client switcher dropdown (agency roles only)
- Keyboard shortcuts (/, g l, g r, etc.)
- Persistent filters per view (URL querystring-based)

**Business Rules**
- RBAC: CLIENT_* see only their `client_id`; AGENCY_* can switch

#### Dashboard
```
+----- Rank Summary -----+  +----- Leads -----+  +----- Reviews -----+
| Avg Pos | Top 3 | Δ    |  | New | Won | CR  |  | ★ Avg | New | Δ  |
+------------------------+  +-----------------+  +-------------------+
| Trend chart (30 days)  |  | Recent items…   |  | Latest reviews…   |
+------------------------+  +-----------------+  +-------------------+
```

**UX**
- Time range picker
- Cards link to detailed modules

**Business Rules**
- Compute via materialized views if heavy

## Component Architecture

### Core Components

#### Public Site Components
- **LeadForm** - Submit legal inquiries
- **CaseStudies** - Display case study information
- **Faqs** - Show frequently asked questions
- **ApiTest** - Test backend connectivity

#### Client Portal Components
- **Dashboard** - Overview of key metrics
- **LeadsList** - Lead management interface
- **ReviewsList** - Review management
- **KeywordsList** - Keyword tracking
- **RankingsChart** - Ranking visualization
- **ContentCalendar** - Content planning
- **AuditResults** - Audit findings display
- **RecommendationsList** - Task management
- **BacklinksList** - Backlink monitoring
- **CitationsList** - Citation management
- **BillingDashboard** - Subscription management
- **SettingsPanel** - Account configuration

### API Integration

All components use the centralized API service for data fetching and submission. The service handles:

- Authentication tokens
- Error handling
- Request/response formatting
- API endpoint management

#### API Service Layer

Create `src/services/api.ts`:

```typescript
import axios, { AxiosInstance, AxiosResponse } from 'axios';

class ApiService {
  private api: AxiosInstance;

  constructor() {
    this.api = axios.create({
      baseURL: import.meta.env.VITE_API_BASE_URL || 'http://localhost:8000/api',
      timeout: 10000,
      headers: {
        'Content-Type': 'application/json',
      },
    });

    // Request interceptor for authentication
    this.api.interceptors.request.use(
      (config) => {
        const token = localStorage.getItem('auth_token');
        if (token) {
          config.headers.Authorization = `Bearer ${token}`;
        }
        return config;
      },
      (error) => Promise.reject(error)
    );

    // Response interceptor for error handling
    this.api.interceptors.response.use(
      (response: AxiosResponse) => response,
      (error) => {
        if (error.response?.status === 401) {
          // Handle unauthorized access
          localStorage.removeItem('auth_token');
          window.location.href = '/login';
        }
        return Promise.reject(error);
      }
    );
  }

  // Generic CRUD methods
  async get<T>(endpoint: string): Promise<T> {
    const response = await this.api.get<T>(endpoint);
    return response.data;
  }

  async post<T>(endpoint: string, data: any): Promise<T> {
    const response = await this.api.post<T>(endpoint, data);
    return response.data;
  }

  async put<T>(endpoint: string, data: any): Promise<T> {
    const response = await this.api.put<T>(endpoint, data);
    return response.data;
  }

  async delete<T>(endpoint: string): Promise<T> {
    const response = await this.api.delete<T>(endpoint);
    return response.data;
  }
}

export const apiService = new ApiService();
```

#### Entity-Specific Services

Create `src/services/leads.ts`:

```typescript
import { apiService } from './api';

export interface Lead {
  id: string;
  name: string;
  email: string;
  phone?: string;
  company?: string;
  message: string;
  status: 'new' | 'contacted' | 'qualified' | 'converted';
  createdAt: string;
  updatedAt: string;
}

export class LeadService {
  static async getLeads(params?: any): Promise<Lead[]> {
    const queryString = new URLSearchParams(params).toString();
    return apiService.get<Lead[]>(`/leads?${queryString}`);
  }

  static async getLead(id: string): Promise<Lead> {
    return apiService.get<Lead>(`/leads/${id}`);
  }

  static async createLead(data: Partial<Lead>): Promise<Lead> {
    return apiService.post<Lead>('/leads', data);
  }

  static async updateLead(id: string, data: Partial<Lead>): Promise<Lead> {
    return apiService.put<Lead>(`/leads/${id}`, data);
  }

  static async deleteLead(id: string): Promise<void> {
    return apiService.delete<void>(`/leads/${id}`);
  }
}
```

#### React Component Usage

```typescript
import React, { useState, useEffect } from 'react';
import { LeadService, Lead } from '../services/leads';

export const LeadsList: React.FC = () => {
  const [leads, setLeads] = useState<Lead[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const fetchLeads = async () => {
      try {
        const data = await LeadService.getLeads();
        setLeads(data);
      } catch (error) {
        console.error('Failed to fetch leads:', error);
      } finally {
        setLoading(false);
      }
    };

    fetchLeads();
  }, []);

  if (loading) return <div>Loading...</div>;

  return (
    <div>
      {leads.map(lead => (
        <div key={lead.id}>
          <h3>{lead.name}</h3>
          <p>{lead.email}</p>
          <p>Status: {lead.status}</p>
        </div>
      ))}
    </div>
  );
};
```

## Styling and Theming

### CSS Framework
The project uses **Tailwind CSS** for styling with custom components and utilities.

### Key CSS Classes Used
The components use Tailwind CSS classes. Key classes include:
- `input-field`: Custom input styling
- `btn-primary`: Primary button styling
- Form validation states use color-coded borders

### Customization Options
1. **Styling**: Modify CSS classes to match your design system
2. **Validation**: Update validation rules in the `validate()` functions
3. **Redirects**: Customize post-login/registration redirects
4. **Success Messages**: Modify success message text and behavior

### Theme Configuration

```typescript
// tailwind.config.js
module.exports = {
  content: ['./src/**/*.{js,ts,jsx,tsx}'],
  theme: {
    extend: {
      colors: {
        primary: {
          50: '#f0f9ff',
          500: '#0ea5e9',
          900: '#0c4a6e',
        },
        secondary: {
          50: '#f8fafc',
          500: '#64748b',
          900: '#0f172a',
        },
      },
      fontFamily: {
        sans: ['Inter', 'system-ui', 'sans-serif'],
      },
    },
  },
  plugins: [],
}
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

### Testing Commands

```bash
# Run tests
npm run test

# Run tests in watch mode
npm run test:watch

# Run tests with coverage
npm run test:coverage

# Run e2e tests
npm run test:e2e
```

## Deployment

### Environment Configuration

```typescript
// Consider using environment variables for API URLs
const API_BASE_URL = process.env.NEXT_PUBLIC_API_URL || 'http://localhost:8000';
```

### Build Process

```bash
# Build for production
npm run build

# Preview production build
npm run preview

# Build and analyze bundle
npm run build:analyze
```

### Performance Targets
- LCP < 2.5s, INP < 200ms, CLS < 0.1 on key pages
- Images lazy-loaded; prefetch next-route data; cache API GETs

### Security & Privacy (UI)
- Mask PII in tables by default; reveal on hover with permission
- CSRF for cookie flows; JWT in Authorization header for API

## Troubleshooting

### Common Issues

1. **Backend Not Running**
   - Ensure the Symfony backend is started on port 8000
   - Check `http://localhost:8000/api` in your browser

2. **CORS Errors**
   - Verify the backend CORS configuration allows `localhost:3000`
   - Check the Vite proxy configuration

3. **API Endpoints Not Found**
   - Verify the backend routes are properly configured
   - Check the API service endpoint URLs

4. **Authentication Issues**
   - Ensure JWT keys are generated in the backend
   - Check the authentication endpoints are working

5. **CORS Errors**: Ensure backend CORS is configured for your frontend domain
6. **Token Expiry**: Handle JWT token expiration and refresh
7. **Network Errors**: Implement retry logic for failed requests
8. **State Management**: Consider using React Context or Redux for authentication state

### Debug Commands

```bash
# Check if backend is running
curl -v http://localhost:8000/api

# Check frontend proxy
curl -v http://localhost:3000/api

# View backend logs
tail -f backend/var/log/dev.log

# Check frontend build
npm run build
```

### Debug Mode
Enable debug logging in development:

```typescript
const DEBUG = process.env.NODE_ENV === 'development';

if (DEBUG) {
  console.log('Login response:', response);
}
```

### Error Handling

#### Login Errors
- **401**: Invalid credentials
- **403**: Account access denied (wrong user type or inactive account)
- **400**: Validation errors
- **500**: Server errors

#### Registration Errors
- **409**: Duplicate organization/client/user
- **400**: Validation errors
- **500**: Server errors

#### Frontend Error Display
Both components handle errors gracefully:
- Form-level errors display at the top
- Field-level errors display below each input
- Network errors show generic retry message

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

### Environment Variables
- Never commit `.env.local` to version control
- Use different API URLs for development/staging/production
- Secure sensitive configuration values

### API Security
- JWT tokens are stored in localStorage (consider httpOnly cookies for production)
- All API requests include proper authentication headers
- CORS is configured to prevent unauthorized access

## Analytics & Telemetry

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

### Events to Track
- `lead_submitted`, `plan_selected`, `video_played`, `faq_opened`
- Portal: `lead_status_changed`, `review_replied`, `keyword_added`, `audit_run_started`, `recommendation_completed`, `invoice_downloaded`

## Next Steps

After successful setup:

1. **Test all API endpoints** using the API Test component
2. **Create additional components** for new features
3. **Implement authentication flows** for user management
4. **Add error boundaries** for better error handling
5. **Set up testing** with Jest and React Testing Library

## Related Documentation

- [Authentication Guide](./AUTHENTICATION_GUIDE.md) - Complete authentication system
- [API Documentation](./API_DOCUMENTATION.md) - Complete API reference
- [Setup Guide](./SETUP_GUIDE.md) - Development setup guide
- [Database Guide](./DATABASE_GUIDE.md) - Database setup and management

---

**Last Updated:** January 2025  
**Maintained By:** Development Team  
**Status:** Complete and consolidated ✅

