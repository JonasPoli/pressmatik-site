<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260803165723 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create service_header table for section background video settings';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE service_header (id INT AUTO_INCREMENT NOT NULL, video_name VARCHAR(255) DEFAULT NULL, video_url VARCHAR(500) DEFAULT NULL, updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE service_header');
    }
}
