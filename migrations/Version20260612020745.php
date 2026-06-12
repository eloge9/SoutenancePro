<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260612020745 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE enseignant (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(100) NOT NULL, prenom VARCHAR(100) NOT NULL, email VARCHAR(180) NOT NULL, specialite VARCHAR(150) NOT NULL, compte_id INT NOT NULL, UNIQUE INDEX UNIQ_81A72FA1E7927C74 (email), UNIQUE INDEX UNIQ_81A72FA1F2C56620 (compte_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE etudiant (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(100) NOT NULL, prenom VARCHAR(100) NOT NULL, email VARCHAR(180) NOT NULL, filiere VARCHAR(100) NOT NULL, theme_memoire VARCHAR(255) NOT NULL, fichier_memoire VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_717E22E3E7927C74 (email), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE salle (id INT AUTO_INCREMENT NOT NULL, code VARCHAR(50) NOT NULL, capacite INT NOT NULL, localisation VARCHAR(255) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE soutenance (id INT AUTO_INCREMENT NOT NULL, date DATE NOT NULL, heure TIME NOT NULL, etudiant_id INT NOT NULL, salle_id INT NOT NULL, president_id INT NOT NULL, examinateur_id INT NOT NULL, encadreur_id INT NOT NULL, UNIQUE INDEX UNIQ_4D59FF6EDDEAB1A3 (etudiant_id), INDEX IDX_4D59FF6EDC304035 (salle_id), INDEX IDX_4D59FF6EB40A33C7 (president_id), INDEX IDX_4D59FF6E9D8D68C0 (examinateur_id), INDEX IDX_4D59FF6EA625A0FD (encadreur_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 (queue_name, available_at, delivered_at, id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE enseignant ADD CONSTRAINT FK_81A72FA1F2C56620 FOREIGN KEY (compte_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE soutenance ADD CONSTRAINT FK_4D59FF6EDDEAB1A3 FOREIGN KEY (etudiant_id) REFERENCES etudiant (id)');
        $this->addSql('ALTER TABLE soutenance ADD CONSTRAINT FK_4D59FF6EDC304035 FOREIGN KEY (salle_id) REFERENCES salle (id)');
        $this->addSql('ALTER TABLE soutenance ADD CONSTRAINT FK_4D59FF6EB40A33C7 FOREIGN KEY (president_id) REFERENCES enseignant (id)');
        $this->addSql('ALTER TABLE soutenance ADD CONSTRAINT FK_4D59FF6E9D8D68C0 FOREIGN KEY (examinateur_id) REFERENCES enseignant (id)');
        $this->addSql('ALTER TABLE soutenance ADD CONSTRAINT FK_4D59FF6EA625A0FD FOREIGN KEY (encadreur_id) REFERENCES enseignant (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE enseignant DROP FOREIGN KEY FK_81A72FA1F2C56620');
        $this->addSql('ALTER TABLE soutenance DROP FOREIGN KEY FK_4D59FF6EDDEAB1A3');
        $this->addSql('ALTER TABLE soutenance DROP FOREIGN KEY FK_4D59FF6EDC304035');
        $this->addSql('ALTER TABLE soutenance DROP FOREIGN KEY FK_4D59FF6EB40A33C7');
        $this->addSql('ALTER TABLE soutenance DROP FOREIGN KEY FK_4D59FF6E9D8D68C0');
        $this->addSql('ALTER TABLE soutenance DROP FOREIGN KEY FK_4D59FF6EA625A0FD');
        $this->addSql('DROP TABLE enseignant');
        $this->addSql('DROP TABLE etudiant');
        $this->addSql('DROP TABLE salle');
        $this->addSql('DROP TABLE soutenance');
        $this->addSql('DROP TABLE `user`');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
