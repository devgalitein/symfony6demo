<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230410053816 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE neighborhood (id INT AUTO_INCREMENT NOT NULL, city_id INT NOT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_FEF1E9EE8BAC62AF (city_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE person (id INT AUTO_INCREMENT NOT NULL, city_id INT NOT NULL, neighborhood_id INT NOT NULL, name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, INDEX IDX_34DCD1768BAC62AF (city_id), INDEX IDX_34DCD176803BB24B (neighborhood_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE neighborhood ADD CONSTRAINT FK_FEF1E9EE8BAC62AF FOREIGN KEY (city_id) REFERENCES city (id)');
        $this->addSql('ALTER TABLE person ADD CONSTRAINT FK_34DCD1768BAC62AF FOREIGN KEY (city_id) REFERENCES city (id)');
        $this->addSql('ALTER TABLE person ADD CONSTRAINT FK_34DCD176803BB24B FOREIGN KEY (neighborhood_id) REFERENCES neighborhood (id)');
        $this->addSql('ALTER TABLE product CHANGE price price NUMERIC(12, 2) DEFAULT \'0\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE neighborhood DROP FOREIGN KEY FK_FEF1E9EE8BAC62AF');
        $this->addSql('ALTER TABLE person DROP FOREIGN KEY FK_34DCD1768BAC62AF');
        $this->addSql('ALTER TABLE person DROP FOREIGN KEY FK_34DCD176803BB24B');
        $this->addSql('DROP TABLE neighborhood');
        $this->addSql('DROP TABLE person');
        $this->addSql('ALTER TABLE product CHANGE price price NUMERIC(12, 2) DEFAULT \'0.00\'');
    }
}
