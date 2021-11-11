<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210501185043 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE admin_config (id INT AUTO_INCREMENT NOT NULL, admin_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', dashboard_title VARCHAR(255) DEFAULT NULL, crud_paginator SMALLINT DEFAULT NULL, UNIQUE INDEX UNIQ_89421E85642B8210 (admin_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE admin_config ADD CONSTRAINT FK_89421E85642B8210 FOREIGN KEY (admin_id) REFERENCES admin (uuid)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE admin_config');
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
