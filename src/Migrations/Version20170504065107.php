<?php

namespace DoctrineMigrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20170504065107 extends AbstractMigration {
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('UPDATE users SET locale = \'en\' WHERE locale IS NULL');
        $this->addSql('ALTER TABLE users ALTER locale SET NOT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE users ALTER locale DROP NOT NULL');
    }
}
