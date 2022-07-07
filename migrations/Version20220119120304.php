<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220119120304 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE shops DROP FOREIGN KEY FK_237A678312469DE2');
        $this->addSql('ALTER TABLE shops ADD shop_banner VARCHAR(255) NOT NULL, ADD email VARCHAR(255) NOT NULL, ADD link VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE shops ADD CONSTRAINT FK_237A678312469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE shops DROP FOREIGN KEY FK_237A678312469DE2');
        $this->addSql('ALTER TABLE shops DROP shop_banner, DROP email, DROP link');
        $this->addSql('ALTER TABLE shops ADD CONSTRAINT FK_237A678312469DE2 FOREIGN KEY (category_id) REFERENCES shop_category (id)');
    }
}
