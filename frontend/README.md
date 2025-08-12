# Mcnary Technical Frontend

This is the React frontend application for the Mcnary Technical Backend project.

## Structure

- `src/components/` - React components (CaseStudies, Faqs, LeadForm)
- `src/services/` - API service layer for backend communication
- `src/App.tsx` - Main application component
- `src/App.css` - Main application styles

## Development

```bash
# Install dependencies
npm install

# Start development server
npm run dev

# Build for production
npm run build

# Preview production build
npm run preview
```

## API Proxy

The development server is configured to proxy `/api` requests to the Symfony backend running on `localhost:8000`.

## Technologies

- React 18
- TypeScript
- Vite
- CSS Modules (can be added later)

## 📚 Documentation

- **[Deployment Guide](DEPLOYMENT_GUIDE.md)** - Production deployment and server configuration
- **[Database Integration](DATABASE_INTEGRATION.md)** - API integration with Symfony backend

## 🚀 Quick Commands

```bash
# Development
npm install                    # Install dependencies
npm run dev                   # Start development server
npm run build                 # Build for production
npm run preview              # Preview production build

# Production
npm ci                       # Install production dependencies
npm run build               # Build optimized bundle
npm run lint                # Run linting
npm run type-check          # Check TypeScript types
```

## 🔧 Environment Configuration

Create `.env.local` for development:

```bash
VITE_API_BASE_URL=http://localhost:8000/api
VITE_APP_NAME="McNary Technical (Dev)"
VITE_ENABLE_DEBUG=true
```

Create `.env.production` for production:

```bash
VITE_API_BASE_URL=https://your-backend-domain.com/api
VITE_APP_NAME="McNary Technical"
VITE_ENABLE_DEBUG=false
```
