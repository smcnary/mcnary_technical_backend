#!/bin/bash

# Helper script to connect to counselrank.legal PostgreSQL database
# Usage: source connect-counselrank-db.sh

# Update these values with your actual counselrank.legal database credentials
export DATABASE_URL="postgresql://username:password@counselrank.legal:5432/database_name?serverVersion=16&charset=utf8"

echo "Database connection configured for counselrank.legal:"
echo "Host: counselrank.legal"
echo "Port: 5432"
echo "Database: database_name"
echo "User: username"
echo ""
echo "You can now run Symfony commands like:"
echo "  bin/console doctrine:query:sql 'SELECT version()'"
echo "  bin/console doctrine:migrations:status"
echo "  bin/console doctrine:migrations:migrate"
echo ""
echo "To use this connection in a new terminal, run: source connect-counselrank-db.sh"
echo ""
echo "IMPORTANT: Update the DATABASE_URL above with your actual credentials!"
