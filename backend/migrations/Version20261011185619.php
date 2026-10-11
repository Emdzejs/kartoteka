<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261011185619 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('
            CREATE TABLE employees (
                id UUID NOT NULL,
                identity_number VARCHAR(11) NOT NULL,
                first_name VARCHAR(100) NOT NULL,
                last_name VARCHAR(100) NOT NULL,
                gender VARCHAR(6) NOT NULL,
                email VARCHAR(254) NOT NULL,
                phone_number VARCHAR(16) NOT NULL,
                created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
                updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
                address_country_code VARCHAR(2) NOT NULL,
                address_postal_code VARCHAR(20) NOT NULL,
                address_city VARCHAR(120) NOT NULL,
                address_street VARCHAR(255) NOT NULL,
                address_street_number VARCHAR(20) NOT NULL,
                PRIMARY KEY (id)
            )'
        );

        $this->addSql(
            'CREATE UNIQUE INDEX UNIQ_BA82C3002BC7B602 ON employees (identity_number)'
        );

        $this->addSql(
        'CREATE TABLE previous_employments (
                id UUID NOT NULL,
                business_registration_number VARCHAR(50) NOT NULL,
                business_name VARCHAR(100) NOT NULL,
                role VARCHAR(100) NOT NULL,
                created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
                updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
                start_date DATE NOT NULL,
                end_date DATE NOT NULL,
                business_address_country_code VARCHAR(2) NOT NULL,
                business_address_postal_code VARCHAR(20) NOT NULL,
                business_address_city VARCHAR(120) NOT NULL,
                business_address_street VARCHAR(255) NOT NULL,
                business_address_street_number VARCHAR(20) NOT NULL,
                employee_id UUID NOT NULL,
                PRIMARY KEY (id)
            )'
        );

        $this->addSql('CREATE INDEX IDX_57AFD78B8C03F15C ON previous_employments (employee_id)');
        $this->addSql('ALTER TABLE previous_employments ADD CONSTRAINT FK_57AFD78B8C03F15C FOREIGN KEY (employee_id) REFERENCES employees (id) ON DELETE CASCADE NOT DEFERRABLE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE previous_employments DROP CONSTRAINT FK_57AFD78B8C03F15C');
        $this->addSql('DROP TABLE employees');
        $this->addSql('DROP TABLE previous_employments');
    }
}
