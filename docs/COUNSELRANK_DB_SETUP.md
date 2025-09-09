# CounselRank.legal Database Setup Guide

## Overview
This guide will help you connect your Symfony backend to the counselrank.legal PostgreSQL database and run migrations to create all the necessary database tables.

## Prerequisites
- Access to the counselrank.legal PostgreSQL database
- Database credentials (username, password, host, port, database name)
- Symfony CLI tools installed

## Step 1: Get Database Credentials
You need the following information from your database administrator:
- **Host/IP Address**: The IP address or hostname of the counselrank.legal database server
- **Port**: Usually 5432 for PostgreSQL
- **Database Name**: The name of the database you want to connect to
- **Username**: Your database username
- **Password**: Your database password

## Step 2: Configure Database Connection

### Option A: Using Environment Variables (Recommended)
Create a `.env.local` file in the backend directory:

```bash
# Create .env.local file
cp env-template.txt .env.local
```

Then edit `.env.local` and update the DATABASE_URL with your actual credentials:

```bash
DATABASE_URL="postgresql://your_username:your_password@your_host:5432/your_database?serverVersion=16&charset=utf8"
```

### Option B: Using the Connection Script
Edit `connect-counselrank-db.sh` and update the DATABASE_URL with your credentials, then run:

```bash
source connect-counselrank-db.sh
```

## Step 3: Test Database Connection
Test if you can connect to the database:

```bash
php bin/console doctrine:query:sql 'SELECT version()'
```

If successful, you should see PostgreSQL version information.

## Step 4: Check Migration Status
Check the current migration status:

```bash
php bin/console doctrine:migrations:status
```

## Step 5: Run Migrations
Run all pending migrations to create the database tables:

```bash
php bin/console doctrine:migrations:migrate --no-interaction
```

## Step 6: Verify Tables Created
Check that all the new entities have corresponding database tables:

```bash
php bin/console doctrine:query:sql "SELECT table_name FROM information_schema.tables WHERE table_schema = 'public' ORDER BY table_name;"
```

## New Entities That Will Be Created
The following new database tables will be created when you run the migrations:

### Core Business Entities
- **campaigns** - Marketing campaigns for clients
- **keywords** - SEO keywords and their metrics
- **rankings** - Search engine rankings for keywords
- **reviews** - Customer reviews from various platforms
- **citations** - Business directory citations
- **content_items** - Blog posts, articles, and other content
- **content_briefs** - Content creation briefs
- **audit_runs** - SEO and technical audit runs
- **audit_findings** - Individual findings from audits
- **recommendations** - Actionable recommendations for clients
- **backlinks** - Incoming links to client websites
- **subscriptions** - Client subscription plans
- **invoices** - Billing invoices

### Existing Entities (Already in Database)
- **users** - System users and staff
- **clients** - Client companies
- **pages** - Website pages
- **faqs** - Frequently asked questions
- **packages** - Service packages
- **media_assets** - Images and other media files
- **leads** - Lead information
- **posts** - Blog posts (legacy)

## Troubleshooting

### Connection Issues
- Verify the host IP address is correct
- Check if the database server allows connections from your IP
- Ensure the database name, username, and password are correct
- Check if the database server is running and accessible

### Migration Issues
- Clear Symfony cache: `php bin/console cache:clear`
- Check for syntax errors in entity files
- Verify all required dependencies are installed

### Permission Issues
- Ensure your database user has CREATE, ALTER, and DROP permissions
- Check if the database exists and is accessible

## Next Steps
After successful migration:
1. Test the API endpoints using the new entities
2. Create sample data for testing
3. Set up proper indexes for performance
4. Configure backup and monitoring

## Support
If you encounter issues:
1. Check the Symfony logs in `var/log/`
2. Verify database connectivity
3. Review entity annotations and validation rules
4. Check Doctrine configuration in `config/packages/doctrine.yaml`
