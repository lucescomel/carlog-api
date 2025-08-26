<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250826133530 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE service_record_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE vehicle_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE service_record (id INT NOT NULL, vehicle_id INT NOT NULL, type VARCHAR(32) NOT NULL, custom_type VARCHAR(100) DEFAULT NULL, date DATE NOT NULL, mileage INT NOT NULL, cost INT DEFAULT NULL, notes TEXT DEFAULT NULL, next_due_mileage INT DEFAULT NULL, next_due_date DATE DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_A5F39AA7545317D1 ON service_record (vehicle_id)');
        $this->addSql('COMMENT ON COLUMN service_record.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE vehicle (id INT NOT NULL, name VARCHAR(100) NOT NULL, make VARCHAR(100) DEFAULT NULL, model VARCHAR(100) DEFAULT NULL, year INT DEFAULT NULL, plate VARCHAR(32) DEFAULT NULL, vin VARCHAR(32) DEFAULT NULL, odometer INT DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN vehicle.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE service_record ADD CONSTRAINT FK_A5F39AA7545317D1 FOREIGN KEY (vehicle_id) REFERENCES vehicle (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE service_record_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE vehicle_id_seq CASCADE');
        $this->addSql('ALTER TABLE service_record DROP CONSTRAINT FK_A5F39AA7545317D1');
        $this->addSql('DROP TABLE service_record');
        $this->addSql('DROP TABLE vehicle');
    }
}
