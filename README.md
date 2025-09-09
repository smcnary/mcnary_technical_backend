# McNary Technical Backend - Project Organization

## Overview
This repository contains a multi-service SEO audit platform with three main components:
- **Backend**: Symfony API with authentication, billing, and core business logic
- **Frontend**: Next.js React application for client dashboard and marketing site  
- **Audit Service**: Dedicated Symfony microservice for crawling, auditing, and scoring

## Project Structure

```
mcnary_technical_backend/
├── docs/                           # Centralized documentation
│   ├── architecture/               # System architecture docs
│   ├── deployment/                # Deployment guides
│   ├── development/                # Development setup guides
│   └── api/                        # API documentation
├── scripts/                        # Shared utility scripts
│   ├── deployment/                 # Deployment scripts
│   ├── database/                   # Database management
│   └── development/                # Development utilities
├── config/                         # Shared configuration
│   ├── environments/               # Environment templates
│   └── docker/                     # Docker configurations
├── backend/                        # Main Symfony API
├── frontend/                       # Next.js React application
├── audit-service/                  # SEO audit microservice
└── README.md                       # This file
```

## Services

### Backend (`/backend`)
- **Purpose**: Main API with authentication, billing, multi-tenancy
- **Tech Stack**: Symfony 7.3, API Platform 4.1, PostgreSQL, JWT
- **Key Features**: User management, agency/client hierarchy, billing integration

### Frontend (`/frontend`) 
- **Purpose**: Client dashboard and marketing website
- **Tech Stack**: Next.js 14, React 18, Tailwind CSS, TypeScript
- **Key Features**: Audit wizard, client dashboard, admin portal

### Audit Service (`/audit-service`)
- **Purpose**: Dedicated SEO audit processing
- **Tech Stack**: Symfony 7.3, Symfony Messenger, Headless Chrome
- **Key Features**: Web crawling, SEO analysis, report generation

## Development

### Prerequisites
- PHP 8.2+
- Node.js 18+
- PostgreSQL 16
- Docker & Docker Compose

### Quick Start
```bash
# Start all services
./scripts/development/start-dev.sh

# Or start individually
cd backend && composer install && symfony serve
cd frontend && npm install && npm run dev
cd audit-service && composer install && symfony serve
```

### Environment Setup
1. Copy environment templates from `config/environments/`
2. Configure database connections
3. Set up JWT secrets and API keys
4. Run migrations: `./scripts/database/migrate.sh`

## Documentation
- [Architecture Overview](docs/architecture/README.md)
- [API Reference](docs/api/README.md)
- [Deployment Guide](docs/deployment/README.md)
- [Development Setup](docs/development/README.md)

## Contributing
1. Follow the established directory structure
2. Update documentation for new features
3. Ensure tests pass before submitting PRs
4. Use conventional commit messages
