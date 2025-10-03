# Python Backend - Migration from Symfony PHP

This directory contains the Python FastAPI backend migrated from the original Symfony PHP backend.

## Architecture Overview

### Technology Stack
- **Framework**: FastAPI (replacing Symfony)
- **Database**: PostgreSQL with SQLAlchemy ORM
- **Authentication**: JWT tokens with python-jose
- **Validation**: Pydantic models
- **Development**: Uvicorn ASGI server

### Project Structure

```
backend_python/
├── app/
│   ├── __init__.py
│   ├── main.py              # FastAPI application entry point
│   ├── core/
│   │   ├── __init__.py
│   │   ├── config.py        # Application configuration
│   │   ├── database.py      # Database setup and session management
│   │   └── auth.py          № Authentication utilities
│   ├── models/              # SQLAlchemy models (migrated from Symfony entities)
│   │   ├── __init__.py
│   │   ├── base.py          # Base model with timestamps
│   │   ├── user.py          # User model
│   │   ├── lead.py          # Lead model
│   │   ├── organization.py  # Organization model
│   │   ├── client.py        # Client model
│   │   ├── lead_source.py   # Lead Source model
│   │   ├── agency.py        # Agency model
│   │   └── tenant.py        # Tenant model
│   └── api/v1/              # API endpoints
│       ├── __init__.py
│       ├── auth.py          # Authentication endpoints
│       ├── leads.py         # Lead management endpoints
│       └── me.py            # Current user endpoint
├── requirements.txt         # Python dependencies
├── create_admin_user.py     # Script to create default admin user
└── run_backend.py           # Startup script
```

## Key Migration Changes

### 1. Framework Migration: Symfony → FastAPI
- **Symfony Controllers** → **FastAPI Routers**
- **Symfony Security** → **JWT-based auth with python-jose**
- **Symfony Doctrine ORM** → **SQLAlchemy**

### 2. Models & Entities
All Symfony entities have been converted to SQLAlchemy models:
- `User.php` → `models/user.py`
- `Lead.php` → `models/lead.py`
- `Organization.php` → `models/organization.py`
- And all other entities...

### 3. Authentication System
- Preserved JWT token-based authentication
- Same user credentials: `smcnary@live.com` / `TulsaSEO122`
- Same user roles and permissions structure

### 4. Lead Management
- **Endpoint**: `POST /api/v1/leads/` (instead of Symfony's `/api/leads`)
- **Content-Type**: `application/json` (simplified from `application/ld+json`)
- **Response Format**: JSON (not JSON-LD)

## Getting Started

### Prerequisites
- Python 3.8+
- PostgreSQL database
- Existing database with tables (can use same database as Symfony backend)

### Installation

1. Install Python dependencies:
```bash
pip install -r requirements.txt
```

2. Set up environment variables:
```bash
export DATABASE_URL="postgresql://admin:admin@localhost:5432/technical_db"
export SECRET_KEY="your-secret-key"
```

3. Create admin user:
```bash
python create_admin_user.py
```

4. Start the server:
```bash
python run_backend.py
# OR
uvicorn app.main:app --host 0.0.0.0 --port 8000 --reload
```

### API Endpoints

- **Health Check**: `GET /health`
- **API Documentation**: `GET /docs` (Swagger UI)
- **Login**: `POST /api/v1/auth/login`
- **Create Lead**: `POST /api/v1/leads/`
- **Get Leads**: `GET /api/v1/leads/`
- **Get Lead**: `GET /api/v1/leads/{lead_id}`

## Testing Lead Creation

### 1. Login to get JWT token:
```bash
curl -X POST "http://localhost:8000/api/v1/auth/login" \
     -H "Content-Type: application/json" \
     -d '{
       "email": "smcnary@live.com",
       "password": "TulsaSEO122"
     }'
```

### 2. Create a lead:
```bash
curl -X POST "http://localhost:8000/api/v1/leads/" \
     -H "Content-Type: application/json" \
     -H "Authorization: Bearer YOUR_JWT_TOKEN" \
     -d '{
       "full_name": "Blaine",
       "email": "blainefrierson@gmail.com",
       "phone": "918-264-0500",
       "firm": "A1 Accident Law",
       "website": "https://a1accidentandinjurylawofok.com/",
       "city": "Tulsa",
       "state": "OK",
       "zip_code": "74135",
       "practice_areas": ["Estate Law"],
       "status": "new_lead"
     }'
```

## Advantages of Python Migration

1. **Simplified Architecture**: No more complex Symfony routing/dependency injection issues
2. **Modern Framework**: FastAPI's automatic OpenAPI documentation
3. **Better Development Experience**: Python's simplicity and readability
4. **Better Performance**: FastAPI's ASGI implementation
5. **Native Python Ecosystem**: Easy integration with ML/AI libraries if needed

## Next Steps

1. **Test Lead Creation**: Verify the exact lead from the image can be created successfully
2. **Frontend Integration**: Update frontend to point to Python backend
3. **Deploy**: Set up production deployment for Python backend
4. **Performance Testing**: Compare performance with Symfony backend
5. **Feature Migration**: Migrate remaining Symfony features gradually

This Python backend should resolve the routing issues we encountered with the Symfony backend and provide a more reliable foundation for lead creation functionality.
