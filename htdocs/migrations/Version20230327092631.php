<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230327092631 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE post (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255), rate INT NOT NULL NOT NULL, base_cost FLOAT NOT NULL, language_cost FLOAT NOT NULL, channel_cost FLOAT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('INSERT INTO post (name, rate, base_cost, language_cost, channel_cost) VALUES ("Copywriting", 120, 0.75, 0.25, 0.16)');
        $this->addSql('INSERT INTO post (name, rate, base_cost, language_cost, channel_cost) VALUES ("Design (statisch)", 105, 0.5, 0.15, 0.25)');
        $this->addSql('INSERT INTO post (name, rate, base_cost, language_cost, channel_cost) VALUES ("Design (Small animation)", 105, 1, 0.5, 0.5)');
        $this->addSql('INSERT INTO post (name, rate, base_cost, language_cost, channel_cost) VALUES ("Design (Advanced animation)", 105, 3, 0.5, 0.5)');
        $this->addSql('INSERT INTO post (name, rate, base_cost, language_cost, channel_cost) VALUES ("Design (Video montage)", 105, 4, 2, 1)');
        $this->addSql('INSERT INTO post (name, rate, base_cost, language_cost, channel_cost) VALUES ("Design (Video shoot)", 105, 3, 1.5, 0.75)');
        $this->addSql('INSERT INTO post (name, rate, base_cost, language_cost, channel_cost) VALUES ("Organische publicatie", 105, 0.25, 0.25, 0.25)');
        $this->addSql('INSERT INTO post (name, rate, base_cost, language_cost, channel_cost) VALUES ("Advertentie + reporting", 105, 0.5, 0.25, 0.25)');

    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE post');
    }
}
