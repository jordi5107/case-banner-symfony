<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260408085921 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE banner (id INT AUTO_INCREMENT NOT NULL, internal_name VARCHAR(255) NOT NULL, background_color VARCHAR(7) NOT NULL, start_date DATETIME DEFAULT NULL, end_date DATETIME DEFAULT NULL, active TINYINT NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE banner_translation (id INT AUTO_INCREMENT NOT NULL, locale VARCHAR(10) NOT NULL, content LONGTEXT NOT NULL, active_lang TINYINT NOT NULL, banner_id INT NOT NULL, INDEX IDX_841ECF1C684EC833 (banner_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 (queue_name, available_at, delivered_at, id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE banner_translation ADD CONSTRAINT FK_841ECF1C684EC833 FOREIGN KEY (banner_id) REFERENCES banner (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE banner_translation DROP FOREIGN KEY FK_841ECF1C684EC833');
        $this->addSql('DROP TABLE banner');
        $this->addSql('DROP TABLE banner_translation');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
