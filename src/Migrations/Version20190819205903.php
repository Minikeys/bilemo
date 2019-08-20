<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190819205903 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE UNIQUE INDEX UNIQ_D34A04AD5E237E06 ON product (name)');
        $this->addSql('ALTER TABLE customer ADD lastname VARCHAR(255) NOT NULL, ADD address LONGTEXT NOT NULL, ADD postalcode INT NOT NULL, ADD city VARCHAR(255) NOT NULL, ADD country VARCHAR(255) NOT NULL, CHANGE name firstname VARCHAR(255) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_81398E0983A00E68 ON customer (firstname)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX UNIQ_81398E0983A00E68 ON customer');
        $this->addSql('ALTER TABLE customer ADD name VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, DROP firstname, DROP lastname, DROP address, DROP postalcode, DROP city, DROP country');
        $this->addSql('DROP INDEX UNIQ_D34A04AD5E237E06 ON product');
    }
}
