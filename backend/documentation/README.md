# CounselRank.legal Documentation

Welcome to the comprehensive documentation for the CounselRank.legal platform. This folder contains all technical documentation, guides, and specifications for the project.

## 📚 Documentation Index

### **🚀 Getting Started**
- **[QUICK_START.md](./QUICK_START.md)** - Complete setup guide for new developers
- **[ARCHITECTURE.md](./ARCHITECTURE.md)** - Overall system architecture and design principles

### **🗄️ Database & Backend**
- **[DATABASE_GUIDE.md](./DATABASE_GUIDE.md)** - Database setup, connection, and entity management
- **[API_REFERENCE.md](./API_REFERENCE.md)** - Complete REST API documentation and RBAC system

### **🚀 Deployment & Operations**
- **[DEPLOYMENT_GUIDE.md](./DEPLOYMENT_GUIDE.md)** - Complete deployment guide for all environments
- **[TECHNICAL_SPECIFICATION.md](./TECHNICAL_SPECIFICATION.md)** - Detailed technical specifications

## 🚀 Quick Navigation

### **For New Developers**
1. **Start Here**: **[QUICK_START.md](./QUICK_START.md)** - Complete setup in one guide
2. **Understand Architecture**: **[ARCHITECTURE.md](./ARCHITECTURE.md)** - System overview

### **For Backend Development**
1. **[DATABASE_GUIDE.md](./DATABASE_GUIDE.md)** - Database setup, entities, and migrations
2. **[API_REFERENCE.md](./API_REFERENCE.md)** - API development and security

### **For DevOps & Deployment**
1. **[DEPLOYMENT_GUIDE.md](./DEPLOYMENT_GUIDE.md)** - Complete deployment process
2. **[TECHNICAL_SPECIFICATION.md](./TECHNICAL_SPECIFICATION.md)** - Infrastructure requirements

## 🔧 Technology Stack

### **Backend**
- **PHP 8.2+** with **Symfony 7.3**
- **API Platform 4.x** for REST API generation
- **Doctrine ORM 3.5** for database management
- **PostgreSQL 16** with UUIDs and JSONB
- **JWT Authentication** via Lexik JWT Bundle
- **Role-Based Access Control (RBAC)** system

### **Frontend**
- **React 18.2** with **TypeScript 5.3**
- **Vite 6.3** for build tooling
- **TailwindCSS** for styling
- **shadcn/ui** for component library

### **Infrastructure**
- **Docker Compose** for local development
- **Multi-tenancy** support via client_id scoping
- **CORS** enabled for cross-origin requests

## 📋 Recent Updates

### **Latest Features (v1.0)**
- ✅ **Complete REST API v1** implementation with 13 new entities
- ✅ **Role-Based Access Control** system
- ✅ **Multi-tenant architecture** with client scoping
- ✅ **UUID primary keys** and **JSONB metadata** fields
- ✅ **Consolidated documentation** for easier navigation

### **New API Endpoints Implemented**
- **Campaign Management** - Marketing campaigns with goals and metrics
- **SEO Tools** - Keywords, rankings, backlinks, and audits
- **Content Management** - Content items, briefs, and recommendations
- **Business Intelligence** - Reviews, citations, and client analytics
- **Billing System** - Subscriptions and invoices

## 🤝 Contributing

When adding new documentation:
1. Place all `.md` files in this `documentation/` folder
2. Update this `README.md` index file
3. Follow the consolidated documentation structure
4. Include code examples and practical usage

## 📞 Support

For questions about the documentation or platform:
- **Start with**: **[QUICK_START.md](./QUICK_START.md)** for common issues
- **Database issues**: **[DATABASE_GUIDE.md](./DATABASE_GUIDE.md)**
- **API development**: **[API_REFERENCE.md](./API_REFERENCE.md)**
- **Deployment**: **[DEPLOYMENT_GUIDE.md](./DEPLOYMENT_GUIDE.md)**

---

**Last Updated:** January 15, 2025  
**Version:** 1.0.0  
**Status:** All core documentation complete and consolidated ✅
