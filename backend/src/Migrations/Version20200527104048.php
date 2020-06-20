<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200527104048 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE app_group (id INT AUTO_INCREMENT NOT NULL, group_name VARCHAR(50) NOT NULL, group_image VARCHAR(1000) DEFAULT NULL, date_created DATETIME NOT NULL, date_modified DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE app_user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(50) NOT NULL, password VARCHAR(255) NOT NULL, phone_number VARCHAR(20) NOT NULL, profile_image VARCHAR(1000) DEFAULT NULL, date_created DATETIME NOT NULL, date_modified DATETIME NOT NULL, username VARCHAR(50) NOT NULL, is_employer TINYINT(1) NOT NULL, address VARCHAR(100) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE app_user_app_group (app_user_id INT NOT NULL, app_group_id INT NOT NULL, INDEX IDX_6409D6124A3353D8 (app_user_id), INDEX IDX_6409D612F6D4B9EA (app_group_id), PRIMARY KEY(app_user_id, app_group_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reservations (id INT AUTO_INCREMENT NOT NULL, restaurant_id INT NOT NULL, restaurant_table_id INT NOT NULL, name VARCHAR(50) NOT NULL, date_of_reservation DATE NOT NULL, time_of_reservation TIME NOT NULL, date_created DATETIME NOT NULL, date_modified DATETIME NOT NULL, number_of_guests INT NOT NULL, INDEX IDX_4DA239B1E7706E (restaurant_id), INDEX IDX_4DA239CC5AE6B3 (restaurant_table_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE restaurant (id INT AUTO_INCREMENT NOT NULL, restaurant_name VARCHAR(50) NOT NULL, restaurant_image VARCHAR(1000) DEFAULT NULL, date_created DATETIME NOT NULL, date_modified DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE restaurant_app_user (restaurant_id INT NOT NULL, app_user_id INT NOT NULL, INDEX IDX_A8183D06B1E7706E (restaurant_id), INDEX IDX_A8183D064A3353D8 (app_user_id), PRIMARY KEY(restaurant_id, app_user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE restaurant_table (id INT AUTO_INCREMENT NOT NULL, restaurant_id INT NOT NULL, table_name VARCHAR(50) NOT NULL, number_of_seats INT NOT NULL, table_type INT DEFAULT NULL, date_created DATETIME NOT NULL, date_modified DATETIME NOT NULL, INDEX IDX_BC343C97B1E7706E (restaurant_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE waiting_list (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(50) NOT NULL, date DATE NOT NULL, time TIME NOT NULL, number_of_guests INT NOT NULL, date_created DATETIME NOT NULL, date_modified DATETIME NOT NULL, latitude DOUBLE PRECISION DEFAULT NULL, longitude DOUBLE PRECISION DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE app_user_app_group ADD CONSTRAINT FK_6409D6124A3353D8 FOREIGN KEY (app_user_id) REFERENCES app_user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE app_user_app_group ADD CONSTRAINT FK_6409D612F6D4B9EA FOREIGN KEY (app_group_id) REFERENCES app_group (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE reservations ADD CONSTRAINT FK_4DA239B1E7706E FOREIGN KEY (restaurant_id) REFERENCES restaurant (id)');
        $this->addSql('ALTER TABLE reservations ADD CONSTRAINT FK_4DA239CC5AE6B3 FOREIGN KEY (restaurant_table_id) REFERENCES restaurant_table (id)');
        $this->addSql('ALTER TABLE restaurant_app_user ADD CONSTRAINT FK_A8183D06B1E7706E FOREIGN KEY (restaurant_id) REFERENCES restaurant (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE restaurant_app_user ADD CONSTRAINT FK_A8183D064A3353D8 FOREIGN KEY (app_user_id) REFERENCES app_user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE restaurant_table ADD CONSTRAINT FK_BC343C97B1E7706E FOREIGN KEY (restaurant_id) REFERENCES restaurant (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE app_user_app_group DROP FOREIGN KEY FK_6409D612F6D4B9EA');
        $this->addSql('ALTER TABLE app_user_app_group DROP FOREIGN KEY FK_6409D6124A3353D8');
        $this->addSql('ALTER TABLE restaurant_app_user DROP FOREIGN KEY FK_A8183D064A3353D8');
        $this->addSql('ALTER TABLE reservations DROP FOREIGN KEY FK_4DA239B1E7706E');
        $this->addSql('ALTER TABLE restaurant_app_user DROP FOREIGN KEY FK_A8183D06B1E7706E');
        $this->addSql('ALTER TABLE restaurant_table DROP FOREIGN KEY FK_BC343C97B1E7706E');
        $this->addSql('ALTER TABLE reservations DROP FOREIGN KEY FK_4DA239CC5AE6B3');
        $this->addSql('DROP TABLE app_group');
        $this->addSql('DROP TABLE app_user');
        $this->addSql('DROP TABLE app_user_app_group');
        $this->addSql('DROP TABLE reservations');
        $this->addSql('DROP TABLE restaurant');
        $this->addSql('DROP TABLE restaurant_app_user');
        $this->addSql('DROP TABLE restaurant_table');
        $this->addSql('DROP TABLE waiting_list');
    }
}
