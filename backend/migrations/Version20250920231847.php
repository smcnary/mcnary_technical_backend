<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250920231847 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE document_signatures (id UUID NOT NULL, document_id UUID NOT NULL, signed_by_id UUID NOT NULL, signature_image VARCHAR(255) DEFAULT NULL, signature_data TEXT DEFAULT NULL, ip_address VARCHAR(255) DEFAULT NULL, user_agent VARCHAR(500) DEFAULT NULL, signed_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, metadata JSONB DEFAULT NULL, status VARCHAR(50) DEFAULT \'pending\' NOT NULL, comments TEXT DEFAULT NULL, is_digital_signature BOOLEAN DEFAULT false NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_BBB89D5AC33F7837 ON document_signatures (document_id)');
        $this->addSql('CREATE INDEX IDX_BBB89D5AD2EDD3FB ON document_signatures (signed_by_id)');
        $this->addSql('COMMENT ON COLUMN document_signatures.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN document_signatures.document_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN document_signatures.signed_by_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN document_signatures.signed_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN document_signatures.metadata IS \'(DC2Type:jsonb)\'');
        $this->addSql('COMMENT ON COLUMN document_signatures.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN document_signatures.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE document_templates (id UUID NOT NULL, name VARCHAR(255) NOT NULL, description TEXT DEFAULT NULL, content TEXT NOT NULL, type VARCHAR(50) NOT NULL, variables JSONB DEFAULT NULL, signature_fields JSONB DEFAULT NULL, metadata JSONB DEFAULT NULL, is_active BOOLEAN DEFAULT true NOT NULL, requires_signature BOOLEAN DEFAULT false NOT NULL, usage_count INT DEFAULT 0 NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN document_templates.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN document_templates.variables IS \'(DC2Type:jsonb)\'');
        $this->addSql('COMMENT ON COLUMN document_templates.signature_fields IS \'(DC2Type:jsonb)\'');
        $this->addSql('COMMENT ON COLUMN document_templates.metadata IS \'(DC2Type:jsonb)\'');
        $this->addSql('COMMENT ON COLUMN document_templates.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN document_templates.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE document_versions (id UUID NOT NULL, document_id UUID NOT NULL, created_by_id UUID NOT NULL, file_id UUID DEFAULT NULL, version_number INT NOT NULL, content TEXT DEFAULT NULL, title VARCHAR(255) DEFAULT NULL, description TEXT DEFAULT NULL, metadata JSONB DEFAULT NULL, changes JSONB DEFAULT NULL, is_current BOOLEAN DEFAULT false NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_961DB18BC33F7837 ON document_versions (document_id)');
        $this->addSql('CREATE INDEX IDX_961DB18BB03A8386 ON document_versions (created_by_id)');
        $this->addSql('CREATE INDEX IDX_961DB18B93CB796C ON document_versions (file_id)');
        $this->addSql('COMMENT ON COLUMN document_versions.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN document_versions.document_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN document_versions.created_by_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN document_versions.file_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN document_versions.metadata IS \'(DC2Type:jsonb)\'');
        $this->addSql('COMMENT ON COLUMN document_versions.changes IS \'(DC2Type:jsonb)\'');
        $this->addSql('COMMENT ON COLUMN document_versions.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN document_versions.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE documents (id UUID NOT NULL, client_id UUID NOT NULL, created_by_id UUID NOT NULL, file_id UUID DEFAULT NULL, template_id UUID DEFAULT NULL, title VARCHAR(255) NOT NULL, description TEXT DEFAULT NULL, content TEXT DEFAULT NULL, status VARCHAR(50) DEFAULT \'draft\' NOT NULL, type VARCHAR(50) NOT NULL, metadata JSONB DEFAULT NULL, signature_fields JSONB DEFAULT NULL, sent_for_signature_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, signed_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, expires_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, requires_signature BOOLEAN DEFAULT false NOT NULL, is_template BOOLEAN DEFAULT false NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_A2B0728819EB6921 ON documents (client_id)');
        $this->addSql('CREATE INDEX IDX_A2B07288B03A8386 ON documents (created_by_id)');
        $this->addSql('CREATE INDEX IDX_A2B0728893CB796C ON documents (file_id)');
        $this->addSql('CREATE INDEX IDX_A2B072885DA0FB8 ON documents (template_id)');
        $this->addSql('COMMENT ON COLUMN documents.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN documents.client_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN documents.created_by_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN documents.file_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN documents.template_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN documents.metadata IS \'(DC2Type:jsonb)\'');
        $this->addSql('COMMENT ON COLUMN documents.signature_fields IS \'(DC2Type:jsonb)\'');
        $this->addSql('COMMENT ON COLUMN documents.sent_for_signature_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN documents.signed_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN documents.expires_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN documents.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN documents.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE document_signatures ADD CONSTRAINT FK_BBB89D5AC33F7837 FOREIGN KEY (document_id) REFERENCES documents (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE document_signatures ADD CONSTRAINT FK_BBB89D5AD2EDD3FB FOREIGN KEY (signed_by_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE document_versions ADD CONSTRAINT FK_961DB18BC33F7837 FOREIGN KEY (document_id) REFERENCES documents (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE document_versions ADD CONSTRAINT FK_961DB18BB03A8386 FOREIGN KEY (created_by_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE document_versions ADD CONSTRAINT FK_961DB18B93CB796C FOREIGN KEY (file_id) REFERENCES media_assets (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE documents ADD CONSTRAINT FK_A2B0728819EB6921 FOREIGN KEY (client_id) REFERENCES clients (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE documents ADD CONSTRAINT FK_A2B07288B03A8386 FOREIGN KEY (created_by_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE documents ADD CONSTRAINT FK_A2B0728893CB796C FOREIGN KEY (file_id) REFERENCES media_assets (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE documents ADD CONSTRAINT FK_A2B072885DA0FB8 FOREIGN KEY (template_id) REFERENCES document_templates (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE document_signatures DROP CONSTRAINT FK_BBB89D5AC33F7837');
        $this->addSql('ALTER TABLE document_signatures DROP CONSTRAINT FK_BBB89D5AD2EDD3FB');
        $this->addSql('ALTER TABLE document_versions DROP CONSTRAINT FK_961DB18BC33F7837');
        $this->addSql('ALTER TABLE document_versions DROP CONSTRAINT FK_961DB18BB03A8386');
        $this->addSql('ALTER TABLE document_versions DROP CONSTRAINT FK_961DB18B93CB796C');
        $this->addSql('ALTER TABLE documents DROP CONSTRAINT FK_A2B0728819EB6921');
        $this->addSql('ALTER TABLE documents DROP CONSTRAINT FK_A2B07288B03A8386');
        $this->addSql('ALTER TABLE documents DROP CONSTRAINT FK_A2B0728893CB796C');
        $this->addSql('ALTER TABLE documents DROP CONSTRAINT FK_A2B072885DA0FB8');
        $this->addSql('DROP TABLE document_signatures');
        $this->addSql('DROP TABLE document_templates');
        $this->addSql('DROP TABLE document_versions');
        $this->addSql('DROP TABLE documents');
    }
}
