<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250922143519 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE openphone_call_logs (id UUID NOT NULL, client_id UUID NOT NULL, integration_id UUID NOT NULL, open_phone_call_id VARCHAR(255) NOT NULL, direction VARCHAR(32) NOT NULL, status VARCHAR(32) NOT NULL, from_number VARCHAR(255) DEFAULT NULL, to_number VARCHAR(255) DEFAULT NULL, duration INT DEFAULT NULL, started_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, ended_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, recording_url TEXT DEFAULT NULL, transcript TEXT DEFAULT NULL, metadata JSONB DEFAULT NULL, is_follow_up_required BOOLEAN DEFAULT false NOT NULL, notes TEXT DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_BC5838A419EB6921 ON openphone_call_logs (client_id)');
        $this->addSql('CREATE INDEX IDX_BC5838A49E82DDEA ON openphone_call_logs (integration_id)');
        $this->addSql('COMMENT ON COLUMN openphone_call_logs.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN openphone_call_logs.client_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN openphone_call_logs.integration_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN openphone_call_logs.started_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN openphone_call_logs.ended_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN openphone_call_logs.metadata IS \'(DC2Type:jsonb)\'');
        $this->addSql('COMMENT ON COLUMN openphone_call_logs.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN openphone_call_logs.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE openphone_integrations (id UUID NOT NULL, client_id UUID NOT NULL, phone_number VARCHAR(255) NOT NULL, display_name VARCHAR(255) DEFAULT NULL, settings JSONB DEFAULT NULL, status VARCHAR(24) DEFAULT \'active\' NOT NULL, metadata JSONB DEFAULT NULL, is_default BOOLEAN DEFAULT false NOT NULL, auto_log_calls BOOLEAN DEFAULT true NOT NULL, auto_log_messages BOOLEAN DEFAULT true NOT NULL, sync_contacts BOOLEAN DEFAULT false NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_80D45CFF19EB6921 ON openphone_integrations (client_id)');
        $this->addSql('COMMENT ON COLUMN openphone_integrations.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN openphone_integrations.client_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN openphone_integrations.settings IS \'(DC2Type:jsonb)\'');
        $this->addSql('COMMENT ON COLUMN openphone_integrations.metadata IS \'(DC2Type:jsonb)\'');
        $this->addSql('COMMENT ON COLUMN openphone_integrations.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN openphone_integrations.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE openphone_message_logs (id UUID NOT NULL, client_id UUID NOT NULL, integration_id UUID NOT NULL, open_phone_message_id VARCHAR(255) NOT NULL, direction VARCHAR(32) NOT NULL, status VARCHAR(32) NOT NULL, from_number VARCHAR(255) DEFAULT NULL, to_number VARCHAR(255) DEFAULT NULL, content TEXT NOT NULL, attachments JSONB DEFAULT NULL, sent_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, metadata JSONB DEFAULT NULL, is_follow_up_required BOOLEAN DEFAULT false NOT NULL, notes TEXT DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_A5E06DD019EB6921 ON openphone_message_logs (client_id)');
        $this->addSql('CREATE INDEX IDX_A5E06DD09E82DDEA ON openphone_message_logs (integration_id)');
        $this->addSql('COMMENT ON COLUMN openphone_message_logs.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN openphone_message_logs.client_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN openphone_message_logs.integration_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN openphone_message_logs.attachments IS \'(DC2Type:jsonb)\'');
        $this->addSql('COMMENT ON COLUMN openphone_message_logs.sent_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN openphone_message_logs.metadata IS \'(DC2Type:jsonb)\'');
        $this->addSql('COMMENT ON COLUMN openphone_message_logs.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN openphone_message_logs.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE openphone_call_logs ADD CONSTRAINT FK_BC5838A419EB6921 FOREIGN KEY (client_id) REFERENCES clients (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE openphone_call_logs ADD CONSTRAINT FK_BC5838A49E82DDEA FOREIGN KEY (integration_id) REFERENCES openphone_integrations (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE openphone_integrations ADD CONSTRAINT FK_80D45CFF19EB6921 FOREIGN KEY (client_id) REFERENCES clients (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE openphone_message_logs ADD CONSTRAINT FK_A5E06DD019EB6921 FOREIGN KEY (client_id) REFERENCES clients (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE openphone_message_logs ADD CONSTRAINT FK_A5E06DD09E82DDEA FOREIGN KEY (integration_id) REFERENCES openphone_integrations (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE leads ALTER status SET DEFAULT \'new\'');
        $this->addSql('ALTER TABLE leads ALTER status TYPE VARCHAR(16)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE openphone_call_logs DROP CONSTRAINT FK_BC5838A419EB6921');
        $this->addSql('ALTER TABLE openphone_call_logs DROP CONSTRAINT FK_BC5838A49E82DDEA');
        $this->addSql('ALTER TABLE openphone_integrations DROP CONSTRAINT FK_80D45CFF19EB6921');
        $this->addSql('ALTER TABLE openphone_message_logs DROP CONSTRAINT FK_A5E06DD019EB6921');
        $this->addSql('ALTER TABLE openphone_message_logs DROP CONSTRAINT FK_A5E06DD09E82DDEA');
        $this->addSql('DROP TABLE openphone_call_logs');
        $this->addSql('DROP TABLE openphone_integrations');
        $this->addSql('DROP TABLE openphone_message_logs');
        $this->addSql('ALTER TABLE leads ALTER status SET DEFAULT \'new_lead\'');
        $this->addSql('ALTER TABLE leads ALTER status TYPE VARCHAR(32)');
    }
}
