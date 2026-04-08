<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260408155256 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE locale (id INT AUTO_INCREMENT NOT NULL, code VARCHAR(10) NOT NULL, active TINYINT NOT NULL, UNIQUE INDEX UNIQ_4180C69877153098 (code), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE banner_translation ADD locale_id INT NOT NULL, DROP locale, DROP active_lang');
        $this->addSql('ALTER TABLE banner_translation ADD CONSTRAINT FK_841ECF1CE559DFD1 FOREIGN KEY (locale_id) REFERENCES locale (id)');
        $this->addSql('CREATE INDEX IDX_841ECF1CE559DFD1 ON banner_translation (locale_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE locale');
        $this->addSql('ALTER TABLE banner_translation DROP FOREIGN KEY FK_841ECF1CE559DFD1');
        $this->addSql('DROP INDEX IDX_841ECF1CE559DFD1 ON banner_translation');
        $this->addSql('ALTER TABLE banner_translation ADD locale VARCHAR(10) NOT NULL, ADD active_lang TINYINT NOT NULL, DROP locale_id');
    }
}
