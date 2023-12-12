<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231211204456 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user_interaction (user_id INT NOT NULL, interacted_user_id INT NOT NULL, liked TINYINT(1) NOT NULL, INDEX IDX_9E963432A76ED395 (user_id), INDEX IDX_9E96343279EAAD39 (interacted_user_id), PRIMARY KEY(user_id, interacted_user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_interaction ADD CONSTRAINT FK_9E963432A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_interaction ADD CONSTRAINT FK_9E96343279EAAD39 FOREIGN KEY (interacted_user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_interaction DROP FOREIGN KEY FK_9E963432A76ED395');
        $this->addSql('ALTER TABLE user_interaction DROP FOREIGN KEY FK_9E96343279EAAD39');
        $this->addSql('DROP TABLE user_interaction');
    }
}
