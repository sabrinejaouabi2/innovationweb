<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231211200742 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE categorie (CatId INT AUTO_INCREMENT NOT NULL, CatLib VARCHAR(20) NOT NULL, PRIMARY KEY(CatId)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE commande (RefCom INT AUTO_INCREMENT NOT NULL, PrixTotal DOUBLE PRECISION NOT NULL, DateValidation DATE DEFAULT NULL, quantite INT NOT NULL, ProdId INT DEFAULT NULL, ClCIN INT DEFAULT NULL, INDEX fk_prod (ProdId), INDEX fk_client (ClCIN), PRIMARY KEY(RefCom)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE evaluation (id_eval INT AUTO_INCREMENT NOT NULL, nombreeval INT NOT NULL, PRIMARY KEY(id_eval)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE evenement (EventId INT AUTO_INCREMENT NOT NULL, EventNom VARCHAR(500) NOT NULL, EventTheme VARCHAR(500) NOT NULL, DateDebutEvent DATE NOT NULL, DateFinEvent DATE NOT NULL, EventAdresse VARCHAR(500) NOT NULL, EventDescription VARCHAR(500) NOT NULL, Eventimage VARCHAR(500) NOT NULL, PRIMARY KEY(EventId)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE favouris (idfav INT AUTO_INCREMENT NOT NULL, id INT DEFAULT NULL, EventId INT DEFAULT NULL, INDEX fkevent (EventId), INDEX fkusr (id), PRIMARY KEY(idfav)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE note (id_note_Service INT AUTO_INCREMENT NOT NULL, note DOUBLE PRECISION NOT NULL, id_user INT NOT NULL, ServId INT NOT NULL, INDEX note_voitures_ibfk_1 (id_user), INDEX note_voitures_ibfk_2 (ServId), PRIMARY KEY(id_note_Service)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE partenaire (idPart INT AUTO_INCREMENT NOT NULL, nomPart VARCHAR(30) NOT NULL, logoPart VARCHAR(30) NOT NULL, nbvue INT DEFAULT NULL, codePromo INT DEFAULT NULL, INDEX fk_part (codePromo), PRIMARY KEY(idPart)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE participation (id_part INT AUTO_INCREMENT NOT NULL, id INT DEFAULT NULL, date_part DATE NOT NULL, Nbplaces INT DEFAULT 5 NOT NULL, EventId INT DEFAULT NULL, INDEX fk_evnet (EventId), INDEX fk_user (id), PRIMARY KEY(id_part)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE produit (categorie INT DEFAULT NULL, ProdId INT AUTO_INCREMENT NOT NULL, ProdLib VARCHAR(30) NOT NULL, ProdDesc VARCHAR(100) NOT NULL, ProdPrix DOUBLE PRECISION NOT NULL, ProdImg VARCHAR(100) NOT NULL, stock INT DEFAULT NULL, FreeCIN INT DEFAULT NULL, INDEX IDX_29A5EC27637467B9 (FreeCIN), INDEX IDX_29A5EC27497DD634 (categorie), FULLTEXT INDEX IDX_29A5EC27CE86C189D6490B1 (prodlib, proddesc), PRIMARY KEY(ProdId)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE promotion (codepromo INT AUTO_INCREMENT NOT NULL, nomPromo VARCHAR(30) NOT NULL, dateDebutPromo DATE NOT NULL, dateFinPromo DATE NOT NULL, PRIMARY KEY(codepromo)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reclamation (RecId INT AUTO_INCREMENT NOT NULL, nom VARCHAR(500) NOT NULL, prenom VARCHAR(500) NOT NULL, tel INT NOT NULL, mail VARCHAR(500) NOT NULL, ReclDate DATE NOT NULL, ReclObject VARCHAR(500) NOT NULL, ReclDescription VARCHAR(500) NOT NULL, type VARCHAR(500) NOT NULL, PRIMARY KEY(RecId)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, prenom_user VARCHAR(255) NOT NULL, nom_user VARCHAR(255) NOT NULL, adresse_user VARCHAR(255) NOT NULL, reset_token VARCHAR(180) DEFAULT NULL, isBlocked TINYINT(1) NOT NULL, etat VARCHAR(10) DEFAULT \'Actif\', UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE commande ADD CONSTRAINT FK_6EEAA67D79A1CF5F FOREIGN KEY (ProdId) REFERENCES produit (ProdId)');
        $this->addSql('ALTER TABLE commande ADD CONSTRAINT FK_6EEAA67D6A82367D FOREIGN KEY (ClCIN) REFERENCES user (id)');
        $this->addSql('ALTER TABLE favouris ADD CONSTRAINT FK_EED923BCE49382F0 FOREIGN KEY (EventId) REFERENCES evenement (EventId)');
        $this->addSql('ALTER TABLE favouris ADD CONSTRAINT FK_EED923BCBF396750 FOREIGN KEY (id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE partenaire ADD CONSTRAINT FK_32FFA3731A1CAF25 FOREIGN KEY (codePromo) REFERENCES promotion (codepromo)');
        $this->addSql('ALTER TABLE participation ADD CONSTRAINT FK_AB55E24FE49382F0 FOREIGN KEY (EventId) REFERENCES evenement (EventId)');
        $this->addSql('ALTER TABLE participation ADD CONSTRAINT FK_AB55E24FBF396750 FOREIGN KEY (id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE produit ADD CONSTRAINT FK_29A5EC27637467B9 FOREIGN KEY (FreeCIN) REFERENCES user (id)');
        $this->addSql('ALTER TABLE produit ADD CONSTRAINT FK_29A5EC27497DD634 FOREIGN KEY (categorie) REFERENCES categorie (CatId)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commande DROP FOREIGN KEY FK_6EEAA67D79A1CF5F');
        $this->addSql('ALTER TABLE commande DROP FOREIGN KEY FK_6EEAA67D6A82367D');
        $this->addSql('ALTER TABLE favouris DROP FOREIGN KEY FK_EED923BCE49382F0');
        $this->addSql('ALTER TABLE favouris DROP FOREIGN KEY FK_EED923BCBF396750');
        $this->addSql('ALTER TABLE partenaire DROP FOREIGN KEY FK_32FFA3731A1CAF25');
        $this->addSql('ALTER TABLE participation DROP FOREIGN KEY FK_AB55E24FE49382F0');
        $this->addSql('ALTER TABLE participation DROP FOREIGN KEY FK_AB55E24FBF396750');
        $this->addSql('ALTER TABLE produit DROP FOREIGN KEY FK_29A5EC27637467B9');
        $this->addSql('ALTER TABLE produit DROP FOREIGN KEY FK_29A5EC27497DD634');
        $this->addSql('DROP TABLE categorie');
        $this->addSql('DROP TABLE commande');
        $this->addSql('DROP TABLE evaluation');
        $this->addSql('DROP TABLE evenement');
        $this->addSql('DROP TABLE favouris');
        $this->addSql('DROP TABLE note');
        $this->addSql('DROP TABLE partenaire');
        $this->addSql('DROP TABLE participation');
        $this->addSql('DROP TABLE produit');
        $this->addSql('DROP TABLE promotion');
        $this->addSql('DROP TABLE reclamation');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
