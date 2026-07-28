<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Migration to add badge fields to banner table.
 */
final class Version20260728201500 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add badgeTop and badgeBottom number/label fields to banner table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE banner ADD badge_top_num_pt VARCHAR(255) DEFAULT NULL, ADD badge_top_num_en VARCHAR(255) DEFAULT NULL, ADD badge_top_num_es VARCHAR(255) DEFAULT NULL, ADD badge_top_label_pt VARCHAR(255) DEFAULT NULL, ADD badge_top_label_en VARCHAR(255) DEFAULT NULL, ADD badge_top_label_es VARCHAR(255) DEFAULT NULL, ADD badge_bottom_num_pt VARCHAR(255) DEFAULT NULL, ADD badge_bottom_num_en VARCHAR(255) DEFAULT NULL, ADD badge_bottom_num_es VARCHAR(255) DEFAULT NULL, ADD badge_bottom_label_pt VARCHAR(255) DEFAULT NULL, ADD badge_bottom_label_en VARCHAR(255) DEFAULT NULL, ADD badge_bottom_label_es VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE banner DROP badge_top_num_pt, DROP badge_top_num_en, DROP badge_top_num_es, DROP badge_top_label_pt, DROP badge_top_label_en, DROP badge_top_label_es, DROP badge_bottom_num_pt, DROP badge_bottom_num_en, DROP badge_bottom_num_es, DROP badge_bottom_label_pt, DROP badge_bottom_label_en, DROP badge_bottom_label_es');
    }
}
