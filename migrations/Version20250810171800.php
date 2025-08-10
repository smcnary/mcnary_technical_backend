<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250810171800 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE categories (id UUID NOT NULL, tenant_id UUID DEFAULT NULL, name VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, description TEXT DEFAULT NULL, status VARCHAR(50) DEFAULT \'active\' NOT NULL, sort_order INT DEFAULT 0 NOT NULL, created_at TIMESTAMP(0) WITH TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITH TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_3AF346689033212A989D9B62 ON categories (tenant_id, slug)');
        $this->addSql('COMMENT ON COLUMN categories.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN categories.tenant_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN categories.created_at IS \'(DC2Type:datetimetz_immutable)\'');
        $this->addSql('COMMENT ON COLUMN categories.updated_at IS \'(DC2Type:datetimetz_immutable)\'');
        $this->addSql('CREATE TABLE form_submissions (id UUID NOT NULL, tenant_id UUID NOT NULL, form_id UUID NOT NULL, data JSON NOT NULL, ip_address VARCHAR(255) DEFAULT NULL, user_agent VARCHAR(255) DEFAULT NULL, created_at TIMESTAMP(0) WITH TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN form_submissions.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN form_submissions.tenant_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN form_submissions.form_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN form_submissions.created_at IS \'(DC2Type:datetimetz_immutable)\'');
        $this->addSql('CREATE TABLE forms (id UUID NOT NULL, tenant_id UUID NOT NULL, site_id UUID NOT NULL, name VARCHAR(255) NOT NULL, fields JSON NOT NULL, status VARCHAR(255) DEFAULT \'active\' NOT NULL, created_at TIMESTAMP(0) WITH TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITH TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_FD3F1BF79033212AF6BD16465E237E06 ON forms (tenant_id, site_id, name)');
        $this->addSql('COMMENT ON COLUMN forms.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN forms.tenant_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN forms.site_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN forms.created_at IS \'(DC2Type:datetimetz_immutable)\'');
        $this->addSql('COMMENT ON COLUMN forms.updated_at IS \'(DC2Type:datetimetz_immutable)\'');
        $this->addSql('CREATE TABLE pages (id UUID NOT NULL, tenant_id UUID NOT NULL, site_id UUID NOT NULL, title VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, content TEXT DEFAULT NULL, status VARCHAR(255) DEFAULT \'draft\' NOT NULL, published_at TIMESTAMP(0) WITH TIME ZONE DEFAULT NULL, created_at TIMESTAMP(0) WITH TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITH TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2074E5759033212AF6BD1646989D9B62 ON pages (tenant_id, site_id, slug)');
        $this->addSql('COMMENT ON COLUMN pages.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN pages.tenant_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN pages.site_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN pages.published_at IS \'(DC2Type:datetimetz_immutable)\'');
        $this->addSql('COMMENT ON COLUMN pages.created_at IS \'(DC2Type:datetimetz_immutable)\'');
        $this->addSql('COMMENT ON COLUMN pages.updated_at IS \'(DC2Type:datetimetz_immutable)\'');
        $this->addSql('CREATE TABLE posts (id UUID NOT NULL, tenant_id UUID NOT NULL, site_id UUID NOT NULL, author_id UUID DEFAULT NULL, title VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, status VARCHAR(255) DEFAULT \'draft\' NOT NULL, excerpt TEXT DEFAULT NULL, published_at TIMESTAMP(0) WITH TIME ZONE DEFAULT NULL, created_at TIMESTAMP(0) WITH TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITH TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_posts_tenant_status_pub ON posts (tenant_id, status, published_at)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_885DBAFA9033212AF6BD1646989D9B62 ON posts (tenant_id, site_id, slug)');
        $this->addSql('COMMENT ON COLUMN posts.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN posts.tenant_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN posts.site_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN posts.author_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN posts.published_at IS \'(DC2Type:datetimetz_immutable)\'');
        $this->addSql('COMMENT ON COLUMN posts.created_at IS \'(DC2Type:datetimetz_immutable)\'');
        $this->addSql('COMMENT ON COLUMN posts.updated_at IS \'(DC2Type:datetimetz_immutable)\'');
        $this->addSql('CREATE TABLE seo_meta (id UUID NOT NULL, tenant_id UUID NOT NULL, entity_type VARCHAR(255) NOT NULL, entity_id UUID NOT NULL, title VARCHAR(255) DEFAULT NULL, meta_description TEXT DEFAULT NULL, canonical_url VARCHAR(255) DEFAULT NULL, robots VARCHAR(255) DEFAULT NULL, open_graph JSON DEFAULT NULL, twitter_card JSON DEFAULT NULL, created_at TIMESTAMP(0) WITH TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITH TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2B2404E49033212AC412EE0281257D5D ON seo_meta (tenant_id, entity_type, entity_id)');
        $this->addSql('COMMENT ON COLUMN seo_meta.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN seo_meta.tenant_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN seo_meta.entity_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN seo_meta.created_at IS \'(DC2Type:datetimetz_immutable)\'');
        $this->addSql('COMMENT ON COLUMN seo_meta.updated_at IS \'(DC2Type:datetimetz_immutable)\'');
        $this->addSql('CREATE TABLE sites (id UUID NOT NULL, tenant_id UUID NOT NULL, domain VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, status VARCHAR(255) DEFAULT \'active\' NOT NULL, created_at TIMESTAMP(0) WITH TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITH TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_BC00AA639033212AA7A91E0B ON sites (tenant_id, domain)');
        $this->addSql('COMMENT ON COLUMN sites.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN sites.tenant_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN sites.created_at IS \'(DC2Type:datetimetz_immutable)\'');
        $this->addSql('COMMENT ON COLUMN sites.updated_at IS \'(DC2Type:datetimetz_immutable)\'');
        $this->addSql('CREATE TABLE tags (id UUID NOT NULL, tenant_id UUID DEFAULT NULL, name VARCHAR(100) NOT NULL, slug VARCHAR(100) NOT NULL, color VARCHAR(7) DEFAULT NULL, status VARCHAR(50) DEFAULT \'active\' NOT NULL, created_at TIMESTAMP(0) WITH TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITH TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_6FBC94269033212A5E237E06 ON tags (tenant_id, name)');
        $this->addSql('COMMENT ON COLUMN tags.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN tags.tenant_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN tags.created_at IS \'(DC2Type:datetimetz_immutable)\'');
        $this->addSql('COMMENT ON COLUMN tags.updated_at IS \'(DC2Type:datetimetz_immutable)\'');
        $this->addSql('CREATE TABLE tenants (id UUID NOT NULL, name VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, status VARCHAR(255) DEFAULT \'trial\' NOT NULL, timezone VARCHAR(255) DEFAULT \'UTC\' NOT NULL, created_at TIMESTAMP(0) WITH TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITH TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B8FC96BB989D9B62 ON tenants (slug)');
        $this->addSql('COMMENT ON COLUMN tenants.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN tenants.created_at IS \'(DC2Type:datetimetz_immutable)\'');
        $this->addSql('COMMENT ON COLUMN tenants.updated_at IS \'(DC2Type:datetimetz_immutable)\'');
        $this->addSql('CREATE TABLE users (id UUID NOT NULL, tenant_id UUID DEFAULT NULL, email VARCHAR(255) NOT NULL, password_hash VARCHAR(255) DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, status VARCHAR(255) DEFAULT \'invited\' NOT NULL, last_login_at TIMESTAMP(0) WITH TIME ZONE DEFAULT NULL, created_at TIMESTAMP(0) WITH TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITH TIME ZONE NOT NULL, roles JSON NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E99033212AE7927C74 ON users (tenant_id, email)');
        $this->addSql('COMMENT ON COLUMN users.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN users.tenant_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN users.last_login_at IS \'(DC2Type:datetimetz_immutable)\'');
        $this->addSql('COMMENT ON COLUMN users.created_at IS \'(DC2Type:datetimetz_immutable)\'');
        $this->addSql('COMMENT ON COLUMN users.updated_at IS \'(DC2Type:datetimetz_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP TABLE categories');
        $this->addSql('DROP TABLE form_submissions');
        $this->addSql('DROP TABLE forms');
        $this->addSql('DROP TABLE pages');
        $this->addSql('DROP TABLE posts');
        $this->addSql('DROP TABLE seo_meta');
        $this->addSql('DROP TABLE sites');
        $this->addSql('DROP TABLE tags');
        $this->addSql('DROP TABLE tenants');
        $this->addSql('DROP TABLE users');
    }
}
