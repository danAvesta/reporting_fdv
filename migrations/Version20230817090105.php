<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230817090105 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE formulairerdv (id INT AUTO_INCREMENT NOT NULL, idrdv_id INT DEFAULT NULL, taille_magasin SMALLINT NOT NULL, marque VARCHAR(255) NOT NULL, display VARCHAR(255) NOT NULL, top_reference VARCHAR(255) NOT NULL, reference VARCHAR(255) NOT NULL, quantite VARCHAR(255) NOT NULL, plv VARCHAR(255) NOT NULL, motif_de_non_presence VARCHAR(255) NOT NULL, demande_dinstalation_plv VARCHAR(255) NOT NULL, fiche_promo VARCHAR(255) NOT NULL, raison_non_presence_fiche_promo VARCHAR(255) NOT NULL, ressenti_de_la_visite INT NOT NULL, remarque_en_plus VARCHAR(255) DEFAULT NULL, INDEX IDX_26D18C2D2873922A (idrdv_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE formulairerdv ADD CONSTRAINT FK_26D18C2D2873922A FOREIGN KEY (idrdv_id) REFERENCES rendez_vous (id)');
        $this->addSql('ALTER TABLE reset_password_request DROP FOREIGN KEY FK_7CE748AA76ED395');
        $this->addSql('DROP TABLE reset_password_request');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE reset_password_request (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, selector VARCHAR(20) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, hashed_token VARCHAR(100) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, requested_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', expires_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_7CE748AA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE reset_password_request ADD CONSTRAINT FK_7CE748AA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE formulairerdv DROP FOREIGN KEY FK_26D18C2D2873922A');
        $this->addSql('DROP TABLE formulairerdv');
    }
}
