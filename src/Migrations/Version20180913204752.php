<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180913204752 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('CREATE TABLE block (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, block_chain_id INTEGER UNSIGNED NOT NULL, previous_block_id INTEGER UNSIGNED DEFAULT NULL, idx INTEGER UNSIGNED NOT NULL, created_at DATETIME NOT NULL, data VARCHAR(2048) DEFAULT NULL, hash VARCHAR(255) NOT NULL, previous_block_hash VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE INDEX IDX_831B9722CC13D38D ON block (block_chain_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_831B9722F6069A08 ON block (previous_block_id)');
        $this->addSql('CREATE TABLE blockchain (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, uuid VARCHAR(36) NOT NULL, active BOOLEAN NOT NULL, created_at DATETIME NOT NULL)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('DROP TABLE block');
        $this->addSql('DROP TABLE blockchain');
    }
}
