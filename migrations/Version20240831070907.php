<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240831070907 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // Ajouter la colonne owner_id en la permettant NULL temporairement
        $this->addSql('ALTER TABLE camera ADD owner_id INT DEFAULT NULL');
       
        // Assigner un propriétaire par défaut (par exemple l'administrateur avec l'ID 2)
        $this->addSql('UPDATE camera SET owner_id = 2 WHERE owner_id IS NULL');
       
        // Modifier la colonne pour ne plus autoriser les valeurs NULL
        $this->addSql('ALTER TABLE camera MODIFY owner_id INT NOT NULL');
       
        // Ajouter la contrainte de clé étrangère
        $this->addSql('ALTER TABLE camera ADD CONSTRAINT FK_3B1CEE057E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_3B1CEE057E3C61F9 ON camera (owner_id)');
    }

    public function down(Schema $schema): void
    {
        // Ce code est généré automatiquement pour annuler les changements
        $this->addSql('ALTER TABLE camera DROP FOREIGN KEY FK_3B1CEE057E3C61F9');
        $this->addSql('DROP INDEX IDX_3B1CEE057E3C61F9 ON camera');
        $this->addSql('ALTER TABLE camera DROP owner_id');
    }
}
