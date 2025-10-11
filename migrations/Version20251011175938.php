<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251011175938 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE buurt (uuid UUID NOT NULL, woonplaats_uuid UUID DEFAULT NULL, wijk_uuid UUID DEFAULT NULL, code VARCHAR(25) DEFAULT NULL, naam VARCHAR(75) DEFAULT NULL, match_naam VARCHAR(75) DEFAULT NULL, geo_center TEXT DEFAULT NULL, gemeente_code VARCHAR(25) DEFAULT NULL, gemeente_naam VARCHAR(75) DEFAULT NULL, in_pdok BOOLEAN NOT NULL, last_seen_in_pdok TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, in_mor_core BOOLEAN NOT NULL, last_seen_in_mor_core TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(uuid))');
        $this->addSql('CREATE INDEX IDX_616A8774C56E82CB ON buurt (woonplaats_uuid)');
        $this->addSql('CREATE INDEX IDX_616A8774BAE23AB0 ON buurt (wijk_uuid)');
        $this->addSql('CREATE INDEX idx_buurt_naam ON buurt (naam)');
        $this->addSql('CREATE UNIQUE INDEX uq_buurt_matchnaam ON buurt (match_naam)');
        $this->addSql('CREATE UNIQUE INDEX uq_buurt_code ON buurt (code)');
        $this->addSql('COMMENT ON COLUMN buurt.uuid IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN buurt.woonplaats_uuid IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN buurt.wijk_uuid IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN buurt.last_seen_in_pdok IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN buurt.last_seen_in_mor_core IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE wijk (uuid UUID NOT NULL, woonplaats_uuid UUID DEFAULT NULL, code VARCHAR(25) DEFAULT NULL, naam VARCHAR(75) DEFAULT NULL, match_naam VARCHAR(75) DEFAULT NULL, geo_center TEXT DEFAULT NULL, gemeente_code VARCHAR(25) DEFAULT NULL, gemeente_naam VARCHAR(75) DEFAULT NULL, in_pdok BOOLEAN NOT NULL, last_seen_in_pdok TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, in_mor_core BOOLEAN NOT NULL, last_seen_in_mor_core TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(uuid))');
        $this->addSql('CREATE INDEX IDX_D5D88C6BC56E82CB ON wijk (woonplaats_uuid)');
        $this->addSql('CREATE INDEX idx_wijk_naam ON wijk (naam)');
        $this->addSql('CREATE UNIQUE INDEX uq_wijk_matchnaam ON wijk (match_naam)');
        $this->addSql('CREATE UNIQUE INDEX uq_wijk_code ON wijk (code)');
        $this->addSql('COMMENT ON COLUMN wijk.uuid IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN wijk.woonplaats_uuid IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN wijk.last_seen_in_pdok IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN wijk.last_seen_in_mor_core IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE woonplaats (uuid UUID NOT NULL, code VARCHAR(25) DEFAULT NULL, naam VARCHAR(75) DEFAULT NULL, match_naam VARCHAR(75) DEFAULT NULL, geo_center TEXT DEFAULT NULL, gemeente_code VARCHAR(25) DEFAULT NULL, gemeente_naam VARCHAR(75) DEFAULT NULL, in_pdok BOOLEAN NOT NULL, last_seen_in_pdok TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, in_mor_core BOOLEAN NOT NULL, last_seen_in_mor_core TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(uuid))');
        $this->addSql('CREATE INDEX idx_woonplaats_naam ON woonplaats (naam)');
        $this->addSql('CREATE UNIQUE INDEX uq_woonplaats_matchnaam ON woonplaats (match_naam)');
        $this->addSql('CREATE UNIQUE INDEX uq_woonplaats_code ON woonplaats (code)');
        $this->addSql('COMMENT ON COLUMN woonplaats.uuid IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN woonplaats.last_seen_in_pdok IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN woonplaats.last_seen_in_mor_core IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE buurt ADD CONSTRAINT FK_616A8774C56E82CB FOREIGN KEY (woonplaats_uuid) REFERENCES woonplaats (uuid) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE buurt ADD CONSTRAINT FK_616A8774BAE23AB0 FOREIGN KEY (wijk_uuid) REFERENCES wijk (uuid) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE wijk ADD CONSTRAINT FK_D5D88C6BC56E82CB FOREIGN KEY (woonplaats_uuid) REFERENCES woonplaats (uuid) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE buurt DROP CONSTRAINT FK_616A8774C56E82CB');
        $this->addSql('ALTER TABLE buurt DROP CONSTRAINT FK_616A8774BAE23AB0');
        $this->addSql('ALTER TABLE wijk DROP CONSTRAINT FK_D5D88C6BC56E82CB');
        $this->addSql('DROP TABLE buurt');
        $this->addSql('DROP TABLE wijk');
        $this->addSql('DROP TABLE woonplaats');
    }
}
