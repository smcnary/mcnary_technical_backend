#!/bin/bash

echo "🚀 Setting up McNary Tech Blog Repository"
echo "=========================================="

# Check if Node.js is installed
if ! command -v node &> /dev/null; then
    echo "❌ Node.js is not installed. Please install Node.js 18+ first."
    exit 1
fi

# Check Node.js version
NODE_VERSION=$(node -v | cut -d'v' -f2 | cut -d'.' -f1)
if [ "$NODE_VERSION" -lt 18 ]; then
    echo "❌ Node.js version 18+ is required. Current version: $(node -v)"
    exit 1
fi

echo "✅ Node.js $(node -v) detected"

# Install dependencies
echo "📦 Installing dependencies..."
npm install

if [ $? -eq 0 ]; then
    echo "✅ Dependencies installed successfully"
else
    echo "❌ Failed to install dependencies"
    exit 1
fi

# Copy environment file
if [ ! -f .env.local ]; then
    echo "🔧 Setting up environment file..."
    cp env.example .env.local
    echo "✅ Environment file created (.env.local)"
    echo "⚠️  Please update .env.local with your configuration"
else
    echo "✅ Environment file already exists"
fi

# Create .env.local if it doesn't exist
if [ ! -f .env.local ]; then
    echo "🔧 Creating .env.local..."
    cat > .env.local << EOF
NODE_ENV=development
API_URL=http://localhost:8000
DATABASE_URL=postgresql://user:password@localhost:5439/blog
REDIS_URL=redis://localhost:6379

# SEO
NEXT_PUBLIC_SITE_URL=http://localhost:3000
NEXT_PUBLIC_SITE_NAME="McNary Tech Blog"
NEXT_PUBLIC_SITE_DESCRIPTION="Technology insights and development tips"
EOF
    echo "✅ .env.local created with default values"
    echo "⚠️  Please update the values in .env.local"
fi

echo ""
echo "🎉 Setup complete!"
echo ""
echo "Next steps:"
echo "1. Update .env.local with your configuration"
echo "2. Run 'npm run dev' to start the development server"
echo "3. Open http://localhost:3000 in your browser"
echo ""
echo "Available commands:"
echo "  npm run dev          - Start development server"
echo "  npm run build        - Build for production"
echo "  npm run lint         - Run ESLint"
echo "  npm run type-check   - Run TypeScript type checking"
echo "  npm run format       - Format code with Prettier"
echo ""
echo "Happy coding! 🚀"
