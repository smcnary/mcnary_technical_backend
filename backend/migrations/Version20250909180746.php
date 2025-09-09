<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250909180746 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE audit_run DROP tool');
        $this->addSql('ALTER TABLE audit_run DROP priority');
        $this->addSql('ALTER TABLE audit_run DROP notify_email');
        $this->addSql('ALTER TABLE audit_run DROP findings_count');
        $this->addSql('ALTER TABLE audit_run DROP metadata');
        $this->addSql('ALTER TABLE audit_run DROP created_at');
        $this->addSql('ALTER TABLE audit_run DROP updated_at');
        $this->addSql('ALTER TABLE audit_run ALTER intake_id SET NOT NULL');
        $this->addSql('ALTER TABLE leads DROP CONSTRAINT FK_17904552953C1C61');
        $this->addSql('ALTER TABLE leads ADD CONSTRAINT FK_17904552953C1C61 FOREIGN KEY (source_id) REFERENCES lead_sources (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE oauth_connections DROP CONSTRAINT FK_E3D750BE19EB6921');
        $this->addSql('ALTER TABLE oauth_connections ADD user_id UUID DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN oauth_connections.user_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE oauth_connections ADD CONSTRAINT FK_E3D750BEA76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE oauth_connections ADD CONSTRAINT FK_E3D750BE19EB6921 FOREIGN KEY (client_id) REFERENCES clients (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_E3D750BEA76ED395 ON oauth_connections (user_id)');
        $this->addSql('ALTER TABLE oauth_tokens DROP CONSTRAINT FK_C06D3296DD03F01');
        $this->addSql('ALTER TABLE oauth_tokens ADD user_id UUID DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN oauth_tokens.user_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE oauth_tokens ADD CONSTRAINT FK_C06D3296A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE oauth_tokens ADD CONSTRAINT FK_C06D3296DD03F01 FOREIGN KEY (connection_id) REFERENCES oauth_connections (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_C06D3296A76ED395 ON oauth_tokens (user_id)');
        $this->addSql('ALTER TABLE rankings_daily DROP CONSTRAINT FK_B0D35353115D4552');
        $this->addSql('ALTER TABLE rankings_daily ADD CONSTRAINT FK_B0D35353115D4552 FOREIGN KEY (keyword_id) REFERENCES keywords (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_client_access DROP CONSTRAINT FK_BD41CDB719EB6921');
        $this->addSql('ALTER TABLE user_client_access DROP CONSTRAINT FK_BD41CDB7A76ED395');
        $this->addSql('ALTER TABLE user_client_access ADD CONSTRAINT FK_BD41CDB719EB6921 FOREIGN KEY (client_id) REFERENCES clients (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_client_access ADD CONSTRAINT FK_BD41CDB7A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE user_client_access DROP CONSTRAINT fk_bd41cdb7a76ed395');
        $this->addSql('ALTER TABLE user_client_access DROP CONSTRAINT fk_bd41cdb719eb6921');
        $this->addSql('ALTER TABLE user_client_access ADD CONSTRAINT fk_bd41cdb7a76ed395 FOREIGN KEY (user_id) REFERENCES clients (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_client_access ADD CONSTRAINT fk_bd41cdb719eb6921 FOREIGN KEY (client_id) REFERENCES users (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE audit_run ADD tool VARCHAR(50) DEFAULT NULL');
        $this->addSql('ALTER TABLE audit_run ADD priority VARCHAR(20) DEFAULT \'medium\' NOT NULL');
        $this->addSql('ALTER TABLE audit_run ADD notify_email VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE audit_run ADD findings_count INT DEFAULT 0 NOT NULL');
        $this->addSql('ALTER TABLE audit_run ADD metadata JSON DEFAULT NULL');
        $this->addSql('ALTER TABLE audit_run ADD created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL');
        $this->addSql('ALTER TABLE audit_run ADD updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL');
        $this->addSql('ALTER TABLE audit_run ALTER intake_id DROP NOT NULL');
        $this->addSql('COMMENT ON COLUMN audit_run.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN audit_run.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE rankings_daily DROP CONSTRAINT fk_b0d35353115d4552');
        $this->addSql('ALTER TABLE rankings_daily ADD CONSTRAINT fk_b0d35353115d4552 FOREIGN KEY (keyword_id) REFERENCES clients (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE oauth_tokens DROP CONSTRAINT FK_C06D3296A76ED395');
        $this->addSql('ALTER TABLE oauth_tokens DROP CONSTRAINT fk_c06d3296dd03f01');
        $this->addSql('DROP INDEX IDX_C06D3296A76ED395');
        $this->addSql('ALTER TABLE oauth_tokens DROP user_id');
        $this->addSql('ALTER TABLE oauth_tokens ADD CONSTRAINT fk_c06d3296dd03f01 FOREIGN KEY (connection_id) REFERENCES users (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE oauth_connections DROP CONSTRAINT FK_E3D750BEA76ED395');
        $this->addSql('ALTER TABLE oauth_connections DROP CONSTRAINT fk_e3d750be19eb6921');
        $this->addSql('DROP INDEX IDX_E3D750BEA76ED395');
        $this->addSql('ALTER TABLE oauth_connections DROP user_id');
        $this->addSql('ALTER TABLE oauth_connections ADD CONSTRAINT fk_e3d750be19eb6921 FOREIGN KEY (client_id) REFERENCES users (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE leads DROP CONSTRAINT fk_17904552953c1c61');
        $this->addSql('ALTER TABLE leads ADD CONSTRAINT fk_17904552953c1c61 FOREIGN KEY (source_id) REFERENCES clients (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }
}
