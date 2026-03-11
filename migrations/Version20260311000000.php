<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Ajout de l'authentification :
 * - Table "user" (email + password hashé + roles)
 * - Colonne owner_id nullable sur vehicle (SET NULL on delete user)
 *
 * IMPORTANT : les véhicules existants auront owner_id = NULL
 * et ne seront plus visibles après activation de l'auth.
 * Mettre à jour manuellement :
 *   UPDATE vehicle SET owner_id = <user_id> WHERE owner_id IS NULL;
 */
final class Version20260311000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add User entity and owner_id FK on Vehicle for authentication';
    }

    public function up(Schema $schema): void
    {
        // Séquence + table user (nom réservé PostgreSQL → guillemets obligatoires)
        $this->addSql('CREATE SEQUENCE user_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE "user" (
            id          INT          NOT NULL,
            email       VARCHAR(180) NOT NULL,
            roles       JSON         NOT NULL,
            password    VARCHAR(255) NOT NULL,
            display_name VARCHAR(100) DEFAULT NULL,
            PRIMARY KEY(id)
        )');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_EMAIL ON "user" (email)');

        // Lien vehicle → owner (nullable pour compat avec données existantes)
        $this->addSql('ALTER TABLE vehicle ADD owner_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE vehicle ADD CONSTRAINT FK_VEHICLE_OWNER
            FOREIGN KEY (owner_id) REFERENCES "user" (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_VEHICLE_OWNER ON vehicle (owner_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE vehicle DROP CONSTRAINT FK_VEHICLE_OWNER');
        $this->addSql('DROP INDEX IDX_VEHICLE_OWNER');
        $this->addSql('ALTER TABLE vehicle DROP owner_id');
        $this->addSql('DROP TABLE "user"');
        $this->addSql('DROP SEQUENCE user_id_seq CASCADE');
    }
}
