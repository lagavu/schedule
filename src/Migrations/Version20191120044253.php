<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191120044253 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE party (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, start_day_party DATE NOT NULL, end_day_party DATE NOT NULL, start_time_party TIME NOT NULL, end_time_party TIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('DROP TABLE party_parties');
        $this->addSql('ALTER TABLE vacation CHANGE user_id user_id INT NOT NULL, CHANGE start_vacation start_vacation DATE NOT NULL, CHANGE end_vacation end_vacation DATE NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE party_parties (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, party_day_from DATE NOT NULL, party_day_before DATE NOT NULL, party_time_from TIME NOT NULL, party_time_before TIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('DROP TABLE party');
        $this->addSql('ALTER TABLE vacation CHANGE user_id user_id INT DEFAULT NULL, CHANGE start_vacation start_vacation DATE DEFAULT NULL, CHANGE end_vacation end_vacation DATE DEFAULT NULL');
    }
}
