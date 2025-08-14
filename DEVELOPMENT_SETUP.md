# Development Setup Guide

## Prerequisites

- **PHP 8.2+** with extensions: `pdo_pgsql`, `mbstring`, `intl`, `xml`, `zip`, `gd`, `curl`, `iconv`
- **Node.js 18+** and npm
- **PostgreSQL 16+** running locally
- **Composer** (PHP package manager)
- **Git**

## Quick Start

### 1. Clone and Setup
```bash
git clone <your-repo-url>
cd mcnary_technical_backend
```

### 2. Backend Setup
```bash
cd backend

# Install PHP dependencies
composer install

# Copy environment file
cp .env .env.local

# Edit .env.local with your database credentials
# DATABASE_URL="postgresql://username:password@127.0.0.1:5432/database_name"

# Create database
php bin/console doctrine:database:create

# Run migrations
php bin/console doctrine:migrations:migrate

# Create system account
php bin/console app:create-system-account --username=admin --display-name="System Administrator" --permissions=read,write,admin

# Start Symfony development server
symfony server:start -d
```

### 3. Frontend Setup
```bash
cd frontend

# Install Node.js dependencies
npm install

# Start development server
npm run dev
```

## Running Both Applications

### Option 1: Manual (Recommended for Development)

#### Terminal 1 - Backend
```bash
cd backend
symfony server:start --port=8000
```

#### Terminal 2 - Frontend
```bash
cd frontend
npm run dev
```

### Option 2: Using the Development Script

```bash
# From project root
./dev-start.sh
```

## Application URLs

- **Backend API**: http://localhost:8000
- **Frontend App**: http://localhost:5173 (Vite default)
- **API Documentation**: http://localhost:8000/api

## Environment Configuration

### Backend (.env.local)
```env
APP_ENV=dev
APP_DEBUG=true
APP_SECRET=your-secret-key-here
DATABASE_URL="postgresql://username:password@127.0.0.1:5432/database_name?serverVersion=16&charset=utf8"
JWT_SECRET_KEY=%kernel.project_dir%/config/jwt/private.pem
JWT_PUBLIC_KEY=%kernel.project_dir%/config/jwt/public.pem
JWT_PASSPHRASE=ChangeThis
CORS_ALLOW_ORIGIN="^https?://(localhost|127\.0\.0\.1)(:[0-9]+)?$"
```

### Frontend (src/services/api.ts)
```typescript
const API_BASE_URL = 'http://localhost:8000';
```

## Database Setup

### PostgreSQL Connection
```bash
# Connect to PostgreSQL
psql -U postgres -h localhost

# Create database
CREATE DATABASE mcnary_marketing;

# Create user (optional)
CREATE USER mcnary_user WITH PASSWORD 'your_password';
GRANT ALL PRIVILEGES ON DATABASE mcnary_marketing TO mcnary_user;
```

### Run Migrations
```bash
cd backend
php bin/console doctrine:migrations:migrate
```

## Troubleshooting

### Common Issues

#### 1. Database Connection Failed
- Check PostgreSQL is running: `sudo systemctl status postgresql`
- Verify connection string in `.env.local`
- Check firewall settings

#### 2. Port Already in Use
- Backend: `symfony server:stop` then restart
- Frontend: Kill process on port 5173 or change port in `vite.config.ts`

#### 3. CORS Issues
- Ensure backend CORS configuration allows frontend origin
- Check `CORS_ALLOW_ORIGIN` in backend `.env.local`

#### 4. JWT Issues
- Generate JWT keys: `php bin/console lexik:jwt:generate-keypair`
- Check JWT configuration in `config/packages/lexik_jwt_authentication.yaml`

### Useful Commands

```bash
# Backend
cd backend
php bin/console cache:clear
php bin/console debug:router
php bin/console doctrine:schema:validate

# Frontend
cd frontend
npm run build
npm run preview
```

## Development Workflow

1. **Start both applications** using the setup above
2. **Make changes** to backend (PHP) or frontend (React)
3. **Backend auto-reloads** with Symfony server
4. **Frontend auto-reloads** with Vite HMR
5. **Test API endpoints** at http://localhost:8000/api
6. **Test frontend** at http://localhost:5173

## Production Build

### Frontend
```bash
cd frontend
npm run build
# Built files in dist/ directory
```

### Backend
```bash
cd backend
composer install --no-dev --optimize-autoloader
php bin/console cache:clear --env=prod
```
