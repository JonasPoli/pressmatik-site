<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260706172653 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE about_gallery_image (id INT AUTO_INCREMENT NOT NULL, image_name VARCHAR(255) DEFAULT NULL, caption_pt VARCHAR(255) DEFAULT NULL, caption_en VARCHAR(255) DEFAULT NULL, caption_es VARCHAR(255) DEFAULT NULL, position INT NOT NULL, updated_at DATETIME DEFAULT NULL, about_us_id INT DEFAULT NULL, INDEX IDX_474A28567CE2CF2D (about_us_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE about_us (id INT AUTO_INCREMENT NOT NULL, title_pt VARCHAR(255) DEFAULT NULL, title_en VARCHAR(255) DEFAULT NULL, title_es VARCHAR(255) DEFAULT NULL, subtitle_pt VARCHAR(255) DEFAULT NULL, subtitle_en VARCHAR(255) DEFAULT NULL, subtitle_es VARCHAR(255) DEFAULT NULL, description_pt LONGTEXT DEFAULT NULL, description_en LONGTEXT DEFAULT NULL, description_es LONGTEXT DEFAULT NULL, mission_pt LONGTEXT DEFAULT NULL, mission_en LONGTEXT DEFAULT NULL, mission_es LONGTEXT DEFAULT NULL, vision_pt LONGTEXT DEFAULT NULL, vision_en LONGTEXT DEFAULT NULL, vision_es LONGTEXT DEFAULT NULL, values_pt LONGTEXT DEFAULT NULL, values_en LONGTEXT DEFAULT NULL, values_es LONGTEXT DEFAULT NULL, advantage1_title_pt VARCHAR(255) DEFAULT NULL, advantage1_title_en VARCHAR(255) DEFAULT NULL, advantage1_title_es VARCHAR(255) DEFAULT NULL, advantage1_desc_pt LONGTEXT DEFAULT NULL, advantage1_desc_en LONGTEXT DEFAULT NULL, advantage1_desc_es LONGTEXT DEFAULT NULL, advantage1_icon VARCHAR(100) DEFAULT NULL, advantage2_title_pt VARCHAR(255) DEFAULT NULL, advantage2_title_en VARCHAR(255) DEFAULT NULL, advantage2_title_es VARCHAR(255) DEFAULT NULL, advantage2_desc_pt LONGTEXT DEFAULT NULL, advantage2_desc_en LONGTEXT DEFAULT NULL, advantage2_desc_es LONGTEXT DEFAULT NULL, advantage2_icon VARCHAR(100) DEFAULT NULL, advantage3_title_pt VARCHAR(255) DEFAULT NULL, advantage3_title_en VARCHAR(255) DEFAULT NULL, advantage3_title_es VARCHAR(255) DEFAULT NULL, advantage3_desc_pt LONGTEXT DEFAULT NULL, advantage3_desc_en LONGTEXT DEFAULT NULL, advantage3_desc_es LONGTEXT DEFAULT NULL, advantage3_icon VARCHAR(100) DEFAULT NULL, advantage4_title_pt VARCHAR(255) DEFAULT NULL, advantage4_title_en VARCHAR(255) DEFAULT NULL, advantage4_title_es VARCHAR(255) DEFAULT NULL, advantage4_desc_pt LONGTEXT DEFAULT NULL, advantage4_desc_en LONGTEXT DEFAULT NULL, advantage4_desc_es LONGTEXT DEFAULT NULL, advantage4_icon VARCHAR(100) DEFAULT NULL, banner_image_name VARCHAR(255) DEFAULT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE history_timeline (id INT AUTO_INCREMENT NOT NULL, title_pt VARCHAR(255) DEFAULT NULL, title_en VARCHAR(255) DEFAULT NULL, title_es VARCHAR(255) DEFAULT NULL, description_pt LONGTEXT DEFAULT NULL, description_en LONGTEXT DEFAULT NULL, description_es LONGTEXT DEFAULT NULL, event_date DATE DEFAULT NULL, position INT NOT NULL, image_name VARCHAR(255) DEFAULT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE product_config_item (id INT AUTO_INCREMENT NOT NULL, product_slug VARCHAR(100) NOT NULL, type VARCHAR(20) NOT NULL, name_pt VARCHAR(255) NOT NULL, name_en VARCHAR(255) DEFAULT NULL, name_es VARCHAR(255) DEFAULT NULL, position INT NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE product_size (id INT AUTO_INCREMENT NOT NULL, product_slug VARCHAR(100) NOT NULL, name VARCHAR(100) NOT NULL, position INT NOT NULL, has_vtype TINYINT NOT NULL, has_htype TINYINT NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE product_spec_value (id INT AUTO_INCREMENT NOT NULL, product_slug VARCHAR(100) NOT NULL, v_type_value VARCHAR(100) DEFAULT NULL, h_type_value VARCHAR(100) DEFAULT NULL, position INT NOT NULL, specification_id INT NOT NULL, product_size_id INT NOT NULL, INDEX IDX_99E827BF908E2FFE (specification_id), INDEX IDX_99E827BF9854B397 (product_size_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE product_video (id INT AUTO_INCREMENT NOT NULL, product_slug VARCHAR(100) NOT NULL, title_pt VARCHAR(255) DEFAULT NULL, title_en VARCHAR(255) DEFAULT NULL, title_es VARCHAR(255) DEFAULT NULL, url VARCHAR(500) NOT NULL, position INT NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE technical_specification (id INT AUTO_INCREMENT NOT NULL, name_pt VARCHAR(255) NOT NULL, name_en VARCHAR(255) DEFAULT NULL, name_es VARCHAR(255) DEFAULT NULL, unit_pt VARCHAR(50) DEFAULT NULL, unit_en VARCHAR(50) DEFAULT NULL, unit_es VARCHAR(50) DEFAULT NULL, position INT NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(180) NOT NULL, email VARCHAR(180) NOT NULL, name VARCHAR(255) DEFAULT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, reset_password_token VARCHAR(100) DEFAULT NULL, reset_password_expires_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D649452C9EC5 (reset_password_token), UNIQUE INDEX UNIQ_IDENTIFIER_USERNAME (username), UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE about_gallery_image ADD CONSTRAINT FK_474A28567CE2CF2D FOREIGN KEY (about_us_id) REFERENCES about_us (id)');
        $this->addSql('ALTER TABLE product_spec_value ADD CONSTRAINT FK_99E827BF908E2FFE FOREIGN KEY (specification_id) REFERENCES technical_specification (id)');
        $this->addSql('ALTER TABLE product_spec_value ADD CONSTRAINT FK_99E827BF9854B397 FOREIGN KEY (product_size_id) REFERENCES product_size (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE about_gallery_image DROP FOREIGN KEY FK_474A28567CE2CF2D');
        $this->addSql('ALTER TABLE product_spec_value DROP FOREIGN KEY FK_99E827BF908E2FFE');
        $this->addSql('ALTER TABLE product_spec_value DROP FOREIGN KEY FK_99E827BF9854B397');
        $this->addSql('DROP TABLE about_gallery_image');
        $this->addSql('DROP TABLE about_us');
        $this->addSql('DROP TABLE history_timeline');
        $this->addSql('DROP TABLE product_config_item');
        $this->addSql('DROP TABLE product_size');
        $this->addSql('DROP TABLE product_spec_value');
        $this->addSql('DROP TABLE product_video');
        $this->addSql('DROP TABLE technical_specification');
        $this->addSql('DROP TABLE user');
    }
}
