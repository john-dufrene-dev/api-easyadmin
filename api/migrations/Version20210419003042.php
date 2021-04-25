<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210419003042 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user_reset_password (id INT AUTO_INCREMENT NOT NULL, user_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', secret VARCHAR(40) NOT NULL, hash_token VARCHAR(255) NOT NULL, requested_at DATETIME NOT NULL, expired_at DATETIME NOT NULL, INDEX IDX_D21DE3BCA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_reset_password ADD CONSTRAINT FK_D21DE3BCA76ED395 FOREIGN KEY (user_id) REFERENCES user (uuid)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE user_reset_password');
    }
}
