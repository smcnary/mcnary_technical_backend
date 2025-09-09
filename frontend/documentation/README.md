# Frontend Documentation

This directory contains comprehensive documentation for the Tulsa SEO Platform frontend application.

## 📚 Documentation Overview

### Core Documentation
- **[📖 Documentation Index](INDEX.md)** - Complete documentation navigation guide
- **[🔌 API Integration Guide](API_INTEGRATION_GUIDE.md)** - Complete frontend-backend integration
- **[✅ Implementation Status](IMPLEMENTATION_TEST_RESULTS.md)** - Current implementation status

### Related Documentation
- **[📖 Backend Documentation](../backend/documentation/README.md)** - Complete backend documentation
- **[🏗️ Architecture Overview](../backend/documentation/ARCHITECTURE.md)** - System design and principles
- **[🔌 Backend API Reference](../backend/documentation/API_REFERENCE.md)** - Complete REST API v1 reference

## 🚀 Quick Start

### Prerequisites
- Node.js 18+ and npm/yarn
- Backend API running on `http://localhost:8000`

### Installation

```bash
# Navigate to frontend directory
cd frontend

# Install dependencies
npm install

# Copy environment template
cp env.example .env.local

# Start development server
npm run dev
```

### Environment Setup

Create `.env.local` with the following configuration:

```bash
NEXT_PUBLIC_API_BASE_URL=http://localhost:8000
NEXT_PUBLIC_APP_NAME=Tulsa SEO Platform
```

## 🏗️ Frontend Architecture

### Technology Stack
- **Framework**: Next.js 14 with App Router
- **Language**: TypeScript 5.3
- **Styling**: TailwindCSS 3.4
- **UI Components**: shadcn/ui
- **State Management**: React Context + Custom Hooks
- **Authentication**: JWT with secure token storage
- **API Integration**: Custom service layer with caching

### Project Structure

```
frontend/
├── src/
│   ├── app/                    # Next.js App Router pages
│   ├── components/             # Reusable UI components
│   │   ├── auth/              # Authentication components
│   │   ├── forms/             # Form components
│   │   └── ui/                # Base UI components
│   ├── hooks/                 # Custom React hooks
│   │   ├── useAuth.ts         # Authentication hook
│   │   └── useData.ts         # Data management hook
│   ├── services/              # API service layer
│   │   ├── api.ts             # Core API service
│   │   └── authService.ts     # Authentication service
│   ├── types/                 # TypeScript type definitions
│   └── utils/                 # Utility functions
├── public/                    # Static assets
├── tests/                     # Test files
└── documentation/             # This documentation
```

## 🎯 Key Features

### ✅ Implemented Features
- **Complete Authentication System** - JWT-based with role management
- **API Integration Layer** - Comprehensive data management with caching
- **Role-Based Access Control** - Admin, Client Admin, and Client Staff roles
- **Responsive Design** - Mobile-first with TailwindCSS
- **Form Management** - Validation and error handling
- **Audit Wizard** - Multi-step client onboarding process

### 🔄 Data Management
- **Automatic Caching** - 5-minute cache for API responses
- **Loading States** - Individual loading indicators per data type
- **Error Handling** - Comprehensive error management
- **Real-time Updates** - Optimistic updates with rollback

### 🔐 Security Features
- **Secure Token Storage** - HTTP-only cookies for JWT
- **CSRF Protection** - Built-in CSRF token handling
- **Role Validation** - Server-side role verification
- **Route Protection** - Component-level access control

## 📖 Getting Started Guide

### 1. Development Setup
```bash
# Start backend API (from project root)
./dev-start.sh

# Start frontend (in separate terminal)
cd frontend
npm run dev
```

### 2. Authentication Flow
```tsx
import { useAuth } from '@/hooks/useAuth';

function LoginComponent() {
  const { login, isAuthenticated, user } = useAuth();
  
  if (isAuthenticated) {
    return <div>Welcome, {user?.name}!</div>;
  }
  
  return <LoginForm onLogin={login} />;
}
```

### 3. Data Management
```tsx
import { useData } from '@/hooks/useData';

function ClientList() {
  const { clients, getClients, getLoadingState } = useData();
  
  useEffect(() => {
    getClients();
  }, []);
  
  if (getLoadingState('clients')) {
    return <div>Loading...</div>;
  }
  
  return (
    <div>
      {clients.map(client => (
        <ClientCard key={client.id} client={client} />
      ))}
    </div>
  );
}
```

## 🧪 Testing

### Running Tests
```bash
# Run all tests
npm test

# Run tests with coverage
npm run test:coverage

# Run Playwright E2E tests
npm run test:e2e
```

### Test Coverage
- **Unit Tests**: Components and hooks
- **Integration Tests**: API service layer
- **E2E Tests**: Complete user workflows
- **Visual Tests**: Screenshot comparisons

## 🚀 Deployment

### Production Build
```bash
# Build for production
npm run build

# Start production server
npm start
```

### Environment Variables
```bash
# Production environment
NEXT_PUBLIC_API_BASE_URL=https://api.yourdomain.com
NEXT_PUBLIC_APP_NAME=Tulsa SEO Platform
```

## 🔧 Development Tools

### Available Scripts
- `npm run dev` - Start development server
- `npm run build` - Build for production
- `npm run start` - Start production server
- `npm run lint` - Run ESLint
- `npm run type-check` - Run TypeScript checks
- `npm test` - Run test suite

### Code Quality
- **ESLint** - Code linting and formatting
- **TypeScript** - Type safety and IntelliSense
- **Prettier** - Code formatting
- **Husky** - Git hooks for quality checks

## 📞 Support

For frontend-specific questions:
1. Start with the [Documentation Index](INDEX.md) for complete navigation
2. Check the [API Integration Guide](API_INTEGRATION_GUIDE.md) for detailed implementation examples
3. Review the [Implementation Status](IMPLEMENTATION_TEST_RESULTS.md) for current features
4. Refer to the [Backend Documentation](../backend/documentation/README.md) for API details

---

**Frontend Status**: ✅ Core features implemented and tested  
**Version**: 1.0.0  
**Last Updated**: January 15, 2025
