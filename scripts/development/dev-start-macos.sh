#!/bin/bash

# Development Startup Script for macOS
# This script starts both the backend (Symfony) and frontend (React) applications

set -e

echo "🚀 Starting McNary Technical Backend Development Environment (macOS)"
echo "=================================================================="

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Function to print colored output
print_status() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

print_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Check if required tools are installed
check_requirements() {
    print_status "Checking system requirements..."
    
    # Check PHP
    if ! command -v php &> /dev/null; then
        print_error "PHP is not installed. Please install PHP 8.2+"
        print_status "You can install it with: brew install php"
        exit 1
    fi
    
    # Check Composer
    if ! command -v composer &> /dev/null; then
        print_error "Composer is not installed. Please install Composer"
        print_status "You can install it with: brew install composer"
        exit 1
    fi
    
    # Check Node.js
    if ! command -v node &> /dev/null; then
        print_error "Node.js is not installed. Please install Node.js 18+"
        print_status "You can install it with: brew install node"
        exit 1
    fi
    
    # Check npm
    if ! command -v npm &> /dev/null; then
        print_error "npm is not installed. Please install npm"
        exit 1
    fi
    
    print_success "All requirements are met"
}

# Check if PostgreSQL is running
check_postgresql() {
    print_status "Checking PostgreSQL connection..."
    
    if command -v psql &> /dev/null; then
        if pg_isready -h localhost -p 5432 &> /dev/null; then
            print_success "PostgreSQL is running"
        else
            print_warning "PostgreSQL is not running. Please start PostgreSQL service"
            print_status "You can start it with: brew services start postgresql"
            print_status "Or manually: pg_ctl -D /usr/local/var/postgres start"
        fi
    else
        print_warning "PostgreSQL client not found. Please install postgresql"
        print_status "You can install it with: brew install postgresql"
    fi
}

# Setup backend environment
setup_backend() {
    print_status "Setting up backend environment..."
    
    cd backend
    
    # Check if .env.local exists
    if [ ! -f .env.local ]; then
        print_status "Creating .env.local from .env..."
        cp .env .env.local
        print_warning "Please edit .env.local with your database credentials"
        print_status "Key settings to update:"
        print_status "  - DATABASE_URL (PostgreSQL connection string)"
        print_status "  - APP_SECRET (random string for security)"
        print_status "  - JWT_PASSPHRASE (for JWT authentication)"
    fi
    
    # Install dependencies if vendor directory doesn't exist
    if [ ! -d "vendor" ]; then
        print_status "Installing PHP dependencies..."
        composer install
    fi
    
    # Check if JWT keys exist
    if [ ! -f "config/jwt/private.pem" ] || [ ! -f "config/jwt/public.pem" ]; then
        print_status "Generating JWT keys..."
        php bin/console lexik:jwt:generate-keypair --overwrite
    fi
    
    # Clear cache
    print_status "Clearing Symfony cache..."
    php bin/console cache:clear
    
    cd ..
}

# Setup frontend environment
setup_frontend() {
    print_status "Setting up frontend environment..."
    
    cd frontend
    
    # Install dependencies if node_modules doesn't exist
    if [ ! -d "node_modules" ]; then
        print_status "Installing Node.js dependencies..."
        npm install
    fi
    
    cd ..
}

# Start backend server
start_backend() {
    print_status "Starting backend server..."
    
    cd backend
    
    # If Symfony CLI exists, use it; otherwise fall back to PHP built-in server
    if command -v symfony &> /dev/null; then
        # Check if Symfony server is already running
        if symfony server:status &> /dev/null; then
            print_status "Stopping existing Symfony server..."
            symfony server:stop
        fi
        
        # Start Symfony server in background
        print_status "Starting Symfony server on http://localhost:8000..."
        symfony server:start -d --port=8000
    else
        # Kill anything on 8000
        if lsof -Pi :8000 -sTCP:LISTEN -t >/dev/null 2>&1; then
            print_warning "Port 8000 is already in use. Killing existing process..."
            lsof -ti:8000 | xargs kill -9 || true
        fi
        print_warning "Symfony CLI not found. Starting PHP built-in server on http://localhost:8000..."
        nohup php -S 127.0.0.1:8000 -t public > var/php-server.log 2>&1 &
        echo $! > var/php-server.pid
    fi
    
    cd ..
    
    # Wait a moment for server to start
    sleep 3
    
    # Check if server is responding
    if curl -s http://localhost:8000 > /dev/null; then
        print_success "Backend server is running on http://localhost:8000"
    else
        print_error "Backend server failed to start"
        exit 1
    fi
}

# Start frontend server
start_frontend() {
    print_status "Starting frontend server..."
    
    cd frontend
    
    # Next.js default port 3000
    if lsof -Pi :3000 -sTCP:LISTEN -t >/dev/null 2>&1; then
        print_warning "Port 3000 is already in use. Killing existing process..."
        lsof -ti:3000 | xargs kill -9
    fi
    
    print_status "Starting Next.js development server on http://localhost:3000..."
    
    # Start frontend in background
    npm run dev &
    FRONTEND_PID=$!
    
    cd ..
    
    # Wait a moment for server to start
    sleep 5
    
    # Check if server is responding
    if curl -s http://localhost:3000 > /dev/null; then
        print_success "Frontend server is running on http://localhost:3000"
    else
        print_warning "Frontend server may still be starting up..."
    fi
}

# Main execution
main() {
    echo ""
    print_status "Starting development environment setup..."
    
    # Check requirements
    check_requirements
    
    # Check PostgreSQL
    check_postgresql
    
    # Setup environments
    setup_backend
    setup_frontend
    
    # Start servers
    start_backend
    start_frontend
    
    echo ""
    echo "🎉 Development environment is starting up!"
    echo "=========================================="
    echo "📱 Frontend: http://localhost:3000"
    echo "🔧 Backend API: http://localhost:8000"
    echo "📚 API Documentation: http://localhost:8000/api"
    echo ""
    echo "💡 Tips:"
    echo "  - Backend auto-reloads with Symfony server"
    echo "  - Frontend auto-reloads with Vite HMR"
    echo "  - Use Ctrl+C to stop both servers"
    echo "  - Check logs in respective terminal windows"
    echo ""
    
    # Keep script running and handle cleanup
    trap 'cleanup' INT TERM
    
    # Wait for user to stop
    echo "Press Ctrl+C to stop both servers..."
    wait
}

# Cleanup function
cleanup() {
    echo ""
    print_status "Shutting down development environment..."
    
    # Stop Symfony server
    if [ -d "backend" ]; then
        cd backend
        if symfony server:status &> /dev/null; then
            symfony server:stop
            print_success "Backend server stopped"
        fi
        cd ..
    fi
    
    # Stop frontend server
    if lsof -Pi :5173 -sTCP:LISTEN -t >/dev/null 2>&1; then
        lsof -ti:5173 | xargs kill -9
        print_success "Frontend server stopped"
    fi
    
    echo ""
    print_success "Development environment stopped"
    exit 0
}

# Run main function
main "$@"
