<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220119110955 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE shops DROP FOREIGN KEY FK_237A67838BAC62AF');
        $this->addSql('DROP INDEX IDX_237A67838BAC62AF ON shops');
        $this->addSql('ALTER TABLE shops ADD category_id INT DEFAULT NULL, CHANGE city_id town_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE shops ADD CONSTRAINT FK_237A678375E23604 FOREIGN KEY (town_id) REFERENCES town (id)');
        $this->addSql('ALTER TABLE shops ADD CONSTRAINT FK_237A678312469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('CREATE INDEX IDX_237A678375E23604 ON shops (town_id)');
        $this->addSql('CREATE INDEX IDX_237A678312469DE2 ON shops (category_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE shops DROP FOREIGN KEY FK_237A678375E23604');
        $this->addSql('ALTER TABLE shops DROP FOREIGN KEY FK_237A678312469DE2');
        $this->addSql('DROP INDEX IDX_237A678375E23604 ON shops');
        $this->addSql('DROP INDEX IDX_237A678312469DE2 ON shops');
        $this->addSql('ALTER TABLE shops ADD city_id INT DEFAULT NULL, DROP town_id, DROP category_id');
        $this->addSql('ALTER TABLE shops ADD CONSTRAINT FK_237A67838BAC62AF FOREIGN KEY (city_id) REFERENCES city (id)');
        $this->addSql('CREATE INDEX IDX_237A67838BAC62AF ON shops (city_id)');
    }
}
