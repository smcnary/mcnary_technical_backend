#!/bin/bash

# McNary Technical Backend - Development Startup Script
# This script starts all services for local development

set -e

echo "🚀 Starting McNary Technical Backend Development Environment"
echo "=============================================================="

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

# Check prerequisites
check_prerequisites() {
    print_status "Checking prerequisites..."
    
    if ! command -v php &> /dev/null; then
        print_error "PHP is not installed. Please install PHP 8.2+"
        exit 1
    fi
    
    if ! command -v composer &> /dev/null; then
        print_error "Composer is not installed. Please install Composer"
        exit 1
    fi
    
    if ! command -v node &> /dev/null; then
        print_error "Node.js is not installed. Please install Node.js 18+"
        exit 1
    fi
    
    if ! command -v npm &> /dev/null; then
        print_error "npm is not installed. Please install npm"
        exit 1
    fi
    
    print_success "All prerequisites are installed"
}

# Setup backend
setup_backend() {
    print_status "Setting up backend service..."
    
    cd backend
    
    # Install dependencies
    if [ ! -d "vendor" ]; then
        print_status "Installing backend dependencies..."
        composer install
    fi
    
    # Copy environment file if it doesn't exist
    if [ ! -f ".env" ]; then
        print_status "Creating backend environment file..."
        cp ../config/environments/env.dev .env
        print_warning "Please configure your .env file with database credentials"
    fi
    
    print_success "Backend setup complete"
    cd ..
}

# Setup frontend
setup_frontend() {
    print_status "Setting up frontend service..."
    
    cd frontend
    
    # Install dependencies
    if [ ! -d "node_modules" ]; then
        print_status "Installing frontend dependencies..."
        npm install
    fi
    
    # Copy environment file if it doesn't exist
    if [ ! -f ".env.local" ]; then
        print_status "Creating frontend environment file..."
        cp env.example .env.local
        print_warning "Please configure your .env.local file with API endpoints"
    fi
    
    print_success "Frontend setup complete"
    cd ..
}

# Setup audit service
setup_audit_service() {
    print_status "Setting up audit service..."
    
    cd audit-service
    
    # Install dependencies
    if [ ! -d "vendor" ]; then
        print_status "Installing audit service dependencies..."
        composer install
    fi
    
    # Copy environment file if it doesn't exist
    if [ ! -f ".env" ]; then
        print_status "Creating audit service environment file..."
        cp env.example .env
        print_warning "Please configure your .env file with service credentials"
    fi
    
    print_success "Audit service setup complete"
    cd ..
}

# Start services
start_services() {
    print_status "Starting services..."
    
    # Start backend in background
    print_status "Starting backend service on port 8000..."
    cd backend
    symfony serve -d --port=8000 || php -S localhost:8000 -t public &
    BACKEND_PID=$!
    cd ..
    
    # Start frontend in background
    print_status "Starting frontend service on port 3000..."
    cd frontend
    npm run dev &
    FRONTEND_PID=$!
    cd ..
    
    # Start audit service in background
    print_status "Starting audit service on port 8001..."
    cd audit-service
    symfony serve -d --port=8001 || php -S localhost:8001 -t public &
    AUDIT_PID=$!
    cd ..
    
    print_success "All services started successfully!"
    echo ""
    echo "🌐 Services are running at:"
    echo "   Backend API:     http://localhost:8000"
    echo "   Frontend:        http://localhost:3000"
    echo "   Audit Service:   http://localhost:8001"
    echo ""
    echo "📝 Process IDs:"
    echo "   Backend:   $BACKEND_PID"
    echo "   Frontend:  $FRONTEND_PID"
    echo "   Audit:     $AUDIT_PID"
    echo ""
    echo "🛑 To stop services, run: kill $BACKEND_PID $FRONTEND_PID $AUDIT_PID"
}

# Main execution
main() {
    check_prerequisites
    setup_backend
    setup_frontend
    setup_audit_service
    start_services
    
    print_success "Development environment is ready!"
    print_status "Check the service URLs above to access the application"
}

# Run main function
main "$@"
