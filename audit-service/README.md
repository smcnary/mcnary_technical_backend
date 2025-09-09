# Audit Service

This is the dedicated Symfony microservice for SEO audit processing.

## Quick Start

```bash
# Install dependencies
composer install

# Set up environment
cp env.example .env
# Edit .env with your service credentials

# Start development server
symfony serve --port=8001
# or
php -S localhost:8001 -t public
```

## Documentation

- [Audit Service Specification](../../docs/architecture/audit-service-spec.md)
- [Architecture Overview](../../docs/architecture/README.md)
- [Development Setup](../../docs/development/README.md)

## Key Features

- Web crawling and SEO analysis
- Symfony Messenger for async processing
- Headless Chrome for screenshots
- Multi-tenant audit processing
- Report generation (HTML, PDF, CSV)

## Environment Variables

Copy from `env.example` and configure:
- `DATABASE_URL` - PostgreSQL connection string
- `REDIS_DSN` - Redis connection for message queues
- `OBJECT_STORE_*` - S3-compatible storage configuration
