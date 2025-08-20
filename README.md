# CounselRank.legal Platform

A comprehensive digital marketing platform for law firms, built with modern web technologies and a robust API-first architecture.

## 🚀 Quick Start

```bash
# Clone the repository
git clone <repository-url>
cd mcnary_technical_backend

# Start the development environment
./dev-start.sh
```

## 📚 Documentation

**All documentation has been consolidated into the `backend/documentation/` folder.**

- **[📖 Documentation Index](backend/documentation/README.md)** - Complete documentation overview
- **[🚀 Quick Start Guide](backend/documentation/QUICK_START.md)** - Get up and running fast
- **[🏗️ Architecture Overview](backend/documentation/ARCHITECTURE.md)** - System design and principles
- **[🔌 API Documentation](backend/documentation/API_REFERENCE.md)** - Complete REST API v1 reference
- **[🎨 Frontend Setup](backend/documentation/FRONTEND_SETUP.md)** - Frontend development guide
- **[🐛 Error Handling](backend/documentation/ERROR_HANDLING_IMPROVEMENTS.md)** - Error handling and logging guide

## 🏗️ Project Structure

```
mcnary_technical_backend/
├── backend/                    # Symfony 7.3 + API Platform backend
│   ├── documentation/          # 📚 All project documentation (consolidated)
│   │   ├── README.md          # Documentation index
│   │   ├── QUICK_START.md     # Getting started guide
│   │   ├── ARCHITECTURE.md    # System architecture
│   │   ├── API_REFERENCE.md   # API documentation
│   │   ├── FRONTEND_*.md      # Frontend guides
│   │   ├── ERROR_HANDLING_IMPROVEMENTS.md # Error handling guide
│   │   └── ...                # Additional documentation
│   ├── src/                   # PHP source code
│   ├── config/                # Symfony configuration
│   └── migrations/            # Database migrations
├── frontend/                  # React 18 + TypeScript frontend
└── dev-start.sh              # Development environment startup
```

## 🔧 Technology Stack

- **Backend**: PHP 8.2+, Symfony 7.3, API Platform 4.x, PostgreSQL 16
- **Frontend**: React 18.2, TypeScript 5.3, Vite 6.3, TailwindCSS
- **Security**: JWT Authentication, Role-Based Access Control (RBAC)
- **Architecture**: Multi-tenant, API-first, UUID primary keys, JSONB metadata
- **Logging**: Comprehensive error handling and logging with Monolog

## 🎯 Features

- ✅ **Complete REST API v1** with authentication and authorization
- ✅ **Role-Based Access Control** for agency and client users
- ✅ **Multi-tenant architecture** with client scoping
- ✅ **Public content management** (pages, FAQs, packages, media)
- ✅ **User and client management** with proper security
- ✅ **Comprehensive documentation** for all components
- ✅ **Enhanced error handling and logging** throughout the system

## 📖 Getting Started

1. **Read the [Quick Start Guide](backend/documentation/QUICK_START.md)**
2. **Review the [Architecture Overview](backend/documentation/ARCHITECTURE.md)**
3. **Set up your [Development Environment](backend/documentation/QUICK_START.md)**
4. **Explore the [API Documentation](backend/documentation/API_REFERENCE.md)**
5. **Check [Frontend Setup](backend/documentation/FRONTEND_SETUP.md)** for frontend development
6. **Review [Error Handling](backend/documentation/ERROR_HANDLING_IMPROVEMENTS.md)** for best practices

## 🤝 Contributing

Please read our contributing guidelines and ensure all documentation is placed in the `backend/documentation/` folder.

## 📞 Support

For questions and support, please refer to the comprehensive documentation in the `backend/documentation/` folder.

---

**Status**: ✅ Core platform complete with full API v1 implementation and enhanced error handling  
**Version**: 1.0.0  
**Last Updated**: January 15, 2025
