# CounselRank.legal Backend

This is the Symfony-based backend API for the CounselRank.legal platform.

## Structure

- `src/` - PHP source code
  - `Entity/` - Doctrine entities
  - `Controller/` - API controllers
  - `Repository/` - Data repositories
  - `ApiPlatform/` - API Platform extensions
  - `MultiTenancy/` - Multi-tenancy implementation
- `config/` - Symfony configuration files
- `migrations/` - Database migrations
- `public/` - Web root directory
- `templates/` - Twig templates
- `var/` - Cache and logs
- `bin/` - Symfony console and other executables

## Setup

```bash
# Install dependencies
composer install

# Set up environment
cp .env.example .env
# Edit .env with your database credentials

# Run migrations
php bin/console doctrine:migrations:migrate

# Start server
symfony server:start
# Or: php -S localhost:8080 -t public/
```

## API Endpoints

- `GET /api` - API discovery
- `POST /api/leads` - Lead submission
- `GET /api/case_studies` - Case studies listing
- `GET /api/faqs` - FAQ listing

## Technologies

- Symfony 6
- API Platform
- Doctrine ORM
- JWT Authentication
- Multi-tenancy support

## 📚 Documentation

- **[Entity Creation Guide](ENTITY_CREATION_GUIDE.md)** - How to create and manage database entities
- **[Database Setup](DATABASE_SETUP.md)** - Database connection and migration workflow
- **[Server Deployment](SERVER_DEPLOYMENT.md)** - Production deployment and server configuration

## 🚀 Quick Commands

```bash
# Development
composer install                    # Install dependencies
bin/console server:start          # Start development server
bin/console make:migration        # Create new migration
bin/console doctrine:migrations:migrate  # Run migrations

# Production
composer install --no-dev --optimize-autoloader  # Install production dependencies
bin/console cache:clear --env=prod              # Clear production cache
bin/console doctrine:migrations:migrate --env=prod --no-interaction  # Run production migrations
```
