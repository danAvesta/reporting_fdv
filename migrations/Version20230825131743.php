<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230825131743 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE inventairerdv ADD product_id INT NOT NULL, DROP reference');
        $this->addSql('ALTER TABLE inventairerdv ADD CONSTRAINT FK_B8C1D0334584665A FOREIGN KEY (product_id) REFERENCES produit (id)');
        $this->addSql('CREATE INDEX IDX_B8C1D0334584665A ON inventairerdv (product_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE inventairerdv DROP FOREIGN KEY FK_B8C1D0334584665A');
        $this->addSql('DROP INDEX IDX_B8C1D0334584665A ON inventairerdv');
        $this->addSql('ALTER TABLE inventairerdv ADD reference VARCHAR(255) NOT NULL, DROP product_id');
    }
}
