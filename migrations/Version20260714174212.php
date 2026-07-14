<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260714174212 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE banner (id INT AUTO_INCREMENT NOT NULL, title_pt VARCHAR(255) DEFAULT NULL, title_en VARCHAR(255) DEFAULT NULL, title_es VARCHAR(255) DEFAULT NULL, subtitle_pt VARCHAR(255) DEFAULT NULL, subtitle_en VARCHAR(255) DEFAULT NULL, subtitle_es VARCHAR(255) DEFAULT NULL, button_text_pt VARCHAR(255) DEFAULT NULL, button_text_en VARCHAR(255) DEFAULT NULL, button_text_es VARCHAR(255) DEFAULT NULL, button_url VARCHAR(500) DEFAULT NULL, image_name VARCHAR(255) DEFAULT NULL, updated_at DATETIME DEFAULT NULL, position INT NOT NULL, is_active TINYINT NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE contact_message (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, phone VARCHAR(50) DEFAULT NULL, cpf_cnpj VARCHAR(20) DEFAULT NULL, company VARCHAR(255) DEFAULT NULL, product_interest VARCHAR(255) DEFAULT NULL, message LONGTEXT DEFAULT NULL, type VARCHAR(20) NOT NULL, product_slug VARCHAR(100) DEFAULT NULL, created_at DATETIME NOT NULL, is_read TINYINT NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE differential (id INT AUTO_INCREMENT NOT NULL, title_pt VARCHAR(255) NOT NULL, title_en VARCHAR(255) DEFAULT NULL, title_es VARCHAR(255) DEFAULT NULL, description_pt LONGTEXT DEFAULT NULL, description_en LONGTEXT DEFAULT NULL, description_es LONGTEXT DEFAULT NULL, icon VARCHAR(100) DEFAULT NULL, position INT NOT NULL, is_active TINYINT NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE org_chart_item (id INT AUTO_INCREMENT NOT NULL, title_pt VARCHAR(255) NOT NULL, title_en VARCHAR(255) DEFAULT NULL, title_es VARCHAR(255) DEFAULT NULL, description_pt LONGTEXT DEFAULT NULL, description_en LONGTEXT DEFAULT NULL, description_es LONGTEXT DEFAULT NULL, icon VARCHAR(100) DEFAULT NULL, position INT NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE product (id INT AUTO_INCREMENT NOT NULL, slug VARCHAR(150) NOT NULL, category VARCHAR(50) NOT NULL, name_pt VARCHAR(255) NOT NULL, name_en VARCHAR(255) DEFAULT NULL, name_es VARCHAR(255) DEFAULT NULL, description_pt LONGTEXT DEFAULT NULL, description_en LONGTEXT DEFAULT NULL, description_es LONGTEXT DEFAULT NULL, tonnage VARCHAR(100) DEFAULT NULL, image_name VARCHAR(255) DEFAULT NULL, updated_at DATETIME DEFAULT NULL, position INT NOT NULL, is_active TINYINT NOT NULL, has_specs TINYINT NOT NULL, default_subproduct_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_D34A04AD989D9B62 (slug), INDEX IDX_D34A04AD2C341DEA (default_subproduct_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE quality_certification (id INT AUTO_INCREMENT NOT NULL, title_pt VARCHAR(255) DEFAULT NULL, title_en VARCHAR(255) DEFAULT NULL, title_es VARCHAR(255) DEFAULT NULL, description_pt LONGTEXT DEFAULT NULL, description_en LONGTEXT DEFAULT NULL, description_es LONGTEXT DEFAULT NULL, image_name VARCHAR(255) DEFAULT NULL, updated_at DATETIME DEFAULT NULL, position INT NOT NULL, is_active TINYINT NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE subproduct (id INT AUTO_INCREMENT NOT NULL, model VARCHAR(50) NOT NULL, name_pt VARCHAR(255) NOT NULL, name_en VARCHAR(255) DEFAULT NULL, name_es VARCHAR(255) DEFAULT NULL, description_pt LONGTEXT DEFAULT NULL, description_en LONGTEXT DEFAULT NULL, description_es LONGTEXT DEFAULT NULL, tag VARCHAR(100) DEFAULT NULL, image_name VARCHAR(255) DEFAULT NULL, updated_at DATETIME DEFAULT NULL, position INT NOT NULL, is_active TINYINT NOT NULL, product_id INT NOT NULL, INDEX IDX_9828AC624584665A (product_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE success_case (id INT AUTO_INCREMENT NOT NULL, title_pt VARCHAR(255) NOT NULL, title_en VARCHAR(255) DEFAULT NULL, title_es VARCHAR(255) DEFAULT NULL, description_pt LONGTEXT DEFAULT NULL, description_en LONGTEXT DEFAULT NULL, description_es LONGTEXT DEFAULT NULL, client_name VARCHAR(255) DEFAULT NULL, client_industry VARCHAR(255) DEFAULT NULL, image_name VARCHAR(255) DEFAULT NULL, updated_at DATETIME DEFAULT NULL, position INT NOT NULL, is_active TINYINT NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE supplier (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, website_url VARCHAR(500) DEFAULT NULL, image_name VARCHAR(255) DEFAULT NULL, updated_at DATETIME DEFAULT NULL, position INT NOT NULL, is_active TINYINT NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04AD2C341DEA FOREIGN KEY (default_subproduct_id) REFERENCES subproduct (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE subproduct ADD CONSTRAINT FK_9828AC624584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE about_us ADD youtube_video_url_pt VARCHAR(500) DEFAULT NULL, ADD youtube_video_url_en VARCHAR(500) DEFAULT NULL, ADD youtube_video_url_es VARCHAR(500) DEFAULT NULL, ADD home_text_pt LONGTEXT DEFAULT NULL, ADD home_text_en LONGTEXT DEFAULT NULL, ADD home_text_es LONGTEXT DEFAULT NULL, ADD home_image_name VARCHAR(255) DEFAULT NULL, ADD org_chart_description_pt LONGTEXT DEFAULT NULL, ADD org_chart_description_en LONGTEXT DEFAULT NULL, ADD org_chart_description_es LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE product_config_item ADD product_id INT DEFAULT NULL, CHANGE product_slug product_slug VARCHAR(100) DEFAULT NULL');
        $this->addSql('ALTER TABLE product_config_item ADD CONSTRAINT FK_2394E7F14584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('CREATE INDEX IDX_2394E7F14584665A ON product_config_item (product_id)');
        $this->addSql('ALTER TABLE product_size ADD product_id INT DEFAULT NULL, CHANGE product_slug product_slug VARCHAR(100) DEFAULT NULL');
        $this->addSql('ALTER TABLE product_size ADD CONSTRAINT FK_7A2806CB4584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('CREATE INDEX IDX_7A2806CB4584665A ON product_size (product_id)');
        $this->addSql('ALTER TABLE product_spec_value ADD product_id INT DEFAULT NULL, CHANGE product_slug product_slug VARCHAR(100) DEFAULT NULL');
        $this->addSql('ALTER TABLE product_spec_value ADD CONSTRAINT FK_99E827BF4584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('CREATE INDEX IDX_99E827BF4584665A ON product_spec_value (product_id)');
        $this->addSql('ALTER TABLE product_video ADD product_id INT DEFAULT NULL, CHANGE product_slug product_slug VARCHAR(100) DEFAULT NULL');
        $this->addSql('ALTER TABLE product_video ADD CONSTRAINT FK_DD9BA1704584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('CREATE INDEX IDX_DD9BA1704584665A ON product_video (product_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04AD2C341DEA');
        $this->addSql('ALTER TABLE subproduct DROP FOREIGN KEY FK_9828AC624584665A');
        $this->addSql('DROP TABLE banner');
        $this->addSql('DROP TABLE contact_message');
        $this->addSql('DROP TABLE differential');
        $this->addSql('DROP TABLE org_chart_item');
        $this->addSql('DROP TABLE product');
        $this->addSql('DROP TABLE quality_certification');
        $this->addSql('DROP TABLE subproduct');
        $this->addSql('DROP TABLE success_case');
        $this->addSql('DROP TABLE supplier');
        $this->addSql('ALTER TABLE about_us DROP youtube_video_url_pt, DROP youtube_video_url_en, DROP youtube_video_url_es, DROP home_text_pt, DROP home_text_en, DROP home_text_es, DROP home_image_name, DROP org_chart_description_pt, DROP org_chart_description_en, DROP org_chart_description_es');
        $this->addSql('ALTER TABLE product_config_item DROP FOREIGN KEY FK_2394E7F14584665A');
        $this->addSql('DROP INDEX IDX_2394E7F14584665A ON product_config_item');
        $this->addSql('ALTER TABLE product_config_item DROP product_id, CHANGE product_slug product_slug VARCHAR(100) NOT NULL');
        $this->addSql('ALTER TABLE product_size DROP FOREIGN KEY FK_7A2806CB4584665A');
        $this->addSql('DROP INDEX IDX_7A2806CB4584665A ON product_size');
        $this->addSql('ALTER TABLE product_size DROP product_id, CHANGE product_slug product_slug VARCHAR(100) NOT NULL');
        $this->addSql('ALTER TABLE product_spec_value DROP FOREIGN KEY FK_99E827BF4584665A');
        $this->addSql('DROP INDEX IDX_99E827BF4584665A ON product_spec_value');
        $this->addSql('ALTER TABLE product_spec_value DROP product_id, CHANGE product_slug product_slug VARCHAR(100) NOT NULL');
        $this->addSql('ALTER TABLE product_video DROP FOREIGN KEY FK_DD9BA1704584665A');
        $this->addSql('DROP INDEX IDX_DD9BA1704584665A ON product_video');
        $this->addSql('ALTER TABLE product_video DROP product_id, CHANGE product_slug product_slug VARCHAR(100) NOT NULL');
    }
}
