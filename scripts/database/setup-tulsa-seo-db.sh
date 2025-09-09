#!/bin/bash

echo "🗄️ Setting up Tulsa SEO Database..."
echo "===================================="

# Check if we're in the backend directory
if [ ! -f "composer.json" ]; then
    echo "❌ Error: Please run this script from the backend directory"
    exit 1
fi

echo "🐳 Starting Tulsa SEO database with Docker..."
docker-compose up -d database

echo "⏳ Waiting for Tulsa SEO database to be ready..."
sleep 15

echo "🔍 Testing database connection..."
if command -v psql &> /dev/null; then
    # Test connection to local database
    if psql -h 127.0.0.1 -p 5432 -U seanmcnary -d tulsa_seo -c "SELECT 1;" &> /dev/null; then
        echo "✅ Local Tulsa SEO database connection successful"
    else
        echo "⚠️  Local database connection failed. Trying Docker database..."
        # Test Docker database connection
        if psql -h 127.0.0.1 -p 5434 -U postgres -d tulsa_seo -c "SELECT 1;" &> /dev/null; then
            echo "✅ Docker Tulsa SEO database connection successful"
        else
            echo "❌ Both local and Docker database connections failed"
            echo "💡 Make sure PostgreSQL is running and accessible"
            exit 1
        fi
    fi
else
    echo "ℹ️  psql not available, skipping connection test"
fi

echo "📊 Running database migrations for Tulsa SEO..."
php bin/console doctrine:migrations:migrate --no-interaction

echo "✅ Tulsa SEO database setup complete!"
echo ""
echo "🔍 Database Information:"
echo "   - Database Name: tulsa_seo"
echo "   - Local Port: 5432 (if using local PostgreSQL)"
echo "   - Docker Port: 5434 (if using Docker)"
echo ""
echo "🚀 To start the server:"
echo "   ./build-dev.sh"
echo "   php bin/console server:start 0.0.0.0:8000"
