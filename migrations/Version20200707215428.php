<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200707215428 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE article ADD marque VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE ligne ADD nom_presta_id INT NOT NULL, ADD vente_id INT NOT NULL, ADD prix_ind DOUBLE PRECISION NOT NULL, ADD remise INT DEFAULT NULL, ADD prix_final DOUBLE PRECISION NOT NULL');
        $this->addSql('ALTER TABLE ligne ADD CONSTRAINT FK_57F0DB832EDEE6B1 FOREIGN KEY (nom_presta_id) REFERENCES prestation (id)');
        $this->addSql('ALTER TABLE ligne ADD CONSTRAINT FK_57F0DB837DC7170A FOREIGN KEY (vente_id) REFERENCES vente (id)');
        $this->addSql('CREATE INDEX IDX_57F0DB832EDEE6B1 ON ligne (nom_presta_id)');
        $this->addSql('CREATE INDEX IDX_57F0DB837DC7170A ON ligne (vente_id)');
        $this->addSql('ALTER TABLE vente ADD client_id INT DEFAULT NULL, ADD date DATE NOT NULL');
        $this->addSql('ALTER TABLE vente ADD CONSTRAINT FK_888A2A4C19EB6921 FOREIGN KEY (client_id) REFERENCES client (id)');
        $this->addSql('CREATE INDEX IDX_888A2A4C19EB6921 ON vente (client_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE article DROP marque');
        $this->addSql('ALTER TABLE ligne DROP FOREIGN KEY FK_57F0DB832EDEE6B1');
        $this->addSql('ALTER TABLE ligne DROP FOREIGN KEY FK_57F0DB837DC7170A');
        $this->addSql('DROP INDEX IDX_57F0DB832EDEE6B1 ON ligne');
        $this->addSql('DROP INDEX IDX_57F0DB837DC7170A ON ligne');
        $this->addSql('ALTER TABLE ligne DROP nom_presta_id, DROP vente_id, DROP prix_ind, DROP remise, DROP prix_final');
        $this->addSql('ALTER TABLE vente DROP FOREIGN KEY FK_888A2A4C19EB6921');
        $this->addSql('DROP INDEX IDX_888A2A4C19EB6921 ON vente');
        $this->addSql('ALTER TABLE vente DROP client_id, DROP date');
    }
}
