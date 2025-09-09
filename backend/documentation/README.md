# CounselRank.legal Documentation

## 📚 Complete Documentation Hub

Welcome to the comprehensive documentation for CounselRank.legal - a multi-tenant SEO and marketing platform for legal professionals.

## 🚀 Quick Start

### For New Developers
1. **[Setup Guide](./SETUP_GUIDE.md)** - Complete development environment setup
2. **[Architecture](./ARCHITECTURE.md)** - System design and principles
3. **[API Documentation](./API_DOCUMENTATION.md)** - Complete API reference

### For Frontend Developers
1. **[Setup Guide](./SETUP_GUIDE.md)** - Development environment setup
2. **[API Documentation](./API_DOCUMENTATION.md)** - API endpoints and examples
3. **[Authentication Guide](./AUTHENTICATION_GUIDE.md)** - Authentication system

### For Backend Developers
1. **[Setup Guide](./SETUP_GUIDE.md)** - Development environment setup
2. **[Database Guide](./DATABASE_GUIDE.md)** - Database setup and management
3. **[API Documentation](./API_DOCUMENTATION.md)** - API development
4. **[Authentication Guide](./AUTHENTICATION_GUIDE.md)** - Authentication system

### For DevOps/Deployment
1. **[Setup Guide](./SETUP_GUIDE.md)** - Environment configuration
2. **[Deployment Guide](./DEPLOYMENT_GUIDE.md)** - Production deployment
3. **[Database Guide](./DATABASE_GUIDE.md)** - Database management

## 📖 Core Documentation

### 🏗️ **Setup & Development**
- **[Setup Guide](./SETUP_GUIDE.md)** - Complete development, staging, and production setup
- **[Architecture](./ARCHITECTURE.md)** - System architecture and design principles

### 🔌 **API & Integration**
- **[API Documentation](./API_DOCUMENTATION.md)** - Complete REST API reference with examples
- **[Authentication Guide](./AUTHENTICATION_GUIDE.md)** - Complete authentication system
- **[OAuth Setup](./OAUTH_SETUP.md)** - OAuth provider configuration (Google, Microsoft)

### 🗄️ **Database & Data**
- **[Database Guide](./DATABASE_GUIDE.md)** - Database setup, entity creation, and management
- **[Database Schema](./DATABASE_SCHEMA.md)** - Complete database schema reference
- **[Entity Relationship Diagram](./ENTITY_RELATIONSHIP_DIAGRAM.md)** - Visual entity relationships

### 🚀 **Deployment & Operations**
- **[Deployment Guide](./DEPLOYMENT_GUIDE.md)** - Complete deployment guide (local, RDS, production)

## 📋 Reference Documentation

### 🔧 **Technical Specifications**
- **[Technical Specification](./TECHNICAL_SPECIFICATION.md)** - Detailed technical specifications
- **[New Role and Tenancy System](./NEW_ROLE_AND_TENANCY_SYSTEM.md)** - Role system implementation

### 🎯 **Specialized Features**
- **[OAuth Setup](./OAUTH_SETUP.md)** - OAuth provider configuration
- **[Error Handling Improvements](./ERROR_HANDLING_IMPROVEMENTS.md)** - Error handling best practices
- **[Audit Intake Integration](./AUDIT_INTAKE_INTEGRATION.md)** - Audit system integration
- **[Audit Intake Validation](./AUDIT_INTAKE_VALIDATION.md)** - Audit validation system

## 🗂️ Documentation Structure

### **Core Documentation (8 files)**
Essential guides for getting started and daily development:

1. **[Setup Guide](./SETUP_GUIDE.md)** - Complete development setup
2. **[Architecture](./ARCHITECTURE.md)** - System architecture
3. **[API Documentation](./API_DOCUMENTATION.md)** - Complete API reference
4. **[Authentication Guide](./AUTHENTICATION_GUIDE.md)** - Complete auth system
5. **[Database Guide](./DATABASE_GUIDE.md)** - Database setup and management
6. **[Deployment Guide](./DEPLOYMENT_GUIDE.md)** - Complete deployment guide

### **Reference Documentation (4 files)**
Detailed references for specific topics:

7. **[Database Schema](./DATABASE_SCHEMA.md)** - Database schema reference
8. **[Entity Relationship Diagram](./ENTITY_RELATIONSHIP_DIAGRAM.md)** - Entity relationships
9. **[Technical Specification](./TECHNICAL_SPECIFICATION.md)** - Technical specs
10. **[New Role and Tenancy System](./NEW_ROLE_AND_TENANCY_SYSTEM.md)** - Role system details

### **Specialized Documentation (4 files)**
Advanced topics and specialized features:

11. **[OAuth Setup](./OAUTH_SETUP.md)** - OAuth provider configuration
12. **[Error Handling Improvements](./ERROR_HANDLING_IMPROVEMENTS.md)** - Error handling
13. **[Audit Intake Integration](./AUDIT_INTAKE_INTEGRATION.md)** - Audit system
14. **[Audit Intake Validation](./AUDIT_INTAKE_VALIDATION.md)** - Audit validation

## 🎯 Navigation Paths

### **New Developer Onboarding**
```
Setup Guide → Architecture → API Documentation → Authentication Guide
```

### **Frontend Development**
```
Setup Guide → API Documentation → Authentication Guide → OAuth Setup
```

### **Backend Development**
```
Setup Guide → Database Guide → API Documentation → Authentication Guide
```

### **DevOps/Deployment**
```
Setup Guide → Deployment Guide → Database Guide → Technical Specification
```

### **API Integration**
```
API Documentation → Authentication Guide → OAuth Setup → Error Handling
```

## 🔍 Quick Reference

### **Common Tasks**

#### Start Development Environment
```bash
# See Setup Guide for complete instructions
cd backend
./scripts/setup-env.sh --dev
./build-dev.sh
php bin/console server:start 0.0.0.0:8000
```

#### Deploy to Production
```bash
# See Deployment Guide for complete instructions
cd backend/scripts
./deploy-rds.sh --production
./setup-env.sh --prod
```

#### Test API Endpoints
```bash
# See API Documentation for complete reference
curl -X POST "http://localhost:8000/api/v1/auth/login" \
  -H "Content-Type: application/json" \
  -d '{"email": "test@example.com", "password": "password123"}'
```

#### Database Operations
```bash
# See Database Guide for complete reference
php bin/console doctrine:migrations:migrate
php bin/console doctrine:schema:validate
```

### **Key URLs**
- **Frontend**: http://localhost:3000
- **Backend API**: http://localhost:8000
- **API Documentation**: http://localhost:8000/api
- **Health Check**: http://localhost:8000/api/v1/health

## 📊 Documentation Metrics

### **Current Status**
- **Total Files**: 14 documentation files
- **Categories**: 3 main categories (Core, Reference, Specialized)
- **Coverage**: Complete coverage of all major system components
- **Last Updated**: September 9, 2025

### **Coverage Areas**
- ✅ **Getting Started** - Complete
- ✅ **Backend API** - Complete
- ✅ **Database** - Complete
- ✅ **Authentication** - Complete
- ✅ **Deployment** - Complete
- ✅ **OAuth Integration** - Complete
- ✅ **Error Handling** - Complete
- ✅ **Audit System** - Complete

## 🆘 Support & Troubleshooting

### **Common Issues**
1. **Setup Problems** → Check [Setup Guide](./SETUP_GUIDE.md) troubleshooting section
2. **API Issues** → Check [API Documentation](./API_DOCUMENTATION.md) error handling
3. **Database Issues** → Check [Database Guide](./DATABASE_GUIDE.md) troubleshooting
4. **Authentication Issues** → Check [Authentication Guide](./AUTHENTICATION_GUIDE.md)
5. **Deployment Issues** → Check [Deployment Guide](./DEPLOYMENT_GUIDE.md)

### **Getting Help**
1. Check the relevant documentation section
2. Review troubleshooting guides
3. Check Symfony logs in `var/log/`
4. Verify environment configuration
5. Contact the development team

## 🔄 Documentation Maintenance

### **When Adding New Features**
1. Update relevant documentation files
2. Add new endpoints to API Documentation
3. Update database schema if needed
4. Add new environment variables to Setup Guide
5. Update this README if structure changes

### **When Updating Existing Features**
1. Update the relevant documentation file
2. Ensure cross-references remain valid
3. Update examples and code snippets
4. Test all documentation links

## 📈 Recent Updates

### **September 9, 2025**
- ✅ Consolidated 30 documentation files into 14 organized files
- ✅ Created comprehensive Setup Guide covering all environments
- ✅ Consolidated authentication documentation into single guide
- ✅ Merged API documentation with examples
- ✅ Updated database guide with RDS support
- ✅ Created OAuth setup guide for Google and Microsoft
- ✅ Organized documentation into logical categories

## 🔗 External Resources

- **[Symfony Documentation](https://symfony.com/doc/current/index.html)**
- **[API Platform Documentation](https://api-platform.com/docs/)**
- **[Doctrine ORM Documentation](https://www.doctrine-project.org/projects/orm.html)**
- **[PostgreSQL Documentation](https://www.postgresql.org/docs/)**
- **[AWS RDS Documentation](https://docs.aws.amazon.com/rds/)**

---

**Last Updated:** September 9, 2025  
**Maintained By:** Development Team  
**Status:** Complete and well-organized ✅

*This documentation is continuously updated to reflect the current state of the CounselRank.legal platform.*
