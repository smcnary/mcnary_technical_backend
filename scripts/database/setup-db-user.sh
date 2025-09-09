#!/bin/bash

echo "👤 Setting up Database User: smcnary..."
echo "======================================="

# Check if we're in the backend directory
if [ ! -f "composer.json" ]; then
    echo "❌ Error: Please run this script from the backend directory"
    exit 1
fi

# Database connection details
DB_HOST=${DB_HOST:-"127.0.0.1"}
DB_PORT=${DB_PORT:-"5432"}
DB_NAME=${DB_NAME:-"tulsa_seo"}
DB_USER=${DB_USER:-"postgres"}
DB_PASSWORD=${DB_PASSWORD:-"postgres"}

echo "🔧 Database Configuration:"
echo "   Host: $DB_HOST"
echo "   Port: $DB_PORT"
echo "   Database: $DB_NAME"
echo "   Admin User: $DB_USER"

# Function to create user and database
create_user_and_database() {
    echo "🗄️ Creating Tulsa SEO database and user..."
    
    # Create database if it doesn't exist
    PGPASSWORD="$DB_PASSWORD" psql -h "$DB_HOST" -p "$DB_PORT" -U "$DB_USER" -d postgres << EOF
-- Create database if it doesn't exist
SELECT 'CREATE DATABASE $DB_NAME' WHERE NOT EXISTS (SELECT FROM pg_database WHERE datname = '$DB_NAME')\gexec

-- Create user if it doesn't exist
DO \$\$
BEGIN
    IF NOT EXISTS (SELECT FROM pg_catalog.pg_roles WHERE rolname = 'smcnary') THEN
        CREATE ROLE smcnary WITH LOGIN PASSWORD 'TulsaSeo122';
    ELSE
        ALTER ROLE smcnary WITH PASSWORD 'TulsaSeo122';
    END IF;
END
\$\$;

-- Grant privileges
GRANT ALL PRIVILEGES ON DATABASE $DB_NAME TO smcnary;
GRANT CREATE ON DATABASE $DB_NAME TO smcnary;
GRANT CONNECT ON DATABASE $DB_NAME TO smcnary;

-- Connect to the database to grant schema privileges
\c $DB_NAME

-- Grant schema privileges
GRANT ALL ON SCHEMA public TO smcnary;
GRANT ALL PRIVILEGES ON ALL TABLES IN SCHEMA public TO smcnary;
GRANT ALL PRIVILEGES ON ALL SEQUENCES IN SCHEMA public TO smcnary;
GRANT ALL PRIVILEGES ON ALL FUNCTIONS IN SCHEMA public TO smcnary;

-- Set default privileges for future objects
ALTER DEFAULT PRIVILEGES IN SCHEMA public GRANT ALL ON TABLES TO smcnary;
ALTER DEFAULT PRIVILEGES IN SCHEMA public GRANT ALL ON SEQUENCES TO smcnary;
ALTER DEFAULT PRIVILEGES IN SCHEMA public GRANT ALL ON FUNCTIONS TO smcnary;

-- Make smcnary the owner of the database
ALTER DATABASE $DB_NAME OWNER TO smcnary;
EOF

    if [ $? -eq 0 ]; then
        echo "✅ Database and user setup completed successfully!"
    else
        echo "❌ Error setting up database and user"
        exit 1
    fi
}

# Function to test connection with new user
test_connection() {
    echo "🔍 Testing connection with smcnary user..."
    
    if PGPASSWORD="TulsaSeo122" psql -h "$DB_HOST" -p "$DB_PORT" -U "smcnary" -d "$DB_NAME" -c "SELECT 'Connection successful' as status;" &> /dev/null; then
        echo "✅ Connection test successful with smcnary user"
        return 0
    else
        echo "❌ Connection test failed with smcnary user"
        return 1
    fi
}

# Function to create system admin user in Symfony
create_system_admin() {
    echo "👑 Creating system admin user in Symfony..."
    
    # Check if .env.local exists and has the right database URL
    if [ -f ".env.local" ]; then
        # Update .env.local with smcnary credentials
        if grep -q "DATABASE_URL" .env.local; then
            # Extract host and port from current DATABASE_URL
            CURRENT_HOST=$(grep "DATABASE_URL" .env.local | sed 's/.*@\([^:]*\):\([^\/]*\)\/.*/\1/')
            CURRENT_PORT=$(grep "DATABASE_URL" .env.local | sed 's/.*@[^:]*:\([^\/]*\)\/.*/\1/')
            
            # Update with smcnary credentials
            NEW_DB_URL="postgresql://smcnary:TulsaSeo122@${CURRENT_HOST:-127.0.0.1}:${CURRENT_PORT:-5432}/tulsa_seo?serverVersion=16&charset=utf8"
            
            # Escape special characters for sed
            ESCAPED_URL=$(echo "$NEW_DB_URL" | sed 's/[[\.*^$()+?{|]/\\&/g')
            
            # Update the file
            sed -i.bak "s|DATABASE_URL=.*|DATABASE_URL=\"$ESCAPED_URL\"|" .env.local
            echo "✅ Updated .env.local with smcnary credentials"
        fi
    fi
    
    # Run Symfony command to create system admin
    echo "🚀 Creating system admin user..."
    php bin/console app:create-system-account --username=smcnary --display-name="Sean McNary" --permissions=read,write,admin --env=dev
    
    if [ $? -eq 0 ]; then
        echo "✅ System admin user created successfully!"
    else
        echo "⚠️  System admin creation failed (this might be expected if user already exists)"
    fi
}

# Main execution
echo "🚀 Starting database user setup..."

# Check if psql is available
if ! command -v psql &> /dev/null; then
    echo "❌ Error: psql command not found. Please install PostgreSQL client tools."
    echo "   On macOS: brew install postgresql"
    echo "   On Ubuntu: sudo apt-get install postgresql-client"
    exit 1
fi

# Create user and database
create_user_and_database

# Test connection
if test_connection; then
    echo "✅ Database user setup completed successfully!"
    
    # Create system admin in Symfony
    create_system_admin
    
    echo ""
    echo "🎉 Setup Complete!"
    echo "=================="
    echo "Database: $DB_NAME"
    echo "User: smcnary"
    echo "Password: TulsaSeo122"
    echo "Privileges: System Admin (ALL PRIVILEGES)"
    echo ""
    echo "🔗 Connection String:"
    echo "postgresql://smcnary:TulsaSeo122@$DB_HOST:$DB_PORT/$DB_NAME"
    echo ""
    echo "🚀 Next steps:"
    echo "   1. Run migrations: php bin/console doctrine:migrations:migrate"
    echo "   2. Start server: php bin/console server:start 0.0.0.0:8000"
    echo "   3. Test API endpoints"
else
    echo "❌ Database user setup failed. Please check your configuration."
    exit 1
fi
