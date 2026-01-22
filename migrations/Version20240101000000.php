<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240101000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Création des tables user, theme et discussion';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE `user` (
            id INT AUTO_INCREMENT NOT NULL,
            email VARCHAR(180) NOT NULL,
            roles JSON NOT NULL,
            password VARCHAR(255) NOT NULL,
            pseudo VARCHAR(50) NOT NULL,
            nom VARCHAR(100) DEFAULT NULL,
            prenom VARCHAR(100) DEFAULT NULL,
            age INT DEFAULT NULL,
            telephone VARCHAR(20) DEFAULT NULL,
            ville VARCHAR(100) DEFAULT NULL,
            confirmation_token VARCHAR(255) DEFAULT NULL,
            token_expires_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\',
            is_verified TINYINT(1) NOT NULL,
            created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\',
            last_activity_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\',
            login_attempts INT NOT NULL DEFAULT 0,
            last_failed_login DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\',
            UNIQUE INDEX UNIQ_8D93D649E7927C74 (email),
            UNIQUE INDEX UNIQ_8D93D64986CC499D (pseudo),
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql('CREATE TABLE theme (
            id INT AUTO_INCREMENT NOT NULL,
            titre VARCHAR(255) NOT NULL,
            description LONGTEXT DEFAULT NULL,
            created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\',
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql('CREATE TABLE discussion (
            id INT AUTO_INCREMENT NOT NULL,
            auteur_id INT NOT NULL,
            theme_id INT NOT NULL,
            contenu LONGTEXT NOT NULL,
            created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\',
            updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\',
            INDEX IDX_C0B9F90F60BB6FE6 (auteur_id),
            INDEX IDX_C0B9F90F59027487 (theme_id),
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql('ALTER TABLE discussion ADD CONSTRAINT FK_C0B9F90F60BB6FE6 FOREIGN KEY (auteur_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE discussion ADD CONSTRAINT FK_C0B9F90F59027487 FOREIGN KEY (theme_id) REFERENCES theme (id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE discussion DROP FOREIGN KEY FK_C0B9F90F60BB6FE6');
        $this->addSql('ALTER TABLE discussion DROP FOREIGN KEY FK_C0B9F90F59027487');
        $this->addSql('DROP TABLE discussion');
        $this->addSql('DROP TABLE theme');
        $this->addSql('DROP TABLE `user`');
    }
}
