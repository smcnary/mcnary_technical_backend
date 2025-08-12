# Database Connection Setup

This Symfony application is configured to connect to a PostgreSQL database running in Docker.

## Quick Start

1. **Start the database:**
   ```bash
   docker-compose up -d
   ```

2. **Connect to the database:**
   ```bash
   source connect-db.sh
   ```

3. **Run migrations:**
   ```bash
   bin/console doctrine:migrations:migrate
   ```

## Connection Details

- **Host:** 127.0.0.1:5433
- **Database:** mcnary_marketing
- **Username:** postgres
- **Password:** postgres
- **Port:** 5433 (mapped from container port 5432)

## Docker Services

The database runs in a Docker container with the following configuration:
- PostgreSQL 16 Alpine
- Persistent volume storage
- Health checks enabled
- Port 5433 exposed on host

## Environment Variables

To use the database connection, set the following environment variable:
```bash
export DATABASE_URL="postgresql://postgres:postgres@127.0.0.1:5433/mcnary_marketing?serverVersion=16&charset=utf8"
```

## Database Migration Workflow

### Development Environment

1. **Create a new migration:**
   ```bash
   bin/console make:migration
   ```

2. **Review the generated migration file** in `migrations/` directory

3. **Run migrations:**
   ```bash
   bin/console doctrine:migrations:migrate
   ```

4. **Check migration status:**
   ```bash
   bin/console doctrine:migrations:status
   ```

### Production Deployment

1. **Generate production migration:**
   ```bash
   bin/console make:migration --env=prod
   ```

2. **Deploy migration files** to production server

3. **Run migrations safely:**
   ```bash
   bin/console doctrine:migrations:migrate --env=prod --no-interaction
   ```

4. **Verify migration success:**
   ```bash
   bin/console doctrine:migrations:status --env=prod
   ```

### Migration Best Practices

- **Always backup** production database before running migrations
- **Test migrations** in staging environment first
- **Use transactions** for complex migrations
- **Version control** all migration files
- **Document breaking changes** in migration files

### Rollback Procedures

1. **Check migration history:**
   ```bash
   bin/console doctrine:migrations:list
   ```

2. **Rollback to specific version:**
   ```bash
   bin/console doctrine:migrations:migrate prev
   ```

3. **Rollback to specific migration:**
   ```bash
   bin/console doctrine:migrations:migrate VERSION_NUMBER
   ```

## Useful Commands

- Check database status: `docker-compose ps`
- View logs: `docker-compose logs database`
- Stop database: `docker-compose down`
- Reset database: `docker-compose down -v && docker-compose up -d`
- Check schema: `bin/console doctrine:schema:validate`
- Update schema: `bin/console doctrine:schema:update --dump-sql`

## Security Note

⚠️ **Important:** The default password `postgres` should be changed in production environments.

## Troubleshooting

### Common Issues

1. **Connection refused:**
   - Check if Docker container is running
   - Verify port mapping in `compose.yaml`

2. **Migration fails:**
   - Check database connection
   - Verify entity annotations
   - Check for syntax errors in migration files

3. **Schema validation errors:**
   - Run `bin/console doctrine:schema:validate`
   - Check entity mapping annotations
   - Verify database table structure
