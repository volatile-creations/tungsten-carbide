<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240309182745 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE event_guest (event_guest VARCHAR(20) NOT NULL, guest_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', INDEX IDX_EDAC2B19EDAC2B19 (event_guest), INDEX IDX_EDAC2B199A4AA658 (guest_id), PRIMARY KEY(event_guest, guest_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE event_guest ADD CONSTRAINT FK_EDAC2B19EDAC2B19 FOREIGN KEY (event_guest) REFERENCES event (name)');
        $this->addSql('ALTER TABLE event_guest ADD CONSTRAINT FK_EDAC2B199A4AA658 FOREIGN KEY (guest_id) REFERENCES guest (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE event_guest DROP FOREIGN KEY FK_EDAC2B19EDAC2B19');
        $this->addSql('ALTER TABLE event_guest DROP FOREIGN KEY FK_EDAC2B199A4AA658');
        $this->addSql('DROP TABLE event_guest');
    }
}
