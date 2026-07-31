<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Migration to add bg_image_name to banner table and create mega_menu_category table.
 */
final class Version20260731211500 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add bg_image_name to banner table and create mega_menu_category table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE banner ADD bg_image_name VARCHAR(255) DEFAULT NULL');
        $this->addSql('CREATE TABLE mega_menu_category (id INT AUTO_INCREMENT NOT NULL, category_key VARCHAR(100) NOT NULL, title_pt VARCHAR(255) DEFAULT NULL, title_en VARCHAR(255) DEFAULT NULL, title_es VARCHAR(255) DEFAULT NULL, image_name VARCHAR(255) DEFAULT NULL, default_image_path VARCHAR(255) DEFAULT NULL, position INT NOT NULL, updated_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_MEGA_MENU_KEY (category_key), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE banner DROP bg_image_name');
        $this->addSql('DROP TABLE mega_menu_category');
    }
}
