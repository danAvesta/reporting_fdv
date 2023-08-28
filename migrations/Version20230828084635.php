<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230828084635 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE inventaire (id INT AUTO_INCREMENT NOT NULL, produit_id INT NOT NULL, id_rdv_id INT NOT NULL, quantite INT NOT NULL, date_inventaire DATETIME NOT NULL, UNIQUE INDEX UNIQ_338920E0F347EFB (produit_id), INDEX IDX_338920E06AF98A6B (id_rdv_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE inventaire ADD CONSTRAINT FK_338920E0F347EFB FOREIGN KEY (produit_id) REFERENCES produit (id)');
        $this->addSql('ALTER TABLE inventaire ADD CONSTRAINT FK_338920E06AF98A6B FOREIGN KEY (id_rdv_id) REFERENCES rdv (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE inventaire DROP FOREIGN KEY FK_338920E0F347EFB');
        $this->addSql('ALTER TABLE inventaire DROP FOREIGN KEY FK_338920E06AF98A6B');
        $this->addSql('DROP TABLE inventaire');
    }
}
