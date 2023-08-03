<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230802141734 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE rdv ADD create_by_id INT NOT NULL, ADD commercial_id INT NOT NULL, ADD create_date DATETIME NOT NULL, ADD date_rdv DATETIME NOT NULL, ADD nom_magasin VARCHAR(255) NOT NULL, ADD adresse_magasin LONGTEXT NOT NULL, ADD statut VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE rdv ADD CONSTRAINT FK_10C31F869E085865 FOREIGN KEY (create_by_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE rdv ADD CONSTRAINT FK_10C31F867854071C FOREIGN KEY (commercial_id) REFERENCES `user` (id)');
        $this->addSql('CREATE INDEX IDX_10C31F869E085865 ON rdv (create_by_id)');
        $this->addSql('CREATE INDEX IDX_10C31F867854071C ON rdv (commercial_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE rdv DROP FOREIGN KEY FK_10C31F869E085865');
        $this->addSql('ALTER TABLE rdv DROP FOREIGN KEY FK_10C31F867854071C');
        $this->addSql('DROP INDEX IDX_10C31F869E085865 ON rdv');
        $this->addSql('DROP INDEX IDX_10C31F867854071C ON rdv');
        $this->addSql('ALTER TABLE rdv DROP create_by_id, DROP commercial_id, DROP create_date, DROP date_rdv, DROP nom_magasin, DROP adresse_magasin, DROP statut');
    }
}
