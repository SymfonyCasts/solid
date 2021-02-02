<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190919170603 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE big_foot_sighting ADD owner_id INT NOT NULL');
        $this->addSql('ALTER TABLE big_foot_sighting ADD CONSTRAINT FK_4CA856637E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_4CA856637E3C61F9 ON big_foot_sighting (owner_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE big_foot_sighting DROP FOREIGN KEY FK_4CA856637E3C61F9');
        $this->addSql('DROP INDEX IDX_4CA856637E3C61F9 ON big_foot_sighting');
        $this->addSql('ALTER TABLE big_foot_sighting DROP owner_id');
    }
}
