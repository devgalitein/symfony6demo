<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230407103837 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product CHANGE price price NUMERIC(12, 2) DEFAULT \'0\'');
        $this->addSql('ALTER TABLE product_variation CHANGE product_id product_id INT DEFAULT NULL, CHANGE color_id color_id INT DEFAULT NULL, CHANGE size_id size_id INT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product CHANGE price price NUMERIC(12, 2) DEFAULT \'0.00\'');
        $this->addSql('ALTER TABLE product_variation CHANGE product_id product_id INT NOT NULL, CHANGE color_id color_id INT NOT NULL, CHANGE size_id size_id INT NOT NULL');
    }
}
