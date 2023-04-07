<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230407101123 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product CHANGE price price NUMERIC(12, 2) DEFAULT \'0\'');
        $this->addSql('ALTER TABLE product_variation DROP FOREIGN KEY FK_C3B85677ADA1FB5');
        $this->addSql('ALTER TABLE product_variation DROP FOREIGN KEY FK_C3B85674584665A');
        $this->addSql('ALTER TABLE product_variation DROP FOREIGN KEY FK_C3B8567498DA827');
        $this->addSql('ALTER TABLE product_variation CHANGE product_id product_id INT NOT NULL');
        $this->addSql('ALTER TABLE product_variation ADD CONSTRAINT FK_C3B85677ADA1FB5 FOREIGN KEY (color_id) REFERENCES colors (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE product_variation ADD CONSTRAINT FK_C3B85674584665A FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE product_variation ADD CONSTRAINT FK_C3B8567498DA827 FOREIGN KEY (size_id) REFERENCES size (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product CHANGE price price NUMERIC(12, 2) DEFAULT \'0.00\'');
        $this->addSql('ALTER TABLE product_variation DROP FOREIGN KEY FK_C3B85674584665A');
        $this->addSql('ALTER TABLE product_variation DROP FOREIGN KEY FK_C3B85677ADA1FB5');
        $this->addSql('ALTER TABLE product_variation DROP FOREIGN KEY FK_C3B8567498DA827');
        $this->addSql('ALTER TABLE product_variation CHANGE product_id product_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE product_variation ADD CONSTRAINT FK_C3B85674584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE product_variation ADD CONSTRAINT FK_C3B85677ADA1FB5 FOREIGN KEY (color_id) REFERENCES colors (id)');
        $this->addSql('ALTER TABLE product_variation ADD CONSTRAINT FK_C3B8567498DA827 FOREIGN KEY (size_id) REFERENCES size (id)');
    }
}
