<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251011181227 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX uq_buurt_code');
        $this->addSql('CREATE INDEX idx_buurt_code ON buurt (code)');
        $this->addSql('DROP INDEX uq_wijk_code');
        $this->addSql('CREATE INDEX idx_wijk_code ON wijk (code)');
        $this->addSql('DROP INDEX uq_woonplaats_code');
        $this->addSql('CREATE INDEX idx_woonplaats_code ON woonplaats (code)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP INDEX idx_buurt_code');
        $this->addSql('CREATE UNIQUE INDEX uq_buurt_code ON buurt (code)');
        $this->addSql('DROP INDEX idx_woonplaats_code');
        $this->addSql('CREATE UNIQUE INDEX uq_woonplaats_code ON woonplaats (code)');
        $this->addSql('DROP INDEX idx_wijk_code');
        $this->addSql('CREATE UNIQUE INDEX uq_wijk_code ON wijk (code)');
    }
}
