<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191112085938 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE user_users (id INT AUTO_INCREMENT NOT NULL, morning_work_hours_from TIME NOT NULL, morning_work_hours_before TIME NOT NULL, afternoon_work_hours_from TIME NOT NULL, afternoon_work_hours_before TIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE holiday_holidays (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, holidays_from DATE NOT NULL, holidays_before DATE NOT NULL, INDEX IDX_DDEC514EA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE party_parties (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, party_day_from DATE NOT NULL, party_day_before DATE NOT NULL, party_time_from TIME NOT NULL, party_time_before TIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE holiday_holidays ADD CONSTRAINT FK_DDEC514EA76ED395 FOREIGN KEY (user_id) REFERENCES user_users (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE holiday_holidays DROP FOREIGN KEY FK_DDEC514EA76ED395');
        $this->addSql('DROP TABLE user_users');
        $this->addSql('DROP TABLE holiday_holidays');
        $this->addSql('DROP TABLE party_parties');
    }
}
