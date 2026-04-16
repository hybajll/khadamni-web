<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260416200000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add Society and Offer entities for job posting management';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE society (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(150) NOT NULL, email VARCHAR(150) NOT NULL, password VARCHAR(255) NOT NULL, phone VARCHAR(20) DEFAULT NULL, address VARCHAR(255) DEFAULT NULL, domain VARCHAR(150) DEFAULT NULL, description LONGTEXT DEFAULT NULL, website VARCHAR(255) DEFAULT NULL, is_active TINYINT(1) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_8568B3DEE7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE offers (id INT AUTO_INCREMENT NOT NULL, society_id INT NOT NULL, title VARCHAR(200) NOT NULL, description LONGTEXT NOT NULL, domain VARCHAR(150) DEFAULT NULL, salary NUMERIC(10, 2) DEFAULT NULL, contract_type VARCHAR(100) NOT NULL, location VARCHAR(150) DEFAULT NULL, experience_level VARCHAR(100) DEFAULT NULL, expiration_date DATE DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', is_active TINYINT(1) NOT NULL, INDEX IDX_DA460427C1CEDA41 (society_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE offers ADD CONSTRAINT FK_DA460427C1CEDA41 FOREIGN KEY (society_id) REFERENCES society (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE offers DROP FOREIGN KEY FK_DA460427C1CEDA41');
        $this->addSql('DROP TABLE society');
        $this->addSql('DROP TABLE offers');
    }
}
