# Mcnary Technical Backend

This is the Symfony-based backend API for the Mcnary Technical project.

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
# Or: php -S localhost:8000 -t public/
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
