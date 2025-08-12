#!/bin/bash

echo "Updating .env file with correct database connection..."

# Read database credentials from compose.yaml
DB_USER=$(grep 'POSTGRES_USER:' compose.yaml | awk '{print $2}' | sed 's/${POSTGRES_USER:-//' | sed 's/}//')
DB_PASSWORD=$(grep 'POSTGRES_PASSWORD:' compose.yaml | awk '{print $2}' | sed 's/${POSTGRES_PASSWORD:-//' | sed 's/}//')
DB_NAME=$(grep 'POSTGRES_DB:' compose.yaml | awk '{print $2}' | sed 's/${POSTGRES_DB:-//' | sed 's/}//')
DB_PORT=$(grep 'ports:' compose.yaml -A 1 | grep -oP '\d+(?=:5432)' | head -1)

if [ -z "$DB_PORT" ]; then
    DB_PORT="5432"
fi

# Create the correct DATABASE_URL
DATABASE_URL="postgresql://${DB_USER}:${DB_PASSWORD}@127.0.0.1:${DB_PORT}/${DB_NAME}?serverVersion=16&charset=utf8"

echo "Generated DATABASE_URL: $DATABASE_URL"
echo ""
echo "Please manually update your .env file with this DATABASE_URL:"
echo ""
echo "DATABASE_URL=\"$DATABASE_URL\""
echo ""
echo "Or replace the existing DATABASE_URL line in .env with the above."
echo ""
echo "After updating .env, restart your Symfony server and test the API again."
