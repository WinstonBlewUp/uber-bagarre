<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250205170405 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE annonce_participants (annonce_id INT NOT NULL, user_id INT NOT NULL, PRIMARY KEY(annonce_id, user_id))');
        $this->addSql('CREATE INDEX IDX_977E83358805AB2F ON annonce_participants (annonce_id)');
        $this->addSql('CREATE INDEX IDX_977E8335A76ED395 ON annonce_participants (user_id)');
        $this->addSql('ALTER TABLE annonce_participants ADD CONSTRAINT FK_977E83358805AB2F FOREIGN KEY (annonce_id) REFERENCES annonce (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE annonce_participants ADD CONSTRAINT FK_977E8335A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE annonce_participants DROP CONSTRAINT FK_977E83358805AB2F');
        $this->addSql('ALTER TABLE annonce_participants DROP CONSTRAINT FK_977E8335A76ED395');
        $this->addSql('DROP TABLE annonce_participants');
    }
}
