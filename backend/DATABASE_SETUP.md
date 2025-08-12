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

## Useful Commands

- Check database status: `docker-compose ps`
- View logs: `docker-compose logs database`
- Stop database: `docker-compose down`
- Reset database: `docker-compose down -v && docker-compose up -d`

## Security Note

⚠️ **Important:** The default password `postgres` should be changed in production environments.
