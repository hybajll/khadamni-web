<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260413191000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add pdfPath to cv table (store uploaded PDF).';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE cv ADD pdfPath VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE cv DROP pdfPath');
    }
}

