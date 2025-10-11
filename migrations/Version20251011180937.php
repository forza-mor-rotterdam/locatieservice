<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251011180937 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE buurt ALTER last_seen_in_pdok DROP NOT NULL');
        $this->addSql('ALTER TABLE buurt ALTER last_seen_in_mor_core DROP NOT NULL');
        $this->addSql('ALTER TABLE wijk ALTER last_seen_in_pdok DROP NOT NULL');
        $this->addSql('ALTER TABLE wijk ALTER last_seen_in_mor_core DROP NOT NULL');
        $this->addSql('ALTER TABLE woonplaats ALTER last_seen_in_pdok DROP NOT NULL');
        $this->addSql('ALTER TABLE woonplaats ALTER last_seen_in_mor_core DROP NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE buurt ALTER last_seen_in_pdok SET NOT NULL');
        $this->addSql('ALTER TABLE buurt ALTER last_seen_in_mor_core SET NOT NULL');
        $this->addSql('ALTER TABLE woonplaats ALTER last_seen_in_pdok SET NOT NULL');
        $this->addSql('ALTER TABLE woonplaats ALTER last_seen_in_mor_core SET NOT NULL');
        $this->addSql('ALTER TABLE wijk ALTER last_seen_in_pdok SET NOT NULL');
        $this->addSql('ALTER TABLE wijk ALTER last_seen_in_mor_core SET NOT NULL');
    }
}
