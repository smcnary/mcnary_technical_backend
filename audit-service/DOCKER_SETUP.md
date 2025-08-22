# Docker Setup Guide for Audit Service

This guide explains how to set up and run the Audit Service using Docker.

## Prerequisites

- Docker Desktop (version 20.10+)
- Docker Compose (version 2.0+)
- At least 4GB of available RAM
- At least 10GB of available disk space

## Quick Start

1. **Clone and navigate to the project:**
   ```bash
   cd audit-service
   ```

2. **Start all services:**
   ```bash
   make start
   # or manually:
   docker-compose up -d
   ```

3. **Check service status:**
   ```bash
   make health
   # or manually:
   curl http://localhost:8080/api/health
   ```

4. **Access the application:**
   - Main application: http://localhost:8080
   - API documentation: http://localhost:8080/api/docs
   - MinIO Console: http://localhost:9001
   - Jaeger Tracing: http://localhost:16686
   - Prometheus: http://localhost:9090

## Services Overview

### Core Services
- **PHP Application** (`php`): Symfony application with PHP 8.2-FPM
- **Nginx** (`nginx`): Web server and reverse proxy (port 8080)
- **PostgreSQL** (`postgres`): Primary database (port 5432)
- **Redis** (`redis`): Cache and message queue (port 6379)

### Storage & Processing
- **MinIO** (`minio`): S3-compatible object storage (ports 9000, 9001)
- **Chrome Headless** (`chrome`): Browser automation for screenshots (port 9222)

### Monitoring & Observability
- **Jaeger** (`jaeger`): Distributed tracing (ports 16686, 14268)
- **Prometheus** (`prometheus`): Metrics collection (port 9090)
- **Worker** (`worker`): Background job processing

## Configuration

### Environment Variables
- Copy `env.example` to `.env.local` for local overrides
- Key configurations:
  - Database connection: `DB_DSN`
  - Redis connection: `REDIS_DSN`
  - MinIO credentials: `OBJECT_STORE_*`
  - JWT secrets: `JWT_SECRET_KEY`, `JWT_PASSPHRASE`

### Docker Configuration
- **PHP**: Custom Dockerfile with extensions and optimizations
- **Nginx**: Optimized configuration with rate limiting and security headers
- **PostgreSQL**: Initialized with audit schema and sample data
- **Prometheus**: Configured to monitor all services

## Common Commands

### Service Management
```bash
make start          # Start all services
make stop           # Stop all services
make restart        # Restart all services
make logs           # View service logs
make clean          # Clean up containers and volumes
```

### Development
```bash
make setup          # Complete initial setup
make test           # Run tests
make migrate        # Run database migrations
make fixtures       # Load test data
make worker         # Start background workers
```

### Monitoring
```bash
make health         # Check service health
make monitoring     # Open monitoring interfaces
```

## Troubleshooting

### Common Issues

1. **Port conflicts:**
   - Check if ports 8080, 5432, 6379, 9000, 9001, 16686, 9090 are available
   - Modify ports in `docker-compose.yml` if needed

2. **Permission issues:**
   - Ensure Docker has access to the project directory
   - Run `chmod -R 777 var/` if needed

3. **Memory issues:**
   - Increase Docker Desktop memory allocation
   - Check `docker stats` for container resource usage

4. **Database connection issues:**
   - Wait for PostgreSQL to fully start (check logs)
   - Verify database credentials in `.env`

### Logs and Debugging
```bash
# View specific service logs
docker-compose logs php
docker-compose logs nginx
docker-compose logs postgres

# Follow logs in real-time
docker-compose logs -f php

# Access container shell
docker-compose exec php sh
docker-compose exec postgres psql -U audit_user -d audit_service
```

### Health Checks
```bash
# API health
curl http://localhost:8080/api/health

# Detailed health
curl http://localhost:8080/api/health/detailed

# Metrics
curl http://localhost:8080/api/metrics
```

## Performance Tuning

### Resource Allocation
- **PHP-FPM**: Adjust `pm.max_children` in `docker/php/php.ini`
- **Nginx**: Modify worker processes in `docker/nginx/nginx.conf`
- **PostgreSQL**: Tune `shared_buffers` and `work_mem` for your workload

### Caching
- **Redis**: Configure memory limits and eviction policies
- **OPcache**: Optimize settings in `docker/php/php.ini`
- **Nginx**: Enable static file caching

## Security Considerations

- Change default passwords in `.env`
- Use strong JWT secrets
- Configure firewall rules for production
- Enable HTTPS in production
- Review and customize security headers in Nginx

## Production Deployment

1. **Environment:**
   ```bash
   make production
   ```

2. **Security:**
   - Use strong, unique passwords
   - Enable HTTPS
   - Configure firewall rules
   - Use secrets management

3. **Monitoring:**
   - Set up alerting in Prometheus
   - Configure log aggregation
   - Monitor resource usage

4. **Backup:**
   - Regular database backups
   - Volume snapshots
   - Configuration backups

## Support

For issues and questions:
1. Check the logs: `make logs`
2. Verify configuration: `docker-compose config`
3. Test connectivity: `make health`
4. Review this documentation
5. Check the main README.md for additional information
