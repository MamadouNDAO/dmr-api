<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240223151622 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE news (id INT AUTO_INCREMENT NOT NULL, titre VARCHAR(255) NOT NULL, image VARCHAR(255) DEFAULT NULL, video VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE news_tag (news_id INT NOT NULL, tag_id INT NOT NULL, INDEX IDX_BE3ED8A1B5A459A0 (news_id), INDEX IDX_BE3ED8A1BAD26311 (tag_id), PRIMARY KEY(news_id, tag_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE paragraphe (id INT AUTO_INCREMENT NOT NULL, news_id INT NOT NULL, content VARCHAR(255) NOT NULL, INDEX IDX_4C1BA9B6B5A459A0 (news_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tag (id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE news_tag ADD CONSTRAINT FK_BE3ED8A1B5A459A0 FOREIGN KEY (news_id) REFERENCES news (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE news_tag ADD CONSTRAINT FK_BE3ED8A1BAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE paragraphe ADD CONSTRAINT FK_4C1BA9B6B5A459A0 FOREIGN KEY (news_id) REFERENCES news (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE news_tag DROP FOREIGN KEY FK_BE3ED8A1B5A459A0');
        $this->addSql('ALTER TABLE news_tag DROP FOREIGN KEY FK_BE3ED8A1BAD26311');
        $this->addSql('ALTER TABLE paragraphe DROP FOREIGN KEY FK_4C1BA9B6B5A459A0');
        $this->addSql('DROP TABLE news');
        $this->addSql('DROP TABLE news_tag');
        $this->addSql('DROP TABLE paragraphe');
        $this->addSql('DROP TABLE tag');
    }
}
