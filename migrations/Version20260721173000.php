<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration for StandardItem, ApplicationListItem, and OptionalItem tables.
 */
final class Version20260721173000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create standard_item, application_list_item, optional_item tables and subproduct mapping tables';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE application_list_item (id INT AUTO_INCREMENT NOT NULL, icon VARCHAR(100) DEFAULT NULL, name_pt VARCHAR(255) NOT NULL, name_en VARCHAR(255) DEFAULT NULL, name_es VARCHAR(255) DEFAULT NULL, position INT NOT NULL, is_active TINYINT NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE optional_item (id INT AUTO_INCREMENT NOT NULL, icon VARCHAR(100) DEFAULT NULL, name_pt VARCHAR(255) NOT NULL, name_en VARCHAR(255) DEFAULT NULL, name_es VARCHAR(255) DEFAULT NULL, position INT NOT NULL, is_active TINYINT NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE standard_item (id INT AUTO_INCREMENT NOT NULL, icon VARCHAR(100) DEFAULT NULL, name_pt VARCHAR(255) NOT NULL, name_en VARCHAR(255) DEFAULT NULL, name_es VARCHAR(255) DEFAULT NULL, position INT NOT NULL, is_active TINYINT NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE subproduct_application_list_items_map (subproduct_id INT NOT NULL, application_list_item_id INT NOT NULL, INDEX IDX_7CD778421A1581F9 (subproduct_id), INDEX IDX_7CD778426D35440A (application_list_item_id), PRIMARY KEY (subproduct_id, application_list_item_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE subproduct_standard_items_map (subproduct_id INT NOT NULL, standard_item_id INT NOT NULL, INDEX IDX_E8C4E64E1A1581F9 (subproduct_id), INDEX IDX_E8C4E64E39CABC08 (standard_item_id), PRIMARY KEY (subproduct_id, standard_item_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE subproduct_optional_items_map (subproduct_id INT NOT NULL, optional_item_id INT NOT NULL, INDEX IDX_7E438901A1581F9 (subproduct_id), INDEX IDX_7E43890F8FAA85F (optional_item_id), PRIMARY KEY (subproduct_id, optional_item_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE subproduct_application_list_items_map ADD CONSTRAINT FK_7CD778421A1581F9 FOREIGN KEY (subproduct_id) REFERENCES subproduct (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE subproduct_application_list_items_map ADD CONSTRAINT FK_7CD778426D35440A FOREIGN KEY (application_list_item_id) REFERENCES application_list_item (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE subproduct_standard_items_map ADD CONSTRAINT FK_E8C4E64E1A1581F9 FOREIGN KEY (subproduct_id) REFERENCES subproduct (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE subproduct_standard_items_map ADD CONSTRAINT FK_E8C4E64E39CABC08 FOREIGN KEY (standard_item_id) REFERENCES standard_item (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE subproduct_optional_items_map ADD CONSTRAINT FK_7E438901A1581F9 FOREIGN KEY (subproduct_id) REFERENCES subproduct (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE subproduct_optional_items_map ADD CONSTRAINT FK_7E43890F8FAA85F FOREIGN KEY (optional_item_id) REFERENCES optional_item (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE subproduct_application_list_items_map DROP FOREIGN KEY FK_7CD778421A1581F9');
        $this->addSql('ALTER TABLE subproduct_application_list_items_map DROP FOREIGN KEY FK_7CD778426D35440A');
        $this->addSql('ALTER TABLE subproduct_standard_items_map DROP FOREIGN KEY FK_E8C4E64E1A1581F9');
        $this->addSql('ALTER TABLE subproduct_standard_items_map DROP FOREIGN KEY FK_E8C4E64E39CABC08');
        $this->addSql('ALTER TABLE subproduct_optional_items_map DROP FOREIGN KEY FK_7E438901A1581F9');
        $this->addSql('ALTER TABLE subproduct_optional_items_map DROP FOREIGN KEY FK_7E43890F8FAA85F');
        $this->addSql('DROP TABLE application_list_item');
        $this->addSql('DROP TABLE optional_item');
        $this->addSql('DROP TABLE standard_item');
        $this->addSql('DROP TABLE subproduct_application_list_items_map');
        $this->addSql('DROP TABLE subproduct_standard_items_map');
        $this->addSql('DROP TABLE subproduct_optional_items_map');
    }
}
