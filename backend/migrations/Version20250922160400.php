<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250922160400 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create lead_events table for tracking lead statistics';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE lead_events (id UUID NOT NULL, lead_id UUID NOT NULL, type VARCHAR(50) NOT NULL, direction VARCHAR(20) DEFAULT NULL, duration INT DEFAULT NULL, notes TEXT DEFAULT NULL, outcome VARCHAR(20) DEFAULT NULL, next_action TEXT DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_6A4A3A0E55458D ON lead_events (lead_id)');
        $this->addSql('COMMENT ON COLUMN lead_events.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN lead_events.lead_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN lead_events.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN lead_events.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE lead_events ADD CONSTRAINT FK_6A4A3A0E55458D FOREIGN KEY (lead_id) REFERENCES leads (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE lead_events DROP CONSTRAINT FK_6A4A3A0E55458D');
        $this->addSql('DROP TABLE lead_events');
    }
}
