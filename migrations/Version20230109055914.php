<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230109055914 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE competition (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE team (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE team_competition (team_id INT NOT NULL, competition_id INT NOT NULL, INDEX IDX_5E3AC3FC296CD8AE (team_id), INDEX IDX_5E3AC3FC7B39D312 (competition_id), PRIMARY KEY(team_id, competition_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE team_competition ADD CONSTRAINT FK_5E3AC3FC296CD8AE FOREIGN KEY (team_id) REFERENCES team (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE team_competition ADD CONSTRAINT FK_5E3AC3FC7B39D312 FOREIGN KEY (competition_id) REFERENCES competition (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE team_competition DROP FOREIGN KEY FK_5E3AC3FC296CD8AE');
        $this->addSql('ALTER TABLE team_competition DROP FOREIGN KEY FK_5E3AC3FC7B39D312');
        $this->addSql('DROP TABLE competition');
        $this->addSql('DROP TABLE team');
        $this->addSql('DROP TABLE team_competition');
    }
}
