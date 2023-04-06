<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230406110822 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE product_variation (id INT AUTO_INCREMENT NOT NULL, product_id INT NOT NULL, color_id INT NOT NULL, size_id INT NOT NULL, INDEX IDX_C3B85674584665A (product_id), INDEX IDX_C3B85677ADA1FB5 (color_id), INDEX IDX_C3B8567498DA827 (size_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE product_variation ADD CONSTRAINT FK_C3B85674584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE product_variation ADD CONSTRAINT FK_C3B85677ADA1FB5 FOREIGN KEY (color_id) REFERENCES colors (id)');
        $this->addSql('ALTER TABLE product_variation ADD CONSTRAINT FK_C3B8567498DA827 FOREIGN KEY (size_id) REFERENCES size (id)');
        $this->addSql('ALTER TABLE product CHANGE price price NUMERIC(12, 2) DEFAULT \'0\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product_variation DROP FOREIGN KEY FK_C3B85674584665A');
        $this->addSql('ALTER TABLE product_variation DROP FOREIGN KEY FK_C3B85677ADA1FB5');
        $this->addSql('ALTER TABLE product_variation DROP FOREIGN KEY FK_C3B8567498DA827');
        $this->addSql('DROP TABLE product_variation');
        $this->addSql('ALTER TABLE product CHANGE price price NUMERIC(12, 2) DEFAULT \'0.00\'');
    }
}
