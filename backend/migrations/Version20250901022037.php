<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250901022037 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE audit_competitor (id UUID NOT NULL, intake_id UUID NOT NULL, name VARCHAR(255) NOT NULL, website_url VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_9D0B2355733DE450 ON audit_competitor (intake_id)');
        $this->addSql('COMMENT ON COLUMN audit_competitor.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN audit_competitor.intake_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE audit_conversion_goal (id UUID NOT NULL, intake_id UUID NOT NULL, type VARCHAR(32) NOT NULL, kpi VARCHAR(128) NOT NULL, baseline DOUBLE PRECISION DEFAULT NULL, value_per_conversion NUMERIC(10, 2) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_DE9A5774733DE450 ON audit_conversion_goal (intake_id)');
        $this->addSql('COMMENT ON COLUMN audit_conversion_goal.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN audit_conversion_goal.intake_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE audit_finding (id UUID NOT NULL, client_id UUID NOT NULL, audit_run_id UUID NOT NULL, title VARCHAR(255) NOT NULL, description TEXT NOT NULL, severity VARCHAR(16) NOT NULL, status VARCHAR(16) NOT NULL, category VARCHAR(64) DEFAULT NULL, location VARCHAR(255) DEFAULT NULL, impact TEXT DEFAULT NULL, recommendation TEXT DEFAULT NULL, impact_score SMALLINT DEFAULT 3 NOT NULL, effort_score SMALLINT DEFAULT 3 NOT NULL, priority_score SMALLINT DEFAULT 3 NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_B63BC64619EB6921 ON audit_finding (client_id)');
        $this->addSql('CREATE INDEX IDX_B63BC646A73DD21F ON audit_finding (audit_run_id)');
        $this->addSql('COMMENT ON COLUMN audit_finding.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN audit_finding.client_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN audit_finding.audit_run_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE audit_intake (id UUID NOT NULL, client_id UUID NOT NULL, requested_by_id UUID DEFAULT NULL, contact_name VARCHAR(255) DEFAULT NULL, contact_email VARCHAR(255) DEFAULT NULL, contact_phone VARCHAR(32) DEFAULT NULL, website_url VARCHAR(255) NOT NULL, subdomains JSON DEFAULT NULL, staging_url VARCHAR(255) DEFAULT NULL, cms VARCHAR(64) NOT NULL, cms_version VARCHAR(64) DEFAULT NULL, hosting_provider VARCHAR(128) DEFAULT NULL, tech_stack JSON DEFAULT NULL, has_google_analytics BOOLEAN DEFAULT false NOT NULL, has_search_console BOOLEAN DEFAULT false NOT NULL, has_google_business_profile BOOLEAN DEFAULT false NOT NULL, has_tag_manager BOOLEAN DEFAULT false NOT NULL, ga_property_id VARCHAR(255) DEFAULT NULL, gsc_property VARCHAR(255) DEFAULT NULL, gbp_location_ids JSON DEFAULT NULL, gtm_container_id VARCHAR(255) DEFAULT NULL, markets JSON DEFAULT NULL, primary_services JSON DEFAULT NULL, target_audience JSON DEFAULT NULL, paid_channels JSON DEFAULT NULL, notes TEXT DEFAULT NULL, status VARCHAR(24) NOT NULL, robots_txt_url VARCHAR(255) DEFAULT NULL, sitemap_xml_url VARCHAR(255) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_8D7CABA119EB6921 ON audit_intake (client_id)');
        $this->addSql('CREATE INDEX IDX_8D7CABA14DA1E751 ON audit_intake (requested_by_id)');
        $this->addSql('COMMENT ON COLUMN audit_intake.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN audit_intake.client_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN audit_intake.requested_by_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN audit_intake.cms IS \'wordpress|shopify|webflow|custom|other\'');
        $this->addSql('COMMENT ON COLUMN audit_intake.status IS \'draft|submitted|approved\'');
        $this->addSql('COMMENT ON COLUMN audit_intake.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN audit_intake.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE audit_keyword (id UUID NOT NULL, intake_id UUID NOT NULL, phrase VARCHAR(255) NOT NULL, intent VARCHAR(16) NOT NULL, priority SMALLINT DEFAULT 3 NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_4BB1A44B733DE450 ON audit_keyword (intake_id)');
        $this->addSql('COMMENT ON COLUMN audit_keyword.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN audit_keyword.intake_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE audit_run (id UUID NOT NULL, client_id UUID NOT NULL, intake_id UUID DEFAULT NULL, initiated_by_id UUID DEFAULT NULL, status VARCHAR(16) NOT NULL, scope JSON DEFAULT NULL, started_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, completed_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, tool VARCHAR(50) DEFAULT NULL, priority VARCHAR(20) DEFAULT \'medium\' NOT NULL, notify_email VARCHAR(255) DEFAULT NULL, findings_count INT DEFAULT 0 NOT NULL, metadata JSON DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_29A80CF019EB6921 ON audit_run (client_id)');
        $this->addSql('CREATE INDEX IDX_29A80CF0733DE450 ON audit_run (intake_id)');
        $this->addSql('CREATE INDEX IDX_29A80CF0C4EF1FC7 ON audit_run (initiated_by_id)');
        $this->addSql('COMMENT ON COLUMN audit_run.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN audit_run.client_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN audit_run.intake_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN audit_run.initiated_by_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN audit_run.started_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN audit_run.completed_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN audit_run.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN audit_run.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE audit_competitor ADD CONSTRAINT FK_9D0B2355733DE450 FOREIGN KEY (intake_id) REFERENCES audit_intake (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE audit_conversion_goal ADD CONSTRAINT FK_DE9A5774733DE450 FOREIGN KEY (intake_id) REFERENCES audit_intake (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE audit_finding ADD CONSTRAINT FK_B63BC64619EB6921 FOREIGN KEY (client_id) REFERENCES clients (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE audit_finding ADD CONSTRAINT FK_B63BC646A73DD21F FOREIGN KEY (audit_run_id) REFERENCES audit_run (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE audit_intake ADD CONSTRAINT FK_8D7CABA119EB6921 FOREIGN KEY (client_id) REFERENCES clients (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE audit_intake ADD CONSTRAINT FK_8D7CABA14DA1E751 FOREIGN KEY (requested_by_id) REFERENCES users (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE audit_keyword ADD CONSTRAINT FK_4BB1A44B733DE450 FOREIGN KEY (intake_id) REFERENCES audit_intake (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE audit_run ADD CONSTRAINT FK_29A80CF019EB6921 FOREIGN KEY (client_id) REFERENCES clients (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE audit_run ADD CONSTRAINT FK_29A80CF0733DE450 FOREIGN KEY (intake_id) REFERENCES audit_intake (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE audit_run ADD CONSTRAINT FK_29A80CF0C4EF1FC7 FOREIGN KEY (initiated_by_id) REFERENCES users (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('DROP TABLE audit_findings');
        $this->addSql('DROP TABLE audit_runs');
        $this->addSql('ALTER TABLE leads DROP CONSTRAINT FK_17904552953C1C61');
        $this->addSql('ALTER TABLE leads ADD CONSTRAINT FK_17904552953C1C61 FOREIGN KEY (source_id) REFERENCES clients (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE oauth_connections DROP CONSTRAINT FK_E3D750BE19EB6921');
        $this->addSql('ALTER TABLE oauth_connections ADD CONSTRAINT FK_E3D750BE19EB6921 FOREIGN KEY (client_id) REFERENCES users (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE oauth_tokens DROP CONSTRAINT FK_C06D3296DD03F01');
        $this->addSql('ALTER TABLE oauth_tokens ADD CONSTRAINT FK_C06D3296DD03F01 FOREIGN KEY (connection_id) REFERENCES users (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE rankings_daily DROP CONSTRAINT FK_B0D35353115D4552');
        $this->addSql('ALTER TABLE rankings_daily ADD CONSTRAINT FK_B0D35353115D4552 FOREIGN KEY (keyword_id) REFERENCES clients (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_client_access DROP CONSTRAINT FK_BD41CDB719EB6921');
        $this->addSql('ALTER TABLE user_client_access DROP CONSTRAINT FK_BD41CDB7A76ED395');
        $this->addSql('ALTER TABLE user_client_access ADD CONSTRAINT FK_BD41CDB719EB6921 FOREIGN KEY (client_id) REFERENCES users (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_client_access ADD CONSTRAINT FK_BD41CDB7A76ED395 FOREIGN KEY (user_id) REFERENCES clients (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE TABLE audit_findings (id UUID NOT NULL, tenant_id UUID DEFAULT NULL, client_id UUID NOT NULL, audit_run_id UUID NOT NULL, title VARCHAR(255) NOT NULL, description TEXT NOT NULL, severity VARCHAR(20) NOT NULL, status VARCHAR(50) NOT NULL, category VARCHAR(255) DEFAULT NULL, location VARCHAR(255) DEFAULT NULL, impact TEXT DEFAULT NULL, recommendation TEXT DEFAULT NULL, code_example TEXT DEFAULT NULL, score NUMERIC(5, 2) DEFAULT NULL, evidence JSONB DEFAULT NULL, "references" JSONB DEFAULT NULL, metadata JSONB DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN audit_findings.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN audit_findings.tenant_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN audit_findings.client_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN audit_findings.audit_run_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN audit_findings.evidence IS \'(DC2Type:jsonb)\'');
        $this->addSql('COMMENT ON COLUMN audit_findings."references" IS \'(DC2Type:jsonb)\'');
        $this->addSql('COMMENT ON COLUMN audit_findings.metadata IS \'(DC2Type:jsonb)\'');
        $this->addSql('COMMENT ON COLUMN audit_findings.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN audit_findings.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE audit_runs (id UUID NOT NULL, tenant_id UUID DEFAULT NULL, client_id UUID NOT NULL, name VARCHAR(255) NOT NULL, description TEXT DEFAULT NULL, type VARCHAR(50) NOT NULL, status VARCHAR(50) NOT NULL, target_url VARCHAR(255) DEFAULT NULL, started_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, completed_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, total_issues INT DEFAULT NULL, critical_issues INT DEFAULT NULL, high_issues INT DEFAULT NULL, medium_issues INT DEFAULT NULL, low_issues INT DEFAULT NULL, score NUMERIC(5, 2) DEFAULT NULL, settings JSONB DEFAULT NULL, results JSONB DEFAULT NULL, metadata JSONB DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN audit_runs.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN audit_runs.tenant_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN audit_runs.client_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN audit_runs.started_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN audit_runs.completed_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN audit_runs.settings IS \'(DC2Type:jsonb)\'');
        $this->addSql('COMMENT ON COLUMN audit_runs.results IS \'(DC2Type:jsonb)\'');
        $this->addSql('COMMENT ON COLUMN audit_runs.metadata IS \'(DC2Type:jsonb)\'');
        $this->addSql('COMMENT ON COLUMN audit_runs.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN audit_runs.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE audit_competitor DROP CONSTRAINT FK_9D0B2355733DE450');
        $this->addSql('ALTER TABLE audit_conversion_goal DROP CONSTRAINT FK_DE9A5774733DE450');
        $this->addSql('ALTER TABLE audit_finding DROP CONSTRAINT FK_B63BC64619EB6921');
        $this->addSql('ALTER TABLE audit_finding DROP CONSTRAINT FK_B63BC646A73DD21F');
        $this->addSql('ALTER TABLE audit_intake DROP CONSTRAINT FK_8D7CABA119EB6921');
        $this->addSql('ALTER TABLE audit_intake DROP CONSTRAINT FK_8D7CABA14DA1E751');
        $this->addSql('ALTER TABLE audit_keyword DROP CONSTRAINT FK_4BB1A44B733DE450');
        $this->addSql('ALTER TABLE audit_run DROP CONSTRAINT FK_29A80CF019EB6921');
        $this->addSql('ALTER TABLE audit_run DROP CONSTRAINT FK_29A80CF0733DE450');
        $this->addSql('ALTER TABLE audit_run DROP CONSTRAINT FK_29A80CF0C4EF1FC7');
        $this->addSql('DROP TABLE audit_competitor');
        $this->addSql('DROP TABLE audit_conversion_goal');
        $this->addSql('DROP TABLE audit_finding');
        $this->addSql('DROP TABLE audit_intake');
        $this->addSql('DROP TABLE audit_keyword');
        $this->addSql('DROP TABLE audit_run');
        $this->addSql('ALTER TABLE user_client_access DROP CONSTRAINT fk_bd41cdb7a76ed395');
        $this->addSql('ALTER TABLE user_client_access DROP CONSTRAINT fk_bd41cdb719eb6921');
        $this->addSql('ALTER TABLE user_client_access ADD CONSTRAINT fk_bd41cdb7a76ed395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_client_access ADD CONSTRAINT fk_bd41cdb719eb6921 FOREIGN KEY (client_id) REFERENCES clients (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE oauth_connections DROP CONSTRAINT fk_e3d750be19eb6921');
        $this->addSql('ALTER TABLE oauth_connections ADD CONSTRAINT fk_e3d750be19eb6921 FOREIGN KEY (client_id) REFERENCES clients (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE oauth_tokens DROP CONSTRAINT fk_c06d3296dd03f01');
        $this->addSql('ALTER TABLE oauth_tokens ADD CONSTRAINT fk_c06d3296dd03f01 FOREIGN KEY (connection_id) REFERENCES oauth_connections (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE rankings_daily DROP CONSTRAINT fk_b0d35353115d4552');
        $this->addSql('ALTER TABLE rankings_daily ADD CONSTRAINT fk_b0d35353115d4552 FOREIGN KEY (keyword_id) REFERENCES keywords (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE leads DROP CONSTRAINT fk_17904552953c1c61');
        $this->addSql('ALTER TABLE leads ADD CONSTRAINT fk_17904552953c1c61 FOREIGN KEY (source_id) REFERENCES lead_sources (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }
}
