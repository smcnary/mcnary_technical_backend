#!/bin/bash

echo "🚀 Manual Backend Deployment to Staging"
echo "========================================"

# Build the backend
cd backend
echo "📦 Building backend..."
./build-prod.sh

# Create deployment package
echo "📦 Creating deployment package..."
tar -czf ../backend-deployment.tar.gz \
    --exclude='.git' \
    --exclude='tests' \
    --exclude='var/cache/*' \
    --exclude='var/log/*' \
    --exclude='.env.local' \
    --exclude='.env.test' \
    --exclude='vendor/*/tests' \
    --exclude='*.tmp' \
    --exclude='*.log' \
    .

cd ..

# Upload to S3
echo "☁️ Uploading to S3..."
aws s3 cp backend-deployment.tar.gz s3://mcnary-tech-backend-production/staging/backend-deployment.tar.gz

echo "✅ Deployment package uploaded to S3"
echo "📋 Next steps:"
echo "   1. Connect to staging server: ssh -i ~/.ssh/mcnary_technical_backend.pem ubuntu@52.23.174.88"
echo "   2. Download package: aws s3 cp s3://mcnary-tech-backend-production/staging/backend-deployment.tar.gz ."
echo "   3. Extract: tar -xzf backend-deployment.tar.gz"
echo "   4. Install: composer install --no-dev --optimize-autoloader"
echo "   5. Setup: php bin/console cache:clear --env=prod"
echo "   6. Configure nginx to serve from /var/www/tulsa-seo-backend/public"
