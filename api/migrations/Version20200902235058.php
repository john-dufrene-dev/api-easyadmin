<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200902235058 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE admin_group (uuid BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', reference VARCHAR(40) NOT NULL, name VARCHAR(255) NOT NULL, roles LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\', created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, id INT AUTO_INCREMENT NOT NULL, UNIQUE INDEX UNIQ_CDEABF3FAEA34913 (reference), UNIQUE INDEX UNIQ_CDEABF3FBF396750 (id), PRIMARY KEY(uuid)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE admin (uuid BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', reference VARCHAR(40) NOT NULL, email VARCHAR(180) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, is_admin TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, id INT AUTO_INCREMENT NOT NULL, UNIQUE INDEX UNIQ_880E0D76AEA34913 (reference), UNIQUE INDEX UNIQ_880E0D76E7927C74 (email), UNIQUE INDEX UNIQ_880E0D76BF396750 (id), PRIMARY KEY(uuid)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE admin_admin_group (admin_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', group_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', INDEX IDX_50F0F636642B8210 (admin_id), INDEX IDX_50F0F636FE54D947 (group_id), PRIMARY KEY(admin_id, group_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE admin_admin_group ADD CONSTRAINT FK_50F0F636642B8210 FOREIGN KEY (admin_id) REFERENCES admin (uuid)');
        $this->addSql('ALTER TABLE admin_admin_group ADD CONSTRAINT FK_50F0F636FE54D947 FOREIGN KEY (group_id) REFERENCES admin_group (uuid)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE admin_admin_group DROP FOREIGN KEY FK_50F0F636642B8210');
        $this->addSql('ALTER TABLE admin_admin_group DROP FOREIGN KEY FK_50F0F636FE54D947');
        $this->addSql('DROP TABLE admin');
        $this->addSql('DROP TABLE admin_admin_group');
        $this->addSql('DROP TABLE admin_group');
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
