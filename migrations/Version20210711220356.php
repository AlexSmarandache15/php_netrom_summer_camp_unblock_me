<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210711220356 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE activity CHANGE blocker blocker VARCHAR(25) NOT NULL, CHANGE blockee blockee VARCHAR(25) NOT NULL, CHANGE status status INT NOT NULL');
        $this->addSql('ALTER TABLE license_plate DROP FOREIGN KEY FK_F5AA79D09D86650F');
        $this->addSql('DROP INDEX IDX_F5AA79D09D86650F ON license_plate');
        $this->addSql('ALTER TABLE license_plate CHANGE license_plate license_plate VARCHAR(25) NOT NULL, CHANGE user_id_id user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE license_plate ADD CONSTRAINT FK_F5AA79D0A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('CREATE INDEX IDX_F5AA79D0A76ED395 ON license_plate (user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE activity CHANGE blocker blocker VARCHAR(100) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, CHANGE blockee blockee VARCHAR(100) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, CHANGE status status INT DEFAULT 0 NOT NULL');
        $this->addSql('ALTER TABLE license_plate DROP FOREIGN KEY FK_F5AA79D0A76ED395');
        $this->addSql('DROP INDEX IDX_F5AA79D0A76ED395 ON license_plate');
        $this->addSql('ALTER TABLE license_plate CHANGE license_plate license_plate VARCHAR(50) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE user_id user_id_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE license_plate ADD CONSTRAINT FK_F5AA79D09D86650F FOREIGN KEY (user_id_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_F5AA79D09D86650F ON license_plate (user_id_id)');
    }
}
