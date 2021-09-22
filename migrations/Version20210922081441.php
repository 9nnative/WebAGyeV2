<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210922081441 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE publication_album (publication_id INT NOT NULL, album_id INT NOT NULL, INDEX IDX_DC4628CF38B217A7 (publication_id), INDEX IDX_DC4628CF1137ABCF (album_id), PRIMARY KEY(publication_id, album_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE publication_album ADD CONSTRAINT FK_DC4628CF38B217A7 FOREIGN KEY (publication_id) REFERENCES publication (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE publication_album ADD CONSTRAINT FK_DC4628CF1137ABCF FOREIGN KEY (album_id) REFERENCES album (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE publication ADD slug VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE user CHANGE roles roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE publication_album');
        $this->addSql('ALTER TABLE publication DROP slug');
        $this->addSql('ALTER TABLE `user` CHANGE roles roles LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_bin`');
    }
}
