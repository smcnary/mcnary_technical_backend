# System Architecture

## Overview

The McNary Technical Backend is a multi-service SEO audit platform built with modern, scalable architecture principles. The system follows a microservices pattern with clear separation of concerns and API-first design.

## Architecture Diagram

```
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│    Frontend     │    │     Backend     │    │  Audit Service  │
│   (Next.js)     │◄──►│   (Symfony)     │◄──►│   (Symfony)     │
│                 │    │                 │    │                 │
│ • React 18      │    │ • API Platform  │    │ • Web Crawler   │
│ • TypeScript    │    │ • JWT Auth      │    │ • SEO Analysis  │
│ • Tailwind CSS  │    │ • Multi-tenancy │    │ • Report Gen    │
└─────────────────┘    └─────────────────┘    └─────────────────┘
         │                       │                       │
         ▼                       ▼                       ▼
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│   Static Assets │    │   PostgreSQL    │    │   Redis Queue   │
│   (CDN/S3)      │    │   Database      │    │   (Messenger)  │
└─────────────────┘    └─────────────────┘    └─────────────────┘
```

## Service Architecture

### Frontend Service (`/frontend`)
- **Technology**: Next.js 14, React 18, TypeScript
- **Purpose**: Client dashboard, admin portal, marketing site
- **Key Features**:
  - Audit wizard interface
  - Real-time dashboard
  - Multi-role access (admin/client)
  - Responsive design with Tailwind CSS

### Backend Service (`/backend`)
- **Technology**: Symfony 7.3, API Platform 4.1, PostgreSQL
- **Purpose**: Main API, authentication, business logic
- **Key Features**:
  - Multi-tenant architecture
  - JWT-based authentication
  - Role-based access control
  - Agency/client hierarchy management

### Audit Service (`/audit-service`)
- **Technology**: Symfony 7.3, Symfony Messenger, Headless Chrome
- **Purpose**: SEO audit processing, web crawling, analysis
- **Key Features**:
  - Async job processing
  - Web crawling and analysis
  - Report generation
  - Multi-tenant audit isolation

## Data Flow

### 1. User Authentication
```
Frontend → Backend API → JWT Token → Frontend Storage
```

### 2. Audit Request
```
Frontend → Backend API → Audit Service Queue → Processing → Results
```

### 3. Data Access
```
Frontend → Backend API → Database (with tenant filtering) → Response
```

## Multi-Tenancy Model

### Agency-Based Hierarchy
```
System Admin (no agency)
├── Agency A
│   ├── Agency Admin User
│   ├── Client 1
│   │   ├── Client User 1
│   │   └── Client User 2
│   └── Client 2
│       └── Client User 3
└── Agency B
    ├── Agency Admin User
    └── Client 3
        └── Client User 4
```

### Role-Based Access Control
- **ROLE_SYSTEM_ADMIN**: Full platform control
- **ROLE_AGENCY_ADMIN**: Agency-level management
- **ROLE_CLIENT_USER**: Client-specific access
- **ROLE_READ_ONLY**: Read-only auditor access

## Technology Stack

### Backend Services
- **Framework**: Symfony 7.3 (PHP 8.2+)
- **API**: API Platform 4.1 with OpenAPI
- **Database**: PostgreSQL 16 with Doctrine ORM
- **Authentication**: JWT via LexikJWTAuthenticationBundle
- **Queue**: Symfony Messenger with Redis
- **Containerization**: Docker with Docker Compose

### Frontend
- **Framework**: Next.js 14 with App Router
- **UI Library**: React 18 with TypeScript
- **Styling**: Tailwind CSS with shadcn/ui components
- **State Management**: React Context + Custom hooks
- **Testing**: Playwright for E2E testing

### Infrastructure
- **Development**: Docker Compose
- **Production**: AWS ECS/Fargate or Fly.io
- **Database**: AWS RDS PostgreSQL
- **Storage**: S3-compatible object storage
- **Monitoring**: Prometheus + Grafana

## Security Architecture

### Authentication Flow
1. User login via Backend API
2. JWT token generation with role claims
3. Token storage in frontend (httpOnly cookies)
4. API requests include Bearer token
5. Backend validates token and extracts user context

### Data Isolation
- **Database Level**: Row-level security with tenant_id filtering
- **API Level**: Automatic tenant filtering via API Platform extensions
- **Service Level**: Tenant context propagation through message queues

### Security Measures
- JWT tokens with short expiration
- HTTPS enforcement in production
- Input validation and sanitization
- Rate limiting on public endpoints
- CORS configuration for cross-origin requests

## Scalability Considerations

### Horizontal Scaling
- Stateless API services
- Load balancer distribution
- Database read replicas
- Redis cluster for message queues

### Performance Optimization
- Database query optimization
- API response caching
- CDN for static assets
- Async processing for heavy operations

### Monitoring & Observability
- Application metrics (Prometheus)
- Log aggregation (ELK stack)
- Error tracking and alerting
- Performance monitoring

## Development Workflow

### Local Development
1. Use `./scripts/development/start-dev.sh` to start all services
2. Services run on different ports:
   - Frontend: http://localhost:3000
   - Backend: http://localhost:8000
   - Audit Service: http://localhost:8001

### Environment Management
- Environment templates in `/config/environments/`
- Service-specific `.env` files
- Docker Compose for service orchestration

### Testing Strategy
- Unit tests for business logic
- Integration tests for API endpoints
- E2E tests for user workflows
- Load testing for performance validation

## Deployment Architecture

### Development
- Docker Compose for local development
- Hot reloading for frontend and backend
- Shared database and Redis instances

### Staging/Production
- Containerized services on cloud platform
- Managed database (AWS RDS)
- Load balancer with SSL termination
- CI/CD pipeline with automated testing

## Future Enhancements

### Planned Features
- Microservice decomposition
- Event-driven architecture
- Advanced caching strategies
- Real-time notifications
- Advanced analytics and reporting

### Scalability Improvements
- Service mesh implementation
- Advanced monitoring and alerting
- Automated scaling policies
- Multi-region deployment
