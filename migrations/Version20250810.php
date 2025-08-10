<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250810 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Enable pgcrypto, citext; align DB functions and indices for mcnary_seo.sql';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("CREATE EXTENSION IF NOT EXISTS pgcrypto;");
        $this->addSql("CREATE EXTENSION IF NOT EXISTS citext;");
    }

    public function down(Schema $schema): void
    {
        // keep extensions
    }
}
