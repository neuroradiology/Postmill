<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

final class Version20180624000919 extends AbstractMigration {
    public function up(Schema $schema): void {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE forums DROP CONSTRAINT fk_fe5e5ab859027487');
        $this->addSql('ALTER TABLE users DROP CONSTRAINT fk_1483a5e94fc448e8');
        $this->addSql('ALTER TABLE theme_revisions DROP CONSTRAINT fk_4772f80859027487');
        $this->addSql('ALTER TABLE theme_revisions DROP CONSTRAINT fk_4772f808727aca70');
        $this->addSql('DROP TABLE themes');
        $this->addSql('DROP TABLE theme_revisions');
        $this->addSql('DROP INDEX idx_1483a5e94fc448e8');
        $this->addSql('ALTER TABLE users DROP preferred_theme_id');
        $this->addSql('ALTER TABLE users DROP show_custom_stylesheets');
        $this->addSql('DROP INDEX idx_fe5e5ab859027487');
        $this->addSql('ALTER TABLE forums DROP theme_id');
    }

    public function down(Schema $schema): void {
        $this->throwIrreversibleMigrationException();
    }
}
