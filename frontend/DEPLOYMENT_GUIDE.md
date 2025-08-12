# 🚀 CounselRank.legal Frontend Deployment Guide

This guide covers deploying the React frontend application to production, including build optimization, server configuration, and integration with the Symfony backend.

## 📋 Prerequisites

- Node.js 18+ and npm
- Web server (Nginx/Apache) or CDN service
- Access to your backend API
- Domain name and SSL certificate (recommended)

## 🏗️ Build Configuration

### 1. Environment Variables

Create `.env.production` file:

```bash
# API Configuration
VITE_API_BASE_URL=https://your-backend-domain.com/api
VITE_APP_NAME="CounselRank.legal"
VITE_APP_VERSION=1.0.0

# Feature Flags
VITE_ENABLE_ANALYTICS=true
VITE_ENABLE_DEBUG=false

# External Services
VITE_GOOGLE_ANALYTICS_ID=GA_MEASUREMENT_ID
```

### 2. Build Optimization

Update `vite.config.ts` for production:

```typescript
import { defineConfig } from 'vite'
import react from '@vitejs/plugin-react'

export default defineConfig({
  plugins: [react()],
  build: {
    outDir: 'dist',
    sourcemap: false,
    minify: 'terser',
    rollupOptions: {
      output: {
        manualChunks: {
          vendor: ['react', 'react-dom'],
          utils: ['axios', 'react-router-dom']
        }
      }
    },
    terserOptions: {
      compress: {
        drop_console: true,
        drop_debugger: true
      }
    }
  },
  server: {
    proxy: {
      '/api': {
        target: 'http://localhost:8000',
        changeOrigin: true,
        secure: false
      }
    }
  }
})
```

## 🚀 Build and Deploy

### 1. Production Build

```bash
# Install dependencies
npm ci

# Build for production
npm run build

# Preview build locally
npm run preview
```

### 2. Build Output

The build process creates a `dist/` directory containing:
- `index.html` - Main HTML file
- `assets/` - Optimized JavaScript, CSS, and other assets
- Static files optimized for production

## 🌐 Web Server Configuration

### 1. Nginx Configuration

```nginx
server {
    listen 80;
    server_name your-frontend-domain.com;
    root /var/www/mcnary_frontend/dist;
    index index.html;

    # Gzip compression
    gzip on;
    gzip_vary on;
    gzip_min_length 1024;
    gzip_types text/plain text/css text/xml text/javascript application/javascript application/xml+rss application/json;

    # Security headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header Referrer-Policy "no-referrer-when-downgrade" always;

    # Handle React Router
    location / {
        try_files $uri $uri/ /index.html;
    }

    # Cache static assets
    location ~* \.(js|css|png|jpg|jpeg|gif|ico|svg)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }

    # API proxy (if needed)
    location /api/ {
        proxy_pass https://your-backend-domain.com;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
    }

    error_log /var/log/nginx/mcnary_frontend_error.log;
    access_log /var/log/nginx/mcnary_frontend_access.log;
}
```

### 2. Apache Configuration

```apache
<VirtualHost *:80>
    ServerName your-frontend-domain.com
    DocumentRoot /var/www/mcnary_frontend/dist
    
    <Directory /var/www/mcnary_frontend/dist>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
        
        # Handle React Router
        RewriteEngine On
        RewriteCond %{REQUEST_FILENAME} !-f
        RewriteCond %{REQUEST_FILENAME} !-d
        RewriteRule . /index.html [L]
    </Directory>
    
    # Enable compression
    <IfModule mod_deflate.c>
        AddOutputFilterByType DEFLATE text/plain
        AddOutputFilterByType DEFLATE text/html
        AddOutputFilterByType DEFLATE text/xml
        AddOutputFilterByType DEFLATE text/css
        AddOutputFilterByType DEFLATE application/xml
        AddOutputFilterByType DEFLATE application/xhtml+xml
        AddOutputFilterByType DEFLATE application/rss+xml
        AddOutputFilterByType DEFLATE application/javascript
        AddOutputFilterByType DEFLATE application/x-javascript
    </IfModule>
    
    ErrorLog ${APACHE_LOG_DIR}/mcnary_frontend_error.log
    CustomLog ${APACHE_LOG_DIR}/mcnary_frontend_access.log combined
</VirtualHost>
```

## 🔄 Continuous Deployment

### 1. Automated Deployment Script

Create `deploy.sh`:

```bash
#!/bin/bash
set -e

echo "🚀 Starting frontend deployment..."

# Navigate to project directory
cd /var/www/mcnary_frontend

# Pull latest changes
git pull origin main

# Install dependencies
npm ci

# Build for production
npm run build

# Set permissions
sudo chown -R www-data:www-data dist/
sudo chmod -R 755 dist/

# Restart web server
sudo systemctl reload nginx

echo "✅ Frontend deployment completed successfully!"
```

### 2. GitHub Actions Workflow

Create `.github/workflows/deploy-frontend.yml`:

```yaml
name: Deploy Frontend

on:
  push:
    branches: [ main ]

jobs:
  deploy:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v3
      
      - name: Setup Node.js
        uses: actions/setup-node@v3
        with:
          node-version: '18'
          cache: 'npm'
      
      - name: Install dependencies
        run: npm ci
      
      - name: Build
        run: npm run build
        env:
          VITE_API_BASE_URL: ${{ secrets.VITE_API_BASE_URL }}
      
      - name: Deploy to server
        uses: appleboy/ssh-action@v0.1.5
        with:
          host: ${{ secrets.HOST }}
          username: ${{ secrets.USERNAME }}
          key: ${{ secrets.KEY }}
          script: |
            cd /var/www/mcnary_frontend
            ./deploy.sh
```

## 🔒 Security Configuration

### 1. Content Security Policy

Add to your HTML or server configuration:

```html
<meta http-equiv="Content-Security-Policy" 
      content="default-src 'self'; 
               script-src 'self' 'unsafe-inline' 'unsafe-eval' https://www.googletagmanager.com; 
               style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; 
               font-src 'self' https://fonts.gstatic.com; 
               img-src 'self' data: https:; 
               connect-src 'self' https://your-backend-domain.com;">
```

### 2. Environment Variable Security

- Never commit `.env.production` to version control
- Use build-time environment variables
- Validate API endpoints in production builds

## 📱 Progressive Web App (PWA)

### 1. PWA Configuration

Create `public/manifest.json`:

```json
{
  "name": "McNary Technical",
  "short_name": "McNary",
  "description": "McNary Technical Marketing Platform",
  "start_url": "/",
  "display": "standalone",
  "background_color": "#ffffff",
  "theme_color": "#000000",
  "icons": [
    {
      "src": "/icon-192x192.png",
      "sizes": "192x192",
      "type": "image/png"
    },
    {
      "src": "/icon-512x512.png",
      "sizes": "512x512",
      "type": "image/png"
    }
  ]
}
```

### 2. Service Worker

Create `public/sw.js`:

```javascript
const CACHE_NAME = 'mcnary-cache-v1';
const urlsToCache = [
  '/',
  '/index.html',
  '/static/js/bundle.js',
  '/static/css/main.css'
];

self.addEventListener('install', event => {
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then(cache => cache.addAll(urlsToCache))
  );
});

self.addEventListener('fetch', event => {
  event.respondWith(
    caches.match(event.request)
      .then(response => response || fetch(event.request))
  );
});
```

## 📊 Performance Optimization

### 1. Bundle Analysis

```bash
# Install bundle analyzer
npm install --save-dev webpack-bundle-analyzer

# Analyze bundle
npm run build
npx webpack-bundle-analyzer dist/stats.json
```

### 2. Image Optimization

```bash
# Install image optimization tools
npm install --save-dev imagemin imagemin-mozjpeg imagemin-pngquant

# Optimize images in build process
npm run build:images
```

### 3. Code Splitting

Implement React.lazy() for route-based code splitting:

```typescript
import React, { lazy, Suspense } from 'react';

const CaseStudies = lazy(() => import('./components/CaseStudies'));
const Faqs = lazy(() => import('./components/Faqs'));

function App() {
  return (
    <Suspense fallback={<div>Loading...</div>}>
      <Routes>
        <Route path="/case-studies" element={<CaseStudies />} />
        <Route path="/faqs" element={<Faqs />} />
      </Routes>
    </Suspense>
  );
}
```

## 🔍 Monitoring and Analytics

### 1. Error Tracking

```typescript
// Install Sentry
npm install @sentry/react @sentry/tracing

// Initialize in main.tsx
import * as Sentry from "@sentry/react";

Sentry.init({
  dsn: "YOUR_SENTRY_DSN",
  integrations: [new Sentry.BrowserTracing()],
  tracesSampleRate: 1.0,
});
```

### 2. Performance Monitoring

```typescript
// Web Vitals
import { getCLS, getFID, getFCP, getLCP, getTTFB } from 'web-vitals';

function sendToAnalytics(metric) {
  // Send to your analytics service
  console.log(metric);
}

getCLS(sendToAnalytics);
getFID(sendToAnalytics);
getFCP(sendToAnalytics);
getLCP(sendToAnalytics);
getTTFB(sendToAnalytics);
```

## 🚨 Troubleshooting

### Common Issues

1. **Build fails:**
   - Check Node.js version compatibility
   - Verify all dependencies are installed
   - Check for TypeScript errors

2. **API calls fail in production:**
   - Verify CORS configuration on backend
   - Check API base URL in environment variables
   - Verify SSL certificates

3. **Routing issues:**
   - Ensure server is configured for SPA routing
   - Check that all routes fall back to index.html

## 📚 Additional Resources

- [Vite Documentation](https://vitejs.dev/)
- [React Deployment](https://create-react-app.dev/docs/deployment/)
- [Nginx Configuration](https://nginx.org/en/docs/)
- [PWA Best Practices](https://web.dev/progressive-web-apps/)

---

**Happy deploying! 🚀**
