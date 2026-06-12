<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260612082152 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE enseignant DROP INDEX UNIQ_81A72FA1F2C56620, ADD INDEX IDX_81A72FA1F2C56620 (compte_id)');
        $this->addSql('DROP INDEX UNIQ_81A72FA1E7927C74 ON enseignant');
        $this->addSql('ALTER TABLE enseignant CHANGE nom nom VARCHAR(255) NOT NULL, CHANGE prenom prenom VARCHAR(255) NOT NULL, CHANGE email email VARCHAR(255) NOT NULL, CHANGE specialite specialite VARCHAR(255) NOT NULL');
        $this->addSql('DROP INDEX UNIQ_717E22E3E7927C74 ON etudiant');
        $this->addSql('ALTER TABLE etudiant CHANGE nom nom VARCHAR(255) NOT NULL, CHANGE prenom prenom VARCHAR(255) NOT NULL, CHANGE email email VARCHAR(255) NOT NULL, CHANGE filiere filiere VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE salle ADD nom VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE soutenance DROP INDEX UNIQ_4D59FF6EDDEAB1A3, ADD INDEX IDX_4D59FF6EDDEAB1A3 (etudiant_id)');
        $this->addSql('ALTER TABLE soutenance CHANGE salle_id salle_id INT DEFAULT NULL, CHANGE president_id president_id INT DEFAULT NULL, CHANGE examinateur_id examinateur_id INT DEFAULT NULL, CHANGE encadreur_id encadreur_id INT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE enseignant DROP INDEX IDX_81A72FA1F2C56620, ADD UNIQUE INDEX UNIQ_81A72FA1F2C56620 (compte_id)');
        $this->addSql('ALTER TABLE enseignant CHANGE nom nom VARCHAR(100) NOT NULL, CHANGE prenom prenom VARCHAR(100) NOT NULL, CHANGE email email VARCHAR(180) NOT NULL, CHANGE specialite specialite VARCHAR(150) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_81A72FA1E7927C74 ON enseignant (email)');
        $this->addSql('ALTER TABLE etudiant CHANGE nom nom VARCHAR(100) NOT NULL, CHANGE prenom prenom VARCHAR(100) NOT NULL, CHANGE email email VARCHAR(180) NOT NULL, CHANGE filiere filiere VARCHAR(100) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_717E22E3E7927C74 ON etudiant (email)');
        $this->addSql('ALTER TABLE salle DROP nom');
        $this->addSql('ALTER TABLE soutenance DROP INDEX IDX_4D59FF6EDDEAB1A3, ADD UNIQUE INDEX UNIQ_4D59FF6EDDEAB1A3 (etudiant_id)');
        $this->addSql('ALTER TABLE soutenance CHANGE salle_id salle_id INT NOT NULL, CHANGE president_id president_id INT NOT NULL, CHANGE examinateur_id examinateur_id INT NOT NULL, CHANGE encadreur_id encadreur_id INT NOT NULL');
    }
}
