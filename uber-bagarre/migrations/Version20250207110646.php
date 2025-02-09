<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250207110646 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE annonce ADD latitude NUMERIC(9, 6) DEFAULT NULL');
        $this->addSql('ALTER TABLE annonce ADD longitude NUMERIC(9, 6) DEFAULT NULL');
        $this->addSql('ALTER TABLE "user" ADD latitude NUMERIC(9, 6) DEFAULT NULL');
        $this->addSql('ALTER TABLE "user" ADD longitude NUMERIC(9, 6) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE annonce DROP latitude');
        $this->addSql('ALTER TABLE annonce DROP longitude');
        $this->addSql('ALTER TABLE "user" DROP latitude');
        $this->addSql('ALTER TABLE "user" DROP longitude');
    }
}
