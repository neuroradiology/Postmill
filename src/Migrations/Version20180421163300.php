<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20180421163300 extends AbstractMigration {
    public function up(Schema $schema) {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE forum_categories ADD description TEXT');
        $this->addSql('UPDATE forum_categories SET description = title');
        $this->addSql('ALTER TABLE forum_categories ALTER description SET NOT NULL');
        $this->addSql('ALTER TABLE forum_categories ADD sidebar TEXT');
        $this->addSql('UPDATE forum_categories SET sidebar = title');
        $this->addSql('ALTER TABLE forum_categories ALTER sidebar SET NOT NULL');
    }

    public function down(Schema $schema) {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE forum_categories DROP description');
        $this->addSql('ALTER TABLE forum_categories DROP sidebar');
    }
}
