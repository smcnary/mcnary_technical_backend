# Frontend Service

This is the Next.js React application for the McNary Technical Backend platform.

## Quick Start

```bash
# Install dependencies
npm install

# Set up environment
cp env.example .env.local
# Edit .env.local with your API endpoints

# Start development server
npm run dev
```

## Documentation

- [Frontend Setup](../../docs/development/README.md)
- [API Integration](../../docs/api/README.md)
- [Architecture Overview](../../docs/architecture/README.md)

## Key Features

- Next.js 14 with App Router
- React 18 with TypeScript
- Tailwind CSS for styling
- shadcn/ui component library
- Client dashboard and admin portal
- Audit wizard interface

## Environment Variables

Copy from `env.example` and configure:
- `NEXT_PUBLIC_API_URL` - Backend API URL
- `NEXT_PUBLIC_AUDIT_SERVICE_URL` - Audit service URL
