<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201005223416 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE shop_info (id INT AUTO_INCREMENT NOT NULL, shop_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', country VARCHAR(180) DEFAULT NULL, shop_hour LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\', shipping_click TINYINT(1) NOT NULL, shipping_delivery TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_A7BD72C4D16C4DD (shop_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE shop_info ADD CONSTRAINT FK_A7BD72C4D16C4DD FOREIGN KEY (shop_id) REFERENCES shop (uuid)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE shop_info DROP FOREIGN KEY FK_A7BD72C4D16C4DD');
        $this->addSql('DROP TABLE shop_info');
    }
}
