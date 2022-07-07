<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220120085538 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE products ADD shop_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE products ADD CONSTRAINT FK_B3BA5A5A4D16C4DD FOREIGN KEY (shop_id) REFERENCES shops (id)');
        $this->addSql('CREATE INDEX IDX_B3BA5A5A4D16C4DD ON products (shop_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE products DROP FOREIGN KEY FK_B3BA5A5A4D16C4DD');
        $this->addSql('DROP INDEX IDX_B3BA5A5A4D16C4DD ON products');
        $this->addSql('ALTER TABLE products DROP shop_id');
    }
}
