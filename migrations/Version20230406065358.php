<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230406065358 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE work (id INT AUTO_INCREMENT NOT NULL, description LONGTEXT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE work_work_tag (work_id INT NOT NULL, work_tag_id INT NOT NULL, INDEX IDX_AC526A43BB3453DB (work_id), INDEX IDX_AC526A43112BA533 (work_tag_id), PRIMARY KEY(work_id, work_tag_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE work_tag (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE work_work_tag ADD CONSTRAINT FK_AC526A43BB3453DB FOREIGN KEY (work_id) REFERENCES work (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE work_work_tag ADD CONSTRAINT FK_AC526A43112BA533 FOREIGN KEY (work_tag_id) REFERENCES work_tag (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE product CHANGE price price NUMERIC(12, 2) DEFAULT \'0\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE work_work_tag DROP FOREIGN KEY FK_AC526A43BB3453DB');
        $this->addSql('ALTER TABLE work_work_tag DROP FOREIGN KEY FK_AC526A43112BA533');
        $this->addSql('DROP TABLE work');
        $this->addSql('DROP TABLE work_work_tag');
        $this->addSql('DROP TABLE work_tag');
        $this->addSql('ALTER TABLE product CHANGE price price NUMERIC(12, 2) DEFAULT \'0.00\'');
    }
}
