#!/bin/bash

echo "🚀 Building Development Environment..."
echo "======================================"

# Check if we're in the backend directory
if [ ! -f "composer.json" ]; then
    echo "❌ Error: Please run this script from the backend directory"
    exit 1
fi

# Set development environment
export APP_ENV=dev

echo "📦 Installing development dependencies..."
composer install

echo "🗄️ Setting up development environment..."
if [ ! -f ".env.local" ]; then
    echo "📝 Creating .env.local from development template..."
    cp env.dev .env.local
    echo "✅ .env.local created. Please update with your actual credentials."
else
    echo "ℹ️  .env.local already exists. Skipping creation."
fi

echo "🔑 Generating JWT keys for development..."
if [ ! -d "config/jwt" ]; then
    mkdir -p config/jwt
fi

# Generate JWT keys if they don't exist
if [ ! -f "config/jwt/private.pem" ] || [ ! -f "config/jwt/public.pem" ]; then
    php bin/console lexik:jwt:generate-keypair --overwrite
    echo "✅ JWT keys generated."
else
    echo "ℹ️  JWT keys already exist. Skipping generation."
fi

echo "🗃️ Setting up Tulsa SEO database with smcnary user..."
# Start database if using Docker
if command -v docker-compose &> /dev/null; then
    echo "🐳 Starting Tulsa SEO database with Docker..."
    docker-compose up -d database
    
    # Wait for database to be ready
    echo "⏳ Waiting for Tulsa SEO database to be ready..."
    sleep 10
    
    echo "👤 Setting up smcnary user..."
    ./setup-db-user.sh
fi

echo "🔄 Clearing development cache..."
php bin/console cache:clear --env=dev

echo "🔥 Warming up development cache..."
php bin/console cache:warmup --env=dev

echo "📊 Running database migrations..."
php bin/console doctrine:migrations:migrate --env=dev --no-interaction

echo "✅ Development environment build complete!"
echo ""
echo "🚀 To start the development server:"
echo "   php bin/console server:start 0.0.0.0:8000"
echo ""
echo "🔍 To test the API:"
echo "   curl http://localhost:8000/api/v1/health"
echo ""
echo "📝 Remember to update .env.local with your actual credentials!"
