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
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE shop_info (uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', country VARCHAR(180) DEFAULT NULL, shop_hour LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\', shipping_click TINYINT(1) NOT NULL, shipping_delivery TINYINT(1) NOT NULL, id INT AUTO_INCREMENT NOT NULL, UNIQUE INDEX UNIQ_A7BD72CBF396750 (id), PRIMARY KEY(uuid)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE shop ADD shop_info_uuid CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE shop ADD CONSTRAINT FK_AC6A4CA291A30CA3 FOREIGN KEY (shop_info_uuid) REFERENCES shop_info (uuid)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_AC6A4CA291A30CA3 ON shop (shop_info_uuid)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE shop DROP FOREIGN KEY FK_AC6A4CA291A30CA3');
        $this->addSql('DROP TABLE shop_info');
        $this->addSql('DROP INDEX UNIQ_AC6A4CA291A30CA3 ON shop');
        $this->addSql('ALTER TABLE shop DROP shop_info_uuid, CHANGE id id INT AUTO_INCREMENT NOT NULL');
    }
}
