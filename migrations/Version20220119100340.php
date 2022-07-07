<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220119100340 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE shops DROP FOREIGN KEY FK_237A678312469DE2');
        $this->addSql('DROP INDEX IDX_237A678312469DE2 ON shops');
        $this->addSql('ALTER TABLE shops DROP category_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE shops ADD category_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE shops ADD CONSTRAINT FK_237A678312469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('CREATE INDEX IDX_237A678312469DE2 ON shops (category_id)');
    }
}
