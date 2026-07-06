<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260706220100 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE client_logo (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) DEFAULT NULL, image_name VARCHAR(255) DEFAULT NULL, updated_at DATETIME DEFAULT NULL, position INT NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE news (id INT AUTO_INCREMENT NOT NULL, date DATE NOT NULL, title_pt VARCHAR(255) NOT NULL, title_en VARCHAR(255) DEFAULT NULL, title_es VARCHAR(255) DEFAULT NULL, short_description_pt LONGTEXT DEFAULT NULL, short_description_en LONGTEXT DEFAULT NULL, short_description_es LONGTEXT DEFAULT NULL, full_description_pt LONGTEXT DEFAULT NULL, full_description_en LONGTEXT DEFAULT NULL, full_description_es LONGTEXT DEFAULT NULL, youtube_video_code VARCHAR(255) DEFAULT NULL, image_name VARCHAR(255) DEFAULT NULL, updated_at DATETIME DEFAULT NULL, slug_pt VARCHAR(255) DEFAULT NULL, slug_en VARCHAR(255) DEFAULT NULL, slug_es VARCHAR(255) DEFAULT NULL, seo_title_pt VARCHAR(255) DEFAULT NULL, seo_title_en VARCHAR(255) DEFAULT NULL, seo_title_es VARCHAR(255) DEFAULT NULL, seo_description_pt VARCHAR(255) DEFAULT NULL, seo_description_en VARCHAR(255) DEFAULT NULL, seo_description_es VARCHAR(255) DEFAULT NULL, image_alt_pt VARCHAR(255) DEFAULT NULL, image_alt_en VARCHAR(255) DEFAULT NULL, image_alt_es VARCHAR(255) DEFAULT NULL, is_highlighted TINYINT NOT NULL, is_active TINYINT NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE news_news_category (news_id INT NOT NULL, news_category_id INT NOT NULL, INDEX IDX_1A91D6D6B5A459A0 (news_id), INDEX IDX_1A91D6D63B732BAD (news_category_id), PRIMARY KEY (news_id, news_category_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE news_category (id INT AUTO_INCREMENT NOT NULL, name_pt VARCHAR(255) NOT NULL, name_en VARCHAR(255) DEFAULT NULL, name_es VARCHAR(255) DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE news_image (id INT AUTO_INCREMENT NOT NULL, image_name VARCHAR(255) DEFAULT NULL, updated_at DATETIME DEFAULT NULL, caption_pt VARCHAR(255) DEFAULT NULL, caption_en VARCHAR(255) DEFAULT NULL, caption_es VARCHAR(255) DEFAULT NULL, position INT NOT NULL, news_id INT NOT NULL, INDEX IDX_BF828301B5A459A0 (news_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE service (id INT AUTO_INCREMENT NOT NULL, title_pt VARCHAR(255) NOT NULL, title_en VARCHAR(255) DEFAULT NULL, title_es VARCHAR(255) DEFAULT NULL, slug_pt VARCHAR(255) DEFAULT NULL, slug_en VARCHAR(255) DEFAULT NULL, slug_es VARCHAR(255) DEFAULT NULL, short_description_pt LONGTEXT DEFAULT NULL, short_description_en LONGTEXT DEFAULT NULL, short_description_es LONGTEXT DEFAULT NULL, description_pt LONGTEXT DEFAULT NULL, description_en LONGTEXT DEFAULT NULL, description_es LONGTEXT DEFAULT NULL, image_name VARCHAR(255) DEFAULT NULL, updated_at DATETIME DEFAULT NULL, is_active TINYINT NOT NULL, position INT NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE service_image (id INT AUTO_INCREMENT NOT NULL, image_name VARCHAR(255) DEFAULT NULL, updated_at DATETIME DEFAULT NULL, caption_pt VARCHAR(255) DEFAULT NULL, caption_en VARCHAR(255) DEFAULT NULL, caption_es VARCHAR(255) DEFAULT NULL, position INT NOT NULL, service_id INT NOT NULL, INDEX IDX_6C4FE9B8ED5CA9E6 (service_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE testimony (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, company VARCHAR(255) DEFAULT NULL, role_pt VARCHAR(255) DEFAULT NULL, role_en VARCHAR(255) DEFAULT NULL, role_es VARCHAR(255) DEFAULT NULL, text_pt LONGTEXT DEFAULT NULL, text_en LONGTEXT DEFAULT NULL, text_es LONGTEXT DEFAULT NULL, image_name VARCHAR(255) DEFAULT NULL, updated_at DATETIME DEFAULT NULL, is_active TINYINT NOT NULL, position INT NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE news_news_category ADD CONSTRAINT FK_1A91D6D6B5A459A0 FOREIGN KEY (news_id) REFERENCES news (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE news_news_category ADD CONSTRAINT FK_1A91D6D63B732BAD FOREIGN KEY (news_category_id) REFERENCES news_category (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE news_image ADD CONSTRAINT FK_BF828301B5A459A0 FOREIGN KEY (news_id) REFERENCES news (id)');
        $this->addSql('ALTER TABLE service_image ADD CONSTRAINT FK_6C4FE9B8ED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE news_news_category DROP FOREIGN KEY FK_1A91D6D6B5A459A0');
        $this->addSql('ALTER TABLE news_news_category DROP FOREIGN KEY FK_1A91D6D63B732BAD');
        $this->addSql('ALTER TABLE news_image DROP FOREIGN KEY FK_BF828301B5A459A0');
        $this->addSql('ALTER TABLE service_image DROP FOREIGN KEY FK_6C4FE9B8ED5CA9E6');
        $this->addSql('DROP TABLE client_logo');
        $this->addSql('DROP TABLE news');
        $this->addSql('DROP TABLE news_news_category');
        $this->addSql('DROP TABLE news_category');
        $this->addSql('DROP TABLE news_image');
        $this->addSql('DROP TABLE service');
        $this->addSql('DROP TABLE service_image');
        $this->addSql('DROP TABLE testimony');
    }
}
