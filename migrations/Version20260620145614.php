<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260620145614 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE rental (id INT AUTO_INCREMENT NOT NULL, borrower_name VARCHAR(255) NOT NULL, rented_at DATETIME NOT NULL, returned_at DATETIME DEFAULT NULL, quantity INT NOT NULL, status VARCHAR(20) DEFAULT \'PENDING\' NOT NULL, resource_id INT DEFAULT NULL, user_id INT DEFAULT NULL, INDEX IDX_1619C27D89329D25 (resource_id), INDEX IDX_1619C27DA76ED395 (user_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE resource (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, author VARCHAR(255) NOT NULL, type VARCHAR(50) NOT NULL, quantity INT NOT NULL, category_id INT NOT NULL, INDEX IDX_BC91F41612469DE2 (category_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE resource_tag (resource_id INT NOT NULL, tag_id INT NOT NULL, INDEX IDX_23D039CA89329D25 (resource_id), INDEX IDX_23D039CABAD26311 (tag_id), PRIMARY KEY (resource_id, tag_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE tag (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, is_verified TINYINT NOT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 (queue_name, available_at, delivered_at, id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE rental ADD CONSTRAINT FK_1619C27D89329D25 FOREIGN KEY (resource_id) REFERENCES resource (id)');
        $this->addSql('ALTER TABLE rental ADD CONSTRAINT FK_1619C27DA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE resource ADD CONSTRAINT FK_BC91F41612469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE resource_tag ADD CONSTRAINT FK_23D039CA89329D25 FOREIGN KEY (resource_id) REFERENCES resource (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE resource_tag ADD CONSTRAINT FK_23D039CABAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE rental DROP FOREIGN KEY FK_1619C27D89329D25');
        $this->addSql('ALTER TABLE rental DROP FOREIGN KEY FK_1619C27DA76ED395');
        $this->addSql('ALTER TABLE resource DROP FOREIGN KEY FK_BC91F41612469DE2');
        $this->addSql('ALTER TABLE resource_tag DROP FOREIGN KEY FK_23D039CA89329D25');
        $this->addSql('ALTER TABLE resource_tag DROP FOREIGN KEY FK_23D039CABAD26311');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE rental');
        $this->addSql('DROP TABLE resource');
        $this->addSql('DROP TABLE resource_tag');
        $this->addSql('DROP TABLE tag');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
