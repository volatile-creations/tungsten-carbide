<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240309172919 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add link between user and guest';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user ADD self_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649C35AA590 FOREIGN KEY (self_id) REFERENCES guest (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649C35AA590 ON user (self_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649C35AA590');
        $this->addSql('DROP INDEX UNIQ_8D93D649C35AA590 ON user');
        $this->addSql('ALTER TABLE user DROP self_id');
    }
}
