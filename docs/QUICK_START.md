# 🚀 Quick Start Guide

## Get Both Applications Running in 3 Steps

### Prerequisites
- **PHP 8.2+** with extensions: `pdo_pgsql`, `mbstring`, `intl`, `xml`, `zip`, `gd`, `curl`, `iconv`
- **Node.js 18+** and npm
- **PostgreSQL 16+** running locally
- **Composer** (PHP package manager)
- **Git**

**macOS users**: Install with `brew install php node postgresql composer`

### Step 1: Setup Environment
```bash
# Clone the repository
git clone <your-repo-url>
cd mcnary_technical_backend

# Copy and configure backend environment
cd backend
cp .env .env.local
# Edit .env.local with your PostgreSQL credentials
cd ..
```

### Step 2: Install Dependencies
```bash
# Backend dependencies
cd backend
composer install
cd ..

# Frontend dependencies
cd frontend
npm install
cd ..
```

### Step 3: Start Both Applications

#### Option A: Automatic (Recommended)
```bash
# macOS users
./dev-start-macos.sh

# Linux/Windows users
./dev-start.sh
```

#### Option B: Manual
```bash
# Terminal 1 - Backend
cd backend
symfony server:start --port=8000

# Terminal 2 - Frontend
cd frontend
npm run dev
```

## 🎯 What You Get

- **Frontend**: http://localhost:5173 (React + Vite)
- **Backend API**: http://localhost:8000 (Symfony + API Platform)
- **API Docs**: http://localhost:8000/api

## 🔧 First Time Setup

If this is your first time running the project:

```bash
cd backend

# Create database
php bin/console doctrine:database:create

# Run migrations
php bin/console doctrine:migrations:migrate

# Create system account
php bin/console app:create-system-account --username=admin --display-name="System Administrator" --permissions=read,write,admin

# Generate JWT keys (if needed)
php bin/console lexik:jwt:generate-keypair --overwrite
```

## 🗄️ Database Configuration

### Local PostgreSQL Setup
```bash
# Connect to PostgreSQL
psql -U postgres -h localhost

# Create database
CREATE DATABASE mcnary_marketing;

# Create user (optional)
CREATE USER mcnary_user WITH PASSWORD 'your_password';
GRANT ALL PRIVILEGES ON DATABASE mcnary_marketing TO mcnary_user;
```

### Environment Configuration (.env.local)
```env
APP_ENV=dev
APP_DEBUG=true
APP_SECRET=your-secret-key-here
DATABASE_URL="postgresql://username:password@127.0.0.1:5432/mcnary_marketing?serverVersion=16&charset=utf8"
JWT_SECRET_KEY=%kernel.project_dir%/config/jwt/private.pem
JWT_PUBLIC_KEY=%kernel.project_dir%/config/jwt/public.pem
JWT_PASSPHRASE=ChangeThis
CORS_ALLOW_ORIGIN="^https?://(localhost|127\.0\.0\.1)(:[0-9]+)?$"
```

### Frontend API Configuration (src/services/api.ts)
```typescript
const API_BASE_URL = 'http://localhost:8000';
```

## 🏗️ Entity Development

### Creating New Entities
Entities are PHP classes that represent database tables. See [DATABASE_GUIDE.md](./DATABASE_GUIDE.md) for detailed entity creation instructions.

### Basic Entity Structure
```php
<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
#[ORM\Table(name: 'products')]
#[ApiResource]
class Product
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    private string $id;

    #[ORM\Column(name: 'tenant_id', type: 'uuid', nullable: true)]
    private ?string $tenantId = null;

    #[ORM\Column(name: 'created_at', type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    public function __construct()
    {
        $this->id = Uuid::v4()->toRfc4122();
        $this->createdAt = new \DateTimeImmutable();
    }

    // Getters and setters
}
```

### Database Migration Workflow
```bash
# Create a new migration
php bin/console make:migration

# Review the generated migration file in migrations/ directory

# Run migrations
php bin/console doctrine:migrations:migrate

# Check migration status
php bin/console doctrine:migrations:status
```

## 🛑 Stopping Applications

- **Automatic**: Press `Ctrl+C` in the terminal running the script
- **Manual**: Stop each server in its respective terminal

## 🔍 Troubleshooting

### Common Issues

#### 1. Database Connection Failed
- Check PostgreSQL is running: `sudo systemctl status postgresql` (Linux) or `brew services start postgresql` (macOS)
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

## 🚀 Development Workflow

1. **Start both applications** using the setup above
2. **Make changes** to backend (PHP) or frontend (React)
3. **Backend auto-reloads** with Symfony server
4. **Frontend auto-reloads** with Vite HMR
5. **Test API endpoints** at http://localhost:8000/api
6. **Test frontend** at http://localhost:5173

## 📚 More Information

- **Database Guide**: [DATABASE_GUIDE.md](./DATABASE_GUIDE.md) - Complete database setup and entity management
- **API Reference**: [API_REFERENCE.md](./API_REFERENCE.md) - API development and security
- **Architecture**: [ARCHITECTURE.md](./ARCHITECTURE.md) - System design and principles
- **Deployment**: [DEPLOYMENT_GUIDE.md](./DEPLOYMENT_GUIDE.md) - Production deployment

## 🆘 Need Help?

1. Check this guide for common setup issues
2. Ensure PostgreSQL is running: `brew services start postgresql` (macOS)
3. Verify all dependencies are installed
4. Check the console output for specific error messages
5. Refer to the specific documentation files for detailed information
