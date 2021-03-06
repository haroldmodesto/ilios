<?php

namespace Ilios\Migrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Remove the Descriptor FK from previousIndexing and use
 * an autogenerated key instead
 */
class Version20150826201107 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE mesh_previous_indexing DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE mesh_previous_indexing ADD mesh_previous_indexing_id INT PRIMARY KEY AUTO_INCREMENT NOT NULL, CHANGE mesh_descriptor_uid mesh_descriptor_uid VARCHAR(9) DEFAULT NULL');
        $this->addSql('CREATE UNIQUE INDEX descriptor_previous ON mesh_previous_indexing (mesh_descriptor_uid)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE mesh_previous_indexing MODIFY mesh_previous_indexing_id INT NOT NULL');
        $this->addSql('DROP INDEX descriptor_previous ON mesh_previous_indexing');
        $this->addSql('ALTER TABLE mesh_previous_indexing DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE mesh_previous_indexing DROP FOREIGN KEY FK_32B6E2F4CDB3C93B');
        $this->addSql('ALTER TABLE mesh_previous_indexing DROP mesh_previous_indexing_id, CHANGE mesh_descriptor_uid mesh_descriptor_uid VARCHAR(9) NOT NULL COLLATE utf8_unicode_ci');
        $this->addSql('ALTER TABLE mesh_previous_indexing ADD PRIMARY KEY (mesh_descriptor_uid)');
        $this->addSql('ALTER TABLE mesh_previous_indexing ADD CONSTRAINT FK_32B6E2F4CDB3C93B FOREIGN KEY (mesh_descriptor_uid) REFERENCES mesh_descriptor (mesh_descriptor_uid)');
    }
}
