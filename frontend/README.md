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
