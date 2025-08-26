#!/bin/bash

echo "🚀 Building Production Environment for Testing..."
echo "================================================"

# Check if we're in the backend directory
if [ ! -f "composer.json" ]; then
    echo "❌ Error: Please run this script from the backend directory"
    exit 1
fi

# Set production environment
export APP_ENV=prod

echo "📦 Installing production dependencies (no dev dependencies)..."
composer install --no-dev --optimize-autoloader

echo "🗄️ Setting up production environment..."
if [ ! -f ".env.local" ]; then
    echo "📝 Creating .env.local from production template..."
    cp env.prod .env.local
    echo "✅ .env.local created. Please update with your actual production credentials."
    echo "⚠️  IMPORTANT: Update database credentials and secrets before proceeding!"
    exit 1
else
    echo "ℹ️  .env.local already exists. Checking configuration..."
fi

echo "🔑 Generating JWT keys for production..."
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

echo "🗃️ Setting up Tulsa SEO production database..."
echo "⚠️  Make sure your Tulsa SEO production database is accessible!"

echo "🔄 Clearing production cache..."
php bin/console cache:clear --env=prod

echo "🔥 Warming up production cache..."
php bin/console cache:warmup --env=prod

echo "📊 Running database migrations..."
php bin/console doctrine:migrations:migrate --env=prod --no-interaction

echo "🔒 Setting production file permissions..."
chmod -R 755 var/cache
chmod -R 755 var/log
chmod -R 600 config/jwt

echo "✅ Production environment build complete!"
echo ""
echo "🚀 To start the production server:"
echo "   APP_ENV=prod php bin/console server:start 0.0.0.0:8000"
echo ""
echo "🔍 To test the production API:"
echo "   curl http://localhost:8000/api/v1/health"
echo ""
echo "⚠️  REMINDERS:"
echo "   - Update .env.local with real production credentials"
echo "   - Change APP_SECRET to a secure random string"
echo "   - Update JWT_PASSPHRASE"
echo "   - Ensure database is accessible"
echo "   - Test all API endpoints thoroughly"
