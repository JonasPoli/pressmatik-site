<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260714220922 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        if (!$schema->hasTable('application')) {
            $this->addSql('CREATE TABLE application (id INT AUTO_INCREMENT NOT NULL, name_pt VARCHAR(255) NOT NULL, name_en VARCHAR(255) DEFAULT NULL, name_es VARCHAR(255) DEFAULT NULL, image_name VARCHAR(255) DEFAULT NULL, is_active TINYINT NOT NULL, position INT NOT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        }
        if (!$schema->hasTable('subproduct_applications_map')) {
            $this->addSql('CREATE TABLE subproduct_applications_map (subproduct_id INT NOT NULL, application_id INT NOT NULL, INDEX IDX_5E37D1511A1581F9 (subproduct_id), INDEX IDX_5E37D1513E030ACD (application_id), PRIMARY KEY (subproduct_id, application_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
            $this->addSql('ALTER TABLE subproduct_applications_map ADD CONSTRAINT FK_5E37D1511A1581F9 FOREIGN KEY (subproduct_id) REFERENCES subproduct (id) ON DELETE CASCADE');
            $this->addSql('ALTER TABLE subproduct_applications_map ADD CONSTRAINT FK_5E37D1513E030ACD FOREIGN KEY (application_id) REFERENCES application (id) ON DELETE CASCADE');
        }

        $productSizeTable = $schema->getTable('product_size');
        if (!$productSizeTable->hasColumn('subproduct_id')) {
            $this->addSql('DELETE FROM product_spec_value');
            $this->addSql('DELETE FROM product_size');
            if ($productSizeTable->hasForeignKey('FK_7A2806CB4584665A')) {
                $this->addSql('ALTER TABLE product_size DROP FOREIGN KEY `FK_7A2806CB4584665A`');
            }
            // Check index presence
            $indexes = $productSizeTable->getIndexes();
            if (isset($indexes['idx_7a2806cb4584665a']) || isset($indexes['IDX_7A2806CB4584665A'])) {
                $this->addSql('DROP INDEX IDX_7A2806CB4584665A ON product_size');
            }
            $this->addSql('ALTER TABLE product_size ADD subproduct_id INT NOT NULL, DROP product_slug, DROP product_id');
            $this->addSql('ALTER TABLE product_size ADD CONSTRAINT FK_7A2806CB1A1581F9 FOREIGN KEY (subproduct_id) REFERENCES subproduct (id) ON DELETE CASCADE');
            $this->addSql('CREATE INDEX IDX_7A2806CB1A1581F9 ON product_size (subproduct_id)');
        }

        $productSpecValueTable = $schema->getTable('product_spec_value');
        if (!$productSpecValueTable->hasColumn('subproduct_id')) {
            if ($productSpecValueTable->hasForeignKey('FK_99E827BF4584665A')) {
                $this->addSql('ALTER TABLE product_spec_value DROP FOREIGN KEY `FK_99E827BF4584665A`');
            }
            $indexesSpec = $productSpecValueTable->getIndexes();
            if (isset($indexesSpec['idx_99e827bf4584665a']) || isset($indexesSpec['IDX_99E827BF4584665A'])) {
                $this->addSql('DROP INDEX IDX_99E827BF4584665A ON product_spec_value');
            }
            $this->addSql('ALTER TABLE product_spec_value ADD subproduct_id INT NOT NULL, DROP product_slug, DROP product_id');
            $this->addSql('ALTER TABLE product_spec_value ADD CONSTRAINT FK_99E827BF1A1581F9 FOREIGN KEY (subproduct_id) REFERENCES subproduct (id) ON DELETE CASCADE');
            $this->addSql('CREATE INDEX IDX_99E827BF1A1581F9 ON product_spec_value (subproduct_id)');
        }

        $subproductTable = $schema->getTable('subproduct');
        if (!$subproductTable->hasColumn('pdf_name_pt')) {
            $this->addSql('ALTER TABLE subproduct ADD pdf_name_pt VARCHAR(255) DEFAULT NULL');
        }
        if (!$subproductTable->hasColumn('pdf_name_en')) {
            $this->addSql('ALTER TABLE subproduct ADD pdf_name_en VARCHAR(255) DEFAULT NULL');
        }
        if (!$subproductTable->hasColumn('pdf_name_es')) {
            $this->addSql('ALTER TABLE subproduct ADD pdf_name_es VARCHAR(255) DEFAULT NULL');
        }
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE subproduct_applications_map DROP FOREIGN KEY FK_5E37D1511A1581F9');
        $this->addSql('ALTER TABLE subproduct_applications_map DROP FOREIGN KEY FK_5E37D1513E030ACD');
        $this->addSql('DROP TABLE application');
        $this->addSql('DROP TABLE subproduct_applications_map');
        $this->addSql('ALTER TABLE product_size DROP FOREIGN KEY FK_7A2806CB1A1581F9');
        $this->addSql('DROP INDEX IDX_7A2806CB1A1581F9 ON product_size');
        $this->addSql('ALTER TABLE product_size ADD product_slug VARCHAR(100) DEFAULT NULL, ADD product_id INT DEFAULT NULL, DROP subproduct_id');
        $this->addSql('ALTER TABLE product_size ADD CONSTRAINT `FK_7A2806CB4584665A` FOREIGN KEY (product_id) REFERENCES product (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_7A2806CB4584665A ON product_size (product_id)');
        $this->addSql('ALTER TABLE product_spec_value DROP FOREIGN KEY FK_99E827BF1A1581F9');
        $this->addSql('DROP INDEX IDX_99E827BF1A1581F9 ON product_spec_value');
        $this->addSql('ALTER TABLE product_spec_value ADD product_slug VARCHAR(100) DEFAULT NULL, ADD product_id INT DEFAULT NULL, DROP subproduct_id');
        $this->addSql('ALTER TABLE product_spec_value ADD CONSTRAINT `FK_99E827BF4584665A` FOREIGN KEY (product_id) REFERENCES product (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_99E827BF4584665A ON product_spec_value (product_id)');

        $subproductTable = $schema->getTable('subproduct');
        if ($subproductTable->hasColumn('pdf_name_pt')) {
            $this->addSql('ALTER TABLE subproduct DROP pdf_name_pt');
        }
        if ($subproductTable->hasColumn('pdf_name_en')) {
            $this->addSql('ALTER TABLE subproduct DROP pdf_name_en');
        }
        if ($subproductTable->hasColumn('pdf_name_es')) {
            $this->addSql('ALTER TABLE subproduct DROP pdf_name_es');
        }
    }
}
