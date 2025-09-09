# CounselRank SEO Audit Service

A comprehensive, multi-tenant SEO audit automation service built with Symfony 7.3, API Platform, and modern cloud-native architecture.

## 🚀 Features

- **Multi-tenant Architecture**: Agency → Client → Project → Audit hierarchy
- **Comprehensive SEO Checks**: Technical, content, authority, and UX analysis
- **Scalable Processing**: Async worker-based architecture with Symfony Messenger
- **External Integrations**: Google PageSpeed Insights, Search Console, backlink APIs
- **Rich Reporting**: HTML, PDF, CSV, and JSON exports
- **Workflow Management**: State machine for audit lifecycle
- **Security**: JWT authentication, RBAC, tenant isolation

## 🏗️ Architecture

```
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│   API Layer     │    │  Worker Layer   │    │  Storage Layer  │
│  (API Platform) │◄──►│ (Symfony Msgr)  │◄──►│ (PostgreSQL +   │
│                 │    │                 │    │   Redis + S3)   │
└─────────────────┘    └─────────────────┘    └─────────────────┘
         │                       │                       │
         ▼                       ▼                       ▼
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│   Web UI        │    │  External APIs  │    │  Object Store   │
│  (Swagger/ReDoc)│    │ (PSI, GSC, etc)│    │ (Screenshots,   │
│                 │    │                 │    │   HTML, PDFs)   │
└─────────────────┘    └─────────────────┘    └─────────────────┘
```

## 🛠️ Tech Stack

- **Backend**: Symfony 7.3 (PHP 8.2+)
- **API**: API Platform 4.1
- **Database**: PostgreSQL 16 with RLS
- **Cache/Queue**: Redis
- **Object Storage**: S3-compatible (MinIO)
- **Browser**: Headless Chrome
- **Messaging**: Symfony Messenger
- **Authentication**: JWT (Lexik)
- **Monitoring**: OpenTelemetry, Prometheus

## 📋 Prerequisites

- PHP 8.2+
- Docker & Docker Compose
- Composer
- Node.js 18+ (for development tools)

## 🚀 Quick Start

### 1. Clone and Setup

```bash
git clone <repository-url>
cd audit-service
cp env.example .env
# Edit .env with your configuration
```

### 2. Install Dependencies

```bash
composer install
```

### 3. Start Services

```bash
docker-compose up -d
```

### 4. Setup Database

```bash
# Wait for PostgreSQL to be ready
docker-compose exec php bin/console doctrine:database:create
docker-compose exec php bin/console doctrine:migrations:migrate
docker-compose exec php bin/console doctrine:fixtures:load
```

### 5. Generate JWT Keys

```bash
docker-compose exec php bin/console lexik:jwt:generate-keypair
```

### 6. Access the Application

- **API Documentation**: http://localhost:8080/api/docs
- **Admin Interface**: http://localhost:8080/admin
- **MinIO Console**: http://localhost:9001
- **Jaeger Tracing**: http://localhost:16686

## 🔧 Configuration

### Environment Variables

Key configuration options in `.env`:

```bash
# Database
DB_DSN="postgresql://user:pass@postgres:5432/audit_service"

# Redis
REDIS_DSN=redis://redis:6379

# Object Storage
OBJECT_STORE_ENDPOINT=http://minio:9000
OBJECT_STORE_BUCKET=audit-artifacts

# Google APIs
GSC_CLIENT_ID=your_client_id
GSC_CLIENT_SECRET=your_client_secret
PSI_API_KEY=your_api_key

# Audit Settings
MAX_CONCURRENCY=10
HTTP_TIMEOUT_MS=30000
SCREENSHOTS_ENABLED=true
```

### Multi-tenancy Setup

1. Create a tenant:
```bash
docker-compose exec php bin/console tenant:create "Agency Name"
```

2. Create a user:
```bash
docker-compose exec php bin/console user:create "admin@agency.com" "password" "ROLE_AGENCY_ADMIN"
```

3. Create a client:
```bash
docker-compose exec php bin/console client:create "Client Name" "tenant_id"
```

## 📊 Usage

### Running Audits

#### Via API

```bash
# Create an audit
curl -X POST "http://localhost:8080/api/audits" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "project": "/api/projects/1",
    "label": "Monthly SEO Audit",
    "config": {
      "maxPages": 1000,
      "sampleForLighthouse": 30,
      "focusKeywords": ["seo", "optimization"]
    }
  }'
```

#### Via CLI

```bash
# Run an audit
docker-compose exec php bin/console audit:run {audit_id}

# With options
docker-compose exec php bin/console audit:run {audit_id} \
  --max-pages=2000 \
  --sample-lighthouse=50

# Check status
docker-compose exec php bin/console audit:status {audit_id}
```

### Worker Management

```bash
# Start all workers
docker-compose exec php bin/console messenger:consume crawl lighthouse analyze aggregate report notify

# Start specific worker
docker-compose exec php bin/console messenger:consume crawl

# Check queue status
docker-compose exec php bin/console messenger:stats
```

## 🔍 Monitoring & Debugging

### Logs

```bash
# Application logs
docker-compose logs php

# Worker logs
docker-compose logs worker

# Specific service
docker-compose logs postgres
```

### Metrics

- **Prometheus**: http://localhost:9090
- **Jaeger**: http://localhost:16686
- **Redis**: http://localhost:6379

### Health Checks

```bash
# API health
curl http://localhost:8080/api/health

# Database connection
docker-compose exec php bin/console doctrine:query:sql "SELECT 1"
```

## 🧪 Testing

### Unit Tests

```bash
docker-compose exec php vendor/bin/phpunit --testsuite=unit
```

### Integration Tests

```bash
docker-compose exec php vendor/bin/phpunit --testsuite=integration
```

### E2E Tests

```bash
docker-compose exec php vendor/bin/phpunit --testsuite=e2e
```

## 📚 API Reference

### Core Endpoints

- `POST /api/audits` - Create/trigger audit
- `GET /api/audit-runs` - List audit runs
- `GET /api/audit-runs/{id}` - Get run details
- `GET /api/audit-runs/{id}/findings` - Get findings
- `GET /api/reports/{id}` - Download report

### Authentication

All API endpoints require JWT authentication:

```bash
# Login
curl -X POST "http://localhost:8080/api/auth/login" \
  -H "Content-Type: application/json" \
  -d '{"email": "user@example.com", "password": "password"}'

# Use token
curl -H "Authorization: Bearer YOUR_JWT_TOKEN" \
  "http://localhost:8080/api/audits"
```

## 🚀 Deployment

### Production

1. Update `.env` with production values
2. Set `APP_ENV=prod`
3. Configure SSL/TLS
4. Set up monitoring and alerting
5. Configure backup strategies

### Kubernetes

```bash
# Apply manifests
kubectl apply -f k8s/

# Scale workers
kubectl scale deployment audit-worker --replicas=5
```

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests
5. Submit a pull request

## 📄 License

Proprietary - All rights reserved

## 🆘 Support

- **Documentation**: [Technical Specification](TECHNICAL_SPECIFICATION.md)
- **Issues**: GitHub Issues
- **Email**: support@counselrank.com

## 🔄 Changelog

### v1.0.0 (Planned)
- Core audit functionality
- Multi-tenant support
- Basic SEO checks
- HTML/PDF reporting

### v1.1.0 (Planned)
- Google Search Console integration
- Advanced backlink analysis
- Custom check authoring
- Webhook system

---

**Built with ❤️ by the CounselRank Backend Platform Team**
