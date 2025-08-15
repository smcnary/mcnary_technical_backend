<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Migration for enhanced role-based access control and new entities
 */
final class Version20250115000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add new entities and fields for enhanced role-based access control system';
    }

    public function up(Schema $schema): void
    {
        // Add client_id and metadata fields to users table
        $this->addSql('ALTER TABLE users ADD COLUMN client_id UUID DEFAULT NULL');
        $this->addSql('ALTER TABLE users ADD COLUMN metadata JSONB DEFAULT NULL');
        $this->addSql('CREATE INDEX idx_users_client_id ON users (client_id)');

        // Add client_id and metadata fields to leads table
        $this->addSql('ALTER TABLE leads ADD COLUMN client_id UUID DEFAULT NULL');
        $this->addSql('ALTER TABLE leads ADD COLUMN metadata JSONB DEFAULT NULL');
        $this->addSql('CREATE INDEX idx_leads_client_id ON leads (client_id)');

        // Create clients table
        $this->addSql('CREATE TABLE clients (
            id UUID NOT NULL,
            tenant_id UUID DEFAULT NULL,
            name VARCHAR(255) NOT NULL,
            slug VARCHAR(255) UNIQUE NOT NULL,
            description TEXT DEFAULT NULL,
            website VARCHAR(255) DEFAULT NULL,
            phone VARCHAR(255) DEFAULT NULL,
            address VARCHAR(255) DEFAULT NULL,
            city VARCHAR(255) DEFAULT NULL,
            state VARCHAR(255) DEFAULT NULL,
            zip_code VARCHAR(10) DEFAULT NULL,
            metadata JSONB DEFAULT NULL,
            google_business_profile JSONB DEFAULT NULL,
            google_search_console JSONB DEFAULT NULL,
            google_analytics JSONB DEFAULT NULL,
            status VARCHAR(255) DEFAULT \'active\' NOT NULL,
            created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
            updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
            PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX idx_clients_tenant_id ON clients (tenant_id)');
        $this->addSql('CREATE INDEX idx_clients_slug ON clients (slug)');
        $this->addSql('CREATE INDEX idx_clients_status ON clients (status)');

        // Create packages table
        $this->addSql('CREATE TABLE packages (
            id UUID NOT NULL,
            tenant_id UUID DEFAULT NULL,
            client_id UUID DEFAULT NULL,
            name VARCHAR(255) NOT NULL,
            slug VARCHAR(255) DEFAULT NULL,
            description TEXT DEFAULT NULL,
            price DECIMAL(10,2) DEFAULT NULL,
            billing_cycle VARCHAR(50) DEFAULT NULL,
            features JSONB NOT NULL,
            included_services JSONB NOT NULL,
            is_popular BOOLEAN DEFAULT FALSE NOT NULL,
            is_active BOOLEAN DEFAULT TRUE NOT NULL,
            sort_order INTEGER DEFAULT 0 NOT NULL,
            metadata JSONB DEFAULT NULL,
            created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
            updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
            PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX idx_packages_tenant_id ON packages (tenant_id)');
        $this->addSql('CREATE INDEX idx_packages_client_id ON packages (client_id)');
        $this->addSql('CREATE INDEX idx_packages_slug ON packages (slug)');
        $this->addSql('CREATE INDEX idx_packages_is_active ON packages (is_active)');
        $this->addSql('CREATE INDEX idx_packages_sort_order ON packages (sort_order)');

        // Create newsletter_subscriptions table
        $this->addSql('CREATE TABLE newsletter_subscriptions (
            id UUID NOT NULL,
            tenant_id UUID DEFAULT NULL,
            email VARCHAR(255) NOT NULL,
            first_name VARCHAR(255) DEFAULT NULL,
            last_name VARCHAR(255) DEFAULT NULL,
            company VARCHAR(255) DEFAULT NULL,
            phone VARCHAR(255) DEFAULT NULL,
            interests JSONB DEFAULT NULL,
            status VARCHAR(255) DEFAULT \'subscribed\' NOT NULL,
            source VARCHAR(255) DEFAULT NULL,
            metadata JSONB DEFAULT NULL,
            subscribed_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
            unsubscribed_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL,
            created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
            updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
            PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX idx_newsletter_subscriptions_tenant_id ON newsletter_subscriptions (tenant_id)');
        $this->addSql('CREATE INDEX idx_newsletter_subscriptions_email ON newsletter_subscriptions (email)');
        $this->addSql('CREATE INDEX idx_newsletter_subscriptions_status ON newsletter_subscriptions (status)');

        // Create pages table
        $this->addSql('CREATE TABLE pages (
            id UUID NOT NULL,
            tenant_id UUID DEFAULT NULL,
            client_id UUID DEFAULT NULL,
            title VARCHAR(255) NOT NULL,
            slug VARCHAR(255) UNIQUE NOT NULL,
            excerpt TEXT DEFAULT NULL,
            content TEXT NOT NULL,
            meta_title VARCHAR(255) DEFAULT NULL,
            meta_description TEXT DEFAULT NULL,
            meta_keywords JSONB DEFAULT NULL,
            featured_image VARCHAR(255) DEFAULT NULL,
            type VARCHAR(50) DEFAULT \'page\' NOT NULL,
            status VARCHAR(255) DEFAULT \'published\' NOT NULL,
            sort_order INTEGER DEFAULT 0 NOT NULL,
            metadata JSONB DEFAULT NULL,
            seo_settings JSONB DEFAULT NULL,
            published_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL,
            created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
            updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
            PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX idx_pages_tenant_id ON pages (tenant_id)');
        $this->addSql('CREATE INDEX idx_pages_client_id ON pages (client_id)');
        $this->addSql('CREATE INDEX idx_pages_slug ON pages (slug)');
        $this->addSql('CREATE INDEX idx_pages_type ON pages (type)');
        $this->addSql('CREATE INDEX idx_pages_status ON pages (status)');
        $this->addSql('CREATE INDEX idx_pages_sort_order ON pages (sort_order)');

        // Create media_assets table
        $this->addSql('CREATE TABLE media_assets (
            id UUID NOT NULL,
            tenant_id UUID DEFAULT NULL,
            client_id UUID DEFAULT NULL,
            filename VARCHAR(255) NOT NULL,
            original_filename VARCHAR(255) NOT NULL,
            mime_type VARCHAR(100) NOT NULL,
            file_size BIGINT NOT NULL,
            storage_path VARCHAR(255) NOT NULL,
            storage_provider VARCHAR(50) DEFAULT \'s3\' NOT NULL,
            title VARCHAR(255) DEFAULT NULL,
            description TEXT DEFAULT NULL,
            alt_text VARCHAR(255) DEFAULT NULL,
            type VARCHAR(50) DEFAULT \'image\' NOT NULL,
            dimensions JSONB DEFAULT NULL,
            metadata JSONB DEFAULT NULL,
            processing_status JSONB DEFAULT NULL,
            status VARCHAR(255) DEFAULT \'active\' NOT NULL,
            uploaded_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
            created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
            updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
            PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX idx_media_assets_tenant_id ON media_assets (tenant_id)');
        $this->addSql('CREATE INDEX idx_media_assets_client_id ON media_assets (client_id)');
        $this->addSql('CREATE INDEX idx_media_assets_type ON media_assets (type)');
        $this->addSql('CREATE INDEX idx_media_assets_status ON media_assets (status)');
        $this->addSql('CREATE INDEX idx_media_assets_mime_type ON media_assets (mime_type)');

        // Add foreign key constraints
        $this->addSql('ALTER TABLE users ADD CONSTRAINT fk_users_client_id FOREIGN KEY (client_id) REFERENCES clients (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE leads ADD CONSTRAINT fk_leads_client_id FOREIGN KEY (client_id) REFERENCES clients (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE packages ADD CONSTRAINT fk_packages_client_id FOREIGN KEY (client_id) REFERENCES clients (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE pages ADD CONSTRAINT fk_pages_client_id FOREIGN KEY (client_id) REFERENCES clients (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE media_assets ADD CONSTRAINT fk_media_assets_client_id FOREIGN KEY (client_id) REFERENCES clients (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema): void
    {
        // Remove foreign key constraints
        $this->addSql('ALTER TABLE users DROP CONSTRAINT fk_users_client_id');
        $this->addSql('ALTER TABLE leads DROP CONSTRAINT fk_leads_client_id');
        $this->addSql('ALTER TABLE packages DROP CONSTRAINT fk_packages_client_id');
        $this->addSql('ALTER TABLE pages DROP CONSTRAINT fk_pages_client_id');
        $this->addSql('ALTER TABLE media_assets DROP CONSTRAINT fk_media_assets_client_id');

        // Drop tables
        $this->addSql('DROP TABLE media_assets');
        $this->addSql('DROP TABLE pages');
        $this->addSql('DROP TABLE newsletter_subscriptions');
        $this->addSql('DROP TABLE packages');
        $this->addSql('DROP TABLE clients');

        // Remove columns from leads table
        $this->addSql('DROP INDEX idx_leads_client_id');
        $this->addSql('ALTER TABLE leads DROP COLUMN client_id');
        $this->addSql('ALTER TABLE leads DROP COLUMN metadata');

        // Remove columns from users table
        $this->addSql('DROP INDEX idx_users_client_id');
        $this->addSql('ALTER TABLE users DROP COLUMN client_id');
        $this->addSql('ALTER TABLE users DROP COLUMN metadata');
    }
}
