<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230106074418 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE login_history CHANGE status status SMALLINT NOT NULL');
        $this->addSql('ALTER TABLE product ADD deleted_at DATETIME DEFAULT NULL, CHANGE price price NUMERIC(12, 2) DEFAULT \'0\'');
        $this->addSql('ALTER TABLE task RENAME INDEX fk_527edb25543330d0 TO IDX_527EDB25543330D0');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE login_history CHANGE status status SMALLINT DEFAULT 0 NOT NULL COMMENT \'0 - active, 1 - inactive\'');
        $this->addSql('ALTER TABLE product DROP deleted_at, CHANGE price price NUMERIC(12, 2) DEFAULT \'0.00\'');
        $this->addSql('ALTER TABLE task RENAME INDEX idx_527edb25543330d0 TO FK_527EDB25543330D0');
    }
}
