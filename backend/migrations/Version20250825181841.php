<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250825181841 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE agencies (id UUID NOT NULL, name VARCHAR(255) NOT NULL, domain VARCHAR(255) DEFAULT NULL, description TEXT DEFAULT NULL, website_url VARCHAR(255) DEFAULT NULL, phone VARCHAR(255) DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, address VARCHAR(255) DEFAULT NULL, city VARCHAR(255) DEFAULT NULL, state VARCHAR(255) DEFAULT NULL, postal_code VARCHAR(10) DEFAULT NULL, country VARCHAR(255) DEFAULT NULL, status VARCHAR(255) DEFAULT \'active\' NOT NULL, metadata JSONB DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN agencies.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN agencies.metadata IS \'(DC2Type:jsonb)\'');
        $this->addSql('COMMENT ON COLUMN agencies.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN agencies.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE clients DROP CONSTRAINT fk_c82e7432c8a3de');
        $this->addSql('DROP INDEX idx_c82e7432c8a3de');
        $this->addSql('ALTER TABLE clients RENAME COLUMN organization_id TO agency_id');
        $this->addSql('ALTER TABLE clients ADD CONSTRAINT FK_C82E74CDEADB2A FOREIGN KEY (agency_id) REFERENCES agencies (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_C82E74CDEADB2A ON clients (agency_id)');
        $this->addSql('ALTER TABLE users DROP CONSTRAINT FK_1483A5E932C8A3DE');
        $this->addSql('ALTER TABLE users DROP CONSTRAINT FK_1483A5E99033212A');
        $this->addSql('ALTER TABLE users ADD agency_id UUID DEFAULT NULL');
        $this->addSql('ALTER TABLE users ALTER role DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN users.agency_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE users ADD CONSTRAINT FK_1483A5E9CDEADB2A FOREIGN KEY (agency_id) REFERENCES agencies (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE users ADD CONSTRAINT FK_1483A5E932C8A3DE FOREIGN KEY (organization_id) REFERENCES organizations (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE users ADD CONSTRAINT FK_1483A5E99033212A FOREIGN KEY (tenant_id) REFERENCES tenants (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_1483A5E9CDEADB2A ON users (agency_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE clients DROP CONSTRAINT FK_C82E74CDEADB2A');
        $this->addSql('ALTER TABLE users DROP CONSTRAINT FK_1483A5E9CDEADB2A');
        $this->addSql('DROP TABLE agencies');
        $this->addSql('ALTER TABLE users DROP CONSTRAINT fk_1483a5e932c8a3de');
        $this->addSql('ALTER TABLE users DROP CONSTRAINT fk_1483a5e99033212a');
        $this->addSql('DROP INDEX IDX_1483A5E9CDEADB2A');
        $this->addSql('ALTER TABLE users DROP agency_id');
        $this->addSql('ALTER TABLE users ALTER role SET DEFAULT \'ROLE_USER\'');
        $this->addSql('ALTER TABLE users ADD CONSTRAINT fk_1483a5e932c8a3de FOREIGN KEY (organization_id) REFERENCES organizations (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE users ADD CONSTRAINT fk_1483a5e99033212a FOREIGN KEY (tenant_id) REFERENCES tenants (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('DROP INDEX IDX_C82E74CDEADB2A');
        $this->addSql('ALTER TABLE clients RENAME COLUMN agency_id TO organization_id');
        $this->addSql('ALTER TABLE clients ADD CONSTRAINT fk_c82e7432c8a3de FOREIGN KEY (organization_id) REFERENCES organizations (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_c82e7432c8a3de ON clients (organization_id)');
    }
}
