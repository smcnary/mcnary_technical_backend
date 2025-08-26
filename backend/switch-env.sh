#!/bin/bash

echo "🔄 Environment Switcher"
echo "======================="

if [ $# -eq 0 ]; then
    echo "Usage: $0 [dev|prod]"
    echo ""
    echo "Examples:"
    echo "  $0 dev    - Switch to development environment"
    echo "  $0 prod   - Switch to production environment"
    exit 1
fi

ENV=$1

case $ENV in
    "dev"|"development")
        echo "🔄 Switching to DEVELOPMENT environment..."
        
        if [ -f "env.dev" ]; then
            cp env.dev .env.local
            echo "✅ Environment switched to development"
            echo "📝 .env.local updated with development settings"
        else
            echo "❌ Error: env.dev file not found"
            exit 1
        fi
        
        echo ""
        echo "🚀 To start development server:"
        echo "   ./build-dev.sh"
        echo "   php bin/console server:start 0.0.0.0:8000"
        ;;
        
    "prod"|"production")
        echo "🔄 Switching to PRODUCTION environment..."
        
        if [ -f "env.prod" ]; then
            cp env.prod .env.local
            echo "✅ Environment switched to production"
            echo "📝 .env.local updated with production settings"
        else
            echo "❌ Error: env.prod file not found"
            exit 1
        fi
        
        echo ""
        echo "⚠️  IMPORTANT: Update .env.local with real production credentials!"
        echo "🚀 To build production environment:"
        echo "   ./build-prod.sh"
        echo "   APP_ENV=prod php bin/console server:start 0.0.0.0:8000"
        ;;
        
    "cloud")
        echo "☁️  Switching to CLOUD environment..."
        
        if [ -f "env.cloud" ]; then
            cp env.cloud .env.local
            echo "✅ Environment switched to cloud"
            echo "📝 .env.local updated with cloud settings"
        else
            echo "❌ Error: env.cloud file not found"
            echo "💡 Run ./migrate-to-cloud.sh first to create cloud environment"
            exit 1
        fi
        
        echo ""
        echo "🚀 To test cloud environment:"
        echo "   APP_ENV=prod php bin/console server:start 0.0.0.0:8000"
        echo "   curl http://localhost:8000/api/v1/health"
        ;;
        
    *)
        echo "❌ Error: Invalid environment '$ENV'"
        echo "Valid options: dev, prod, cloud"
        exit 1
        ;;
esac

echo ""
echo "🔍 Current environment variables:"
echo "   APP_ENV: $(grep '^APP_ENV=' .env.local | cut -d'=' -f2 2>/dev/null || echo 'not set')"
echo "   APP_DEBUG: $(grep '^APP_DEBUG=' .env.local | cut -d'=' -f2 2>/dev/null || echo 'not set')"
