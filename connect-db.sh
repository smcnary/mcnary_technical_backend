#!/bin/bash

# Helper script to connect to Docker PostgreSQL database
# Usage: source connect-db.sh

export DATABASE_URL="postgresql://postgres:postgres@127.0.0.1:5433/mcnary_marketing?serverVersion=16&charset=utf8"

echo "Database connection configured:"
echo "Host: 127.0.0.1:5433"
echo "Database: mcnary_marketing"
echo "User: postgres"
echo "Password: postgres"
echo ""
echo "You can now run Symfony commands like:"
echo "  bin/console doctrine:query:sql 'SELECT version()'"
echo "  bin/console doctrine:migrations:status"
echo "  bin/console doctrine:migrations:migrate"
echo ""
echo "To use this connection in a new terminal, run: source connect-db.sh"
