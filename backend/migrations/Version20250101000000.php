<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250101000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create notifications table';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE notifications (
            id SERIAL NOT NULL,
            user_id UUID NOT NULL,
            title VARCHAR(255) NOT NULL,
            message TEXT DEFAULT NULL,
            type VARCHAR(50) NOT NULL,
            is_read BOOLEAN NOT NULL,
            action_url VARCHAR(100) DEFAULT NULL,
            action_label VARCHAR(100) DEFAULT NULL,
            created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
            read_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL,
            metadata JSON DEFAULT NULL,
            PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_6000B0D3A76ED395 ON notifications (user_id)');
        $this->addSql('COMMENT ON COLUMN notifications.user_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN notifications.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN notifications.read_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE notifications ADD CONSTRAINT FK_6000B0D3A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE notifications DROP CONSTRAINT FK_6000B0D3A76ED395');
        $this->addSql('DROP TABLE notifications');
    }
}
