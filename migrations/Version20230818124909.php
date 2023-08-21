<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230818124909 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE inventairerdv (id INT AUTO_INCREMENT NOT NULL, id_rdv_id INT DEFAULT NULL, reference VARCHAR(255) NOT NULL, quantite INT NOT NULL, datetime DATETIME NOT NULL, INDEX IDX_B8C1D0336AF98A6B (id_rdv_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE inventairerdv ADD CONSTRAINT FK_B8C1D0336AF98A6B FOREIGN KEY (id_rdv_id) REFERENCES rendez_vous (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE inventairerdv DROP FOREIGN KEY FK_B8C1D0336AF98A6B');
        $this->addSql('DROP TABLE inventairerdv');
    }
}
