<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200920215704 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE shop (uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', name VARCHAR(255) NOT NULL, email VARCHAR(180) NOT NULL, is_active TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, id INT AUTO_INCREMENT NOT NULL, UNIQUE INDEX UNIQ_AC6A4CA2E7927C74 (email), UNIQUE INDEX UNIQ_AC6A4CA2BF396750 (id), PRIMARY KEY(uuid)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE shop_admin (shop_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', admin_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', INDEX IDX_4F1857244D16C4DD (shop_id), INDEX IDX_4F185724642B8210 (admin_id), PRIMARY KEY(shop_id, admin_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE shop_admin ADD CONSTRAINT FK_4F1857244D16C4DD FOREIGN KEY (shop_id) REFERENCES shop (uuid)');
        $this->addSql('ALTER TABLE shop_admin ADD CONSTRAINT FK_4F185724642B8210 FOREIGN KEY (admin_id) REFERENCES admin (uuid)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE shop_admin DROP FOREIGN KEY FK_4F1857244D16C4DD');
        $this->addSql('DROP TABLE shop');
        $this->addSql('DROP TABLE shop_admin');
    }
}
