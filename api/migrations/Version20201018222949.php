<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201018222949 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE shop_file (id INT AUTO_INCREMENT NOT NULL, shop_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', image_name VARCHAR(255) DEFAULT NULL, original_name VARCHAR(255) DEFAULT NULL, image_size INT DEFAULT NULL, mime_type VARCHAR(255) DEFAULT NULL, dimensions LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:simple_array)\', created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_4D6DD06B4D16C4DD (shop_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE shop_file ADD CONSTRAINT FK_4D6DD06B4D16C4DD FOREIGN KEY (shop_id) REFERENCES shop (uuid)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE shop_file');
    }
}
