<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210729083827 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE program ADD poster_original_name VARCHAR(255) DEFAULT NULL, ADD poster_mime_type VARCHAR(255) DEFAULT NULL, ADD poster_size INT DEFAULT NULL, ADD poster_dimensions LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:simple_array)\', CHANGE poster poster_name VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE program ADD poster VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, DROP poster_name, DROP poster_original_name, DROP poster_mime_type, DROP poster_size, DROP poster_dimensions');
    }
}
