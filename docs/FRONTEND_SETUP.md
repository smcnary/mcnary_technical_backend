# 🎨 Frontend Setup Guide

## 📋 Overview

This guide will help you set up the React frontend application and ensure it's properly connected to the Symfony backend.

## 🚀 Quick Start

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

## 🔌 Backend Connection

### API Configuration

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

### API Service

The frontend includes a comprehensive API service (`src/services/api.ts`) that provides:

- **Authentication** - Login, token management
- **Lead Management** - Submit and retrieve leads
- **Content Management** - Case studies, FAQs, pages
- **Business Intelligence** - Campaigns, clients, users
- **Error Handling** - Automatic token refresh and error management

## 🧪 Testing the Connection

### API Test Component

The frontend includes an "API Test" component that allows you to:

1. **Test Connection** - Verify the backend is reachable
2. **Health Check** - Check backend status
3. **View API Info** - See available endpoints
4. **Monitor Errors** - Debug connection issues

To access it:
1. Navigate to the frontend app
2. Click "API Test" in the navigation
3. Use the test buttons to verify connectivity

### Manual Testing

You can also test the connection manually:

```bash
# Test backend directly
curl http://localhost:8000/api

# Test through frontend proxy
curl http://localhost:3000/api
```

## 🔧 Development Workflow

### 1. Start Both Applications

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

### 2. Make Changes

- **Backend changes** - PHP server auto-reloads
- **Frontend changes** - Vite HMR updates automatically
- **API changes** - Frontend will reflect new endpoints

### 3. Test Endpoints

Use the API Test component or check the browser's Network tab to verify API calls are working correctly.

## 📱 Available Components

### Core Components

- **LeadForm** - Submit legal inquiries
- **CaseStudies** - Display case study information
- **Faqs** - Show frequently asked questions
- **ApiTest** - Test backend connectivity

### API Integration

All components use the centralized API service for data fetching and submission. The service handles:

- Authentication tokens
- Error handling
- Request/response formatting
- API endpoint management

## 🚨 Troubleshooting

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

## 🔒 Security Considerations

### Environment Variables

- Never commit `.env.local` to version control
- Use different API URLs for development/staging/production
- Secure sensitive configuration values

### API Security

- JWT tokens are stored in localStorage (consider httpOnly cookies for production)
- All API requests include proper authentication headers
- CORS is configured to prevent unauthorized access

## 📚 Next Steps

After successful setup:

1. **Test all API endpoints** using the API Test component
2. **Create additional components** for new features
3. **Implement authentication flows** for user management
4. **Add error boundaries** for better error handling
5. **Set up testing** with Jest and React Testing Library

## 📖 Additional Resources

- **[Backend Documentation](../backend/documentation/)** - Complete backend setup and API reference
- **[Vite Documentation](https://vitejs.dev/)** - Build tool configuration
- **[React Documentation](https://react.dev/)** - Component development
- **[TypeScript Documentation](https://www.typescriptlang.org/)** - Type safety and interfaces

---

**Happy coding! 🚀**
