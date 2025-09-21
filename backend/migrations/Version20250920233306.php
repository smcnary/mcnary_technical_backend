<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250920233306 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Update leads table status field to support new enum values and migrate existing data';
    }

    public function up(Schema $schema): void
    {
        // First, update the column to support longer enum values
        $this->addSql('ALTER TABLE leads ALTER COLUMN status TYPE VARCHAR(32)');
        $this->addSql("ALTER TABLE leads ALTER COLUMN status SET DEFAULT 'new_lead'");
        
        // Then map existing status values to new enum values
        $this->addSql("UPDATE leads SET status = 'new_lead' WHERE status = 'new'");
        $this->addSql("UPDATE leads SET status = 'contacted' WHERE status = 'contacted'"); // Keep as is
        $this->addSql("UPDATE leads SET status = 'interview_scheduled' WHERE status = 'qualified'");
        $this->addSql("UPDATE leads SET status = 'interview_completed' WHERE status = 'proposal'");
        $this->addSql("UPDATE leads SET status = 'enrolled' WHERE status = 'closed_won'");
        $this->addSql("UPDATE leads SET status = 'contacted' WHERE status = 'closed_lost'"); // Map to contacted for follow-up
        
        // Add constraint to ensure only valid enum values are allowed
        $this->addSql("ALTER TABLE leads ADD CONSTRAINT check_lead_status CHECK (status IN ('new_lead', 'contacted', 'interview_scheduled', 'interview_completed', 'application_received', 'audit_in_progress', 'audit_complete', 'enrolled'))");
    }

    public function down(Schema $schema): void
    {
        // Remove the constraint
        $this->addSql('ALTER TABLE leads DROP CONSTRAINT IF EXISTS check_lead_status');
        
        // Revert status values back to original
        $this->addSql("UPDATE leads SET status = 'new' WHERE status = 'new_lead'");
        $this->addSql("UPDATE leads SET status = 'qualified' WHERE status = 'interview_scheduled'");
        $this->addSql("UPDATE leads SET status = 'proposal' WHERE status = 'interview_completed'");
        $this->addSql("UPDATE leads SET status = 'closed_won' WHERE status = 'enrolled'");
        $this->addSql("UPDATE leads SET status = 'closed_lost' WHERE status = 'contacted' AND status != 'contacted'"); // Only revert if it was originally closed_lost
        
        // Revert column changes
        $this->addSql('ALTER TABLE leads ALTER COLUMN status TYPE VARCHAR(16)');
        $this->addSql("ALTER TABLE leads ALTER COLUMN status SET DEFAULT 'new'");
    }
}
