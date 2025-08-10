# McNary Technical Backend

A Symfony-based backend application for SEO database management with multi-tenancy support, built with modern PHP practices and Docker containerization.

## 🚀 Features

- **SEO Database Management** - Comprehensive entities for forms, pages, posts, and SEO metadata
- **Multi-Tenancy Support** - Built-in tenant isolation and management
- **RESTful API** - Powered by API Platform for GraphQL and REST endpoints
- **JWT Authentication** - Secure authentication with Lexik JWT Bundle
- **PostgreSQL Database** - Robust database backend with Docker containerization
- **Modern Symfony 7** - Latest Symfony framework with best practices

## 🏗️ Architecture

### Core Entities
- **Form & FormSubmission** - Dynamic form creation and data collection
- **Page & Post** - Content management with SEO optimization
- **SeoMeta** - Comprehensive SEO metadata management
- **Site & Tenant** - Multi-tenant site management
- **User** - User authentication and management

### Technology Stack
- **Backend Framework:** Symfony 7
- **Database:** PostgreSQL 16
- **ORM:** Doctrine ORM with migrations
- **API:** API Platform (REST/GraphQL)
- **Authentication:** JWT with Lexik JWT Bundle
- **Containerization:** Docker & Docker Compose
- **CORS:** Nelmio CORS Bundle

## 📋 Prerequisites

- PHP 8.2+
- Composer
- Docker & Docker Compose
- Git

## 🛠️ Installation

1. **Clone the repository:**
   ```bash
   git clone <your-github-repo-url>
   cd mcnary_technical_backend
   ```

2. **Install dependencies:**
   ```bash
   composer install
   ```

3. **Start the database:**
   ```bash
   docker-compose up -d
   ```

4. **Configure database connection:**
   ```bash
   source connect-db.sh
   ```

5. **Run database migrations:**
   ```bash
   bin/console doctrine:migrations:migrate
   ```

## 🗄️ Database Setup

The application uses PostgreSQL running in Docker. See [DATABASE_SETUP.md](DATABASE_SETUP.md) for detailed configuration.

### Quick Database Connection
```bash
# Start database
docker-compose up -d

# Connect to database
source connect-db.sh

# Check status
bin/console doctrine:migrations:status
```

## 🚀 Usage

### Development Server
```bash
# Start Symfony development server
symfony server:start

# Or use PHP built-in server
php -S localhost:8000 -t public/
```

### API Endpoints
- **REST API:** `/api`
- **GraphQL:** `/api/graphql`
- **Documentation:** `/api/docs`

### Console Commands
```bash
# Database operations
bin/console doctrine:migrations:migrate
bin/console doctrine:schema:validate

# Cache operations
bin/console cache:clear
bin/console cache:warmup

# User management
bin/console app:create-user
```

## 🔧 Configuration

### Environment Variables
- `APP_ENV` - Application environment (dev, prod, test)
- `DATABASE_URL` - PostgreSQL connection string
- `JWT_SECRET_KEY` - JWT private key path
- `JWT_PUBLIC_KEY` - JWT public key path
- `JWT_PASSPHRASE` - JWT key passphrase

### Docker Configuration
- PostgreSQL 16 Alpine image
- Persistent volume storage
- Health checks enabled
- Port 5433 exposed (avoiding conflicts)

## 🧪 Testing

```bash
# Run tests
bin/phpunit

# Run with coverage
bin/phpunit --coverage-html coverage/
```

## 📚 API Documentation

The API is self-documenting through API Platform. Visit `/api/docs` after starting the application to explore available endpoints and test them interactively.

## 🔒 Security

- JWT-based authentication
- CORS configuration for frontend integration
- Multi-tenant data isolation
- Environment-based configuration

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## 📄 License

This project is proprietary software. All rights reserved.

## 🆘 Support

For support and questions, please contact the development team or create an issue in the repository.

---

**Built with ❤️ using Symfony 7 and modern PHP practices**
