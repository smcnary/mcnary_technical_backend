# System Architecture

## Overview

This system follows a **layered architecture** where:

1. **Frontend** → connects to **Backend API** (no direct database access)
2. **Backend** → connects to **PostgreSQL Database**
3. **System Account** → handles all database operations through the backend

## Architecture Diagram

```
┌─────────────┐    HTTP/API    ┌─────────────┐    Database    ┌─────────────┐
│   Frontend  │ ──────────────→ │   Backend   │ ──────────────→ │ PostgreSQL  │
│  (React)    │                 │ (Symfony)   │                 │             │
└─────────────┘                 └─────────────┘                 └─────────────┘
```

## Key Components

### Frontend (React + TypeScript)
- **No direct database connections**
- Communicates with backend via REST API
- Uses `ApiService` for all data operations
- Located in `frontend/` directory

### Backend (Symfony + API Platform)
- **Single point of database access**
- Provides REST API endpoints
- Handles authentication and authorization
- Located in `backend/` directory

### System Account
- **SystemUser entity** for system-level operations
- **SystemAccountService** for business logic
- **Permission-based access control**
- All database operations go through this service

## Database Configuration

### Production
- **PostgreSQL** database
- Connection via `DATABASE_URL` environment variable
- Located in `backend/.env`

### Testing
- **PostgreSQL** test database
- Configured in CI/CD workflows
- Uses `test_db_test` database

## API Endpoints

All frontend-backend communication uses these endpoints:

- `/leads` - Lead management
- `/case_studies` - Case study content
- `/faqs` - FAQ management
- `/system_users` - System account management

## Security Model

1. **Frontend**: No database credentials
2. **Backend**: Database credentials stored securely
3. **System Account**: Permission-based access control
4. **API**: JWT authentication required

## Benefits

✅ **Security**: No database credentials in frontend  
✅ **Maintainability**: Single point of database access  
✅ **Scalability**: Backend can be scaled independently  
✅ **Testing**: Clear separation of concerns  
✅ **Deployment**: Frontend and backend can be deployed separately  

## Commands

### Create System Account
```bash
cd backend
php bin/console app:create-system-account --username=admin --display-name="System Administrator" --permissions=read,write,admin
```

### Test Database Connection
```bash
cd backend
php bin/console doctrine:database:create --env=test
php bin/console doctrine:schema:create --env=test
```
