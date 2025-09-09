# Backend Service

This is the main Symfony API service for the McNary Technical Backend platform.

## Quick Start

```bash
# Install dependencies
composer install

# Set up environment
cp ../config/environments/env.dev .env
# Edit .env with your database credentials

# Run migrations
php bin/console doctrine:migrations:migrate

# Start development server
symfony serve
# or
php -S localhost:8000 -t public
```

## Documentation

- [API Reference](../../docs/api/README.md)
- [Architecture Overview](../../docs/architecture/README.md)
- [Development Setup](../../docs/development/README.md)
- [Deployment Guide](../../docs/deployment/README.md)

## Key Features

- Multi-tenant architecture with agency/client hierarchy
- JWT-based authentication
- API Platform for automatic API generation
- PostgreSQL database with Doctrine ORM
- Role-based access control (RBAC)

## Environment Variables

Copy from `../config/environments/env.dev` and configure:
- `DATABASE_URL` - PostgreSQL connection string
- `JWT_SECRET_KEY` - JWT signing key
- `APP_SECRET` - Symfony application secret
