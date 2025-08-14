# 🚀 Quick Start Guide

## Get Both Applications Running in 3 Steps

### Prerequisites
- PHP 8.2+, Node.js 18+, PostgreSQL 16+, Composer
- **macOS users**: Install with `brew install php node postgresql composer`

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

## 🛑 Stopping Applications

- **Automatic**: Press `Ctrl+C` in the terminal running the script
- **Manual**: Stop each server in its respective terminal

## 📚 More Information

- **Full Setup Guide**: [DEVELOPMENT_SETUP.md](DEVELOPMENT_SETUP.md)
- **Architecture**: [ARCHITECTURE.md](ARCHITECTURE.md)
- **Troubleshooting**: See [DEVELOPMENT_SETUP.md](DEVELOPMENT_SETUP.md#troubleshooting)

## 🆘 Need Help?

1. Check the [DEVELOPMENT_SETUP.md](DEVELOPMENT_SETUP.md) for detailed instructions
2. Ensure PostgreSQL is running: `brew services start postgresql` (macOS)
3. Verify all dependencies are installed
4. Check the console output for specific error messages
