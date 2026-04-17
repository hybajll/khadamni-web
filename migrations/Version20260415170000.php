<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260415170000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add avatarPath to user table (profile picture).';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE user ADD avatarPath VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE user DROP avatarPath');
    }
}

