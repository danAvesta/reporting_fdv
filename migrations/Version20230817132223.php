<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230817132223 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE formulairerdv CHANGE motif_de_non_presence motif_de_non_presence VARCHAR(255) DEFAULT NULL, CHANGE demande_dinstalation_plv demande_dinstalation_plv VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE formulairerdv CHANGE motif_de_non_presence motif_de_non_presence VARCHAR(255) NOT NULL, CHANGE demande_dinstalation_plv demande_dinstalation_plv VARCHAR(255) NOT NULL');
    }
}
