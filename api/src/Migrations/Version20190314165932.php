<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190314165932 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE team_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE challenge_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE "user_id_seq" INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE team (id INT NOT NULL, name VARCHAR(255) NOT NULL, score INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE team_user (team_id INT NOT NULL, user_id INT NOT NULL, PRIMARY KEY(team_id, user_id))');
        $this->addSql('CREATE INDEX IDX_5C722232296CD8AE ON team_user (team_id)');
        $this->addSql('CREATE INDEX IDX_5C722232A76ED395 ON team_user (user_id)');
        $this->addSql('CREATE TABLE challenge (id INT NOT NULL, name VARCHAR(255) NOT NULL, activities_group VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE challenge_team (challenge_id INT NOT NULL, team_id INT NOT NULL, PRIMARY KEY(challenge_id, team_id))');
        $this->addSql('CREATE INDEX IDX_CD4FA19998A21AC6 ON challenge_team (challenge_id)');
        $this->addSql('CREATE INDEX IDX_CD4FA199296CD8AE ON challenge_team (team_id)');
        $this->addSql('CREATE TABLE "user" (id INT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, decathlon_connect_id VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON "user" (email)');
        $this->addSql('ALTER TABLE team_user ADD CONSTRAINT FK_5C722232296CD8AE FOREIGN KEY (team_id) REFERENCES team (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE team_user ADD CONSTRAINT FK_5C722232A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE challenge_team ADD CONSTRAINT FK_CD4FA19998A21AC6 FOREIGN KEY (challenge_id) REFERENCES challenge (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE challenge_team ADD CONSTRAINT FK_CD4FA199296CD8AE FOREIGN KEY (team_id) REFERENCES team (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE team_user DROP CONSTRAINT FK_5C722232296CD8AE');
        $this->addSql('ALTER TABLE challenge_team DROP CONSTRAINT FK_CD4FA199296CD8AE');
        $this->addSql('ALTER TABLE challenge_team DROP CONSTRAINT FK_CD4FA19998A21AC6');
        $this->addSql('ALTER TABLE team_user DROP CONSTRAINT FK_5C722232A76ED395');
        $this->addSql('DROP SEQUENCE team_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE challenge_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE "user_id_seq" CASCADE');
        $this->addSql('DROP TABLE team');
        $this->addSql('DROP TABLE team_user');
        $this->addSql('DROP TABLE challenge');
        $this->addSql('DROP TABLE challenge_team');
        $this->addSql('DROP TABLE "user"');
    }
}
