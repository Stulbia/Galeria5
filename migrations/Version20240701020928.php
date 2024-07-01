<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240701020928 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE avatars (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, filename VARCHAR(191) NOT NULL, UNIQUE INDEX UNIQ_B0C98520A76ED395 (user_id), UNIQUE INDEX uq_avatars_id (filename), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE comments (id INT AUTO_INCREMENT NOT NULL, photo_id INT NOT NULL, user_id INT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', content VARCHAR(255) DEFAULT NULL, INDEX IDX_5F9E962A7E9E4C8C (photo_id), INDEX IDX_5F9E962AA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE galleries (id INT AUTO_INCREMENT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', title VARCHAR(64) NOT NULL, slug VARCHAR(64) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE galleries_users (gallery_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_EF22B1F24E7AF8F (gallery_id), INDEX IDX_EF22B1F2A76ED395 (user_id), PRIMARY KEY(gallery_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE photos (id INT AUTO_INCREMENT NOT NULL, gallery_id INT NOT NULL, author_id INT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', status VARCHAR(255) NOT NULL, title VARCHAR(255) NOT NULL, slug VARCHAR(255) DEFAULT NULL, description TINYTEXT DEFAULT NULL, fileName VARCHAR(191) NOT NULL, INDEX IDX_876E0D94E7AF8F (gallery_id), INDEX IDX_876E0D9F675F31B (author_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE photos_tags (photo_id INT NOT NULL, tag_id INT NOT NULL, INDEX IDX_F8A626C77E9E4C8C (photo_id), INDEX IDX_F8A626C7BAD26311 (tag_id), PRIMARY KEY(photo_id, tag_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE rating (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, photo_id INT NOT NULL, value DOUBLE PRECISION NOT NULL, INDEX IDX_D8892622A76ED395 (user_id), INDEX IDX_D88926227E9E4C8C (photo_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tags (id INT AUTO_INCREMENT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', slug VARCHAR(64) NOT NULL, title VARCHAR(64) NOT NULL, UNIQUE INDEX uq_tags_title (title), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE users (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(180) NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, banned TINYINT(1) NOT NULL, UNIQUE INDEX email_idx (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE avatars ADD CONSTRAINT FK_B0C98520A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE comments ADD CONSTRAINT FK_5F9E962A7E9E4C8C FOREIGN KEY (photo_id) REFERENCES photos (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE comments ADD CONSTRAINT FK_5F9E962AA76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE galleries_users ADD CONSTRAINT FK_EF22B1F24E7AF8F FOREIGN KEY (gallery_id) REFERENCES galleries (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE galleries_users ADD CONSTRAINT FK_EF22B1F2A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE photos ADD CONSTRAINT FK_876E0D94E7AF8F FOREIGN KEY (gallery_id) REFERENCES galleries (id)');
        $this->addSql('ALTER TABLE photos ADD CONSTRAINT FK_876E0D9F675F31B FOREIGN KEY (author_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE photos_tags ADD CONSTRAINT FK_F8A626C77E9E4C8C FOREIGN KEY (photo_id) REFERENCES photos (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE photos_tags ADD CONSTRAINT FK_F8A626C7BAD26311 FOREIGN KEY (tag_id) REFERENCES tags (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE rating ADD CONSTRAINT FK_D8892622A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE rating ADD CONSTRAINT FK_D88926227E9E4C8C FOREIGN KEY (photo_id) REFERENCES photos (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE avatars DROP FOREIGN KEY FK_B0C98520A76ED395');
        $this->addSql('ALTER TABLE comments DROP FOREIGN KEY FK_5F9E962A7E9E4C8C');
        $this->addSql('ALTER TABLE comments DROP FOREIGN KEY FK_5F9E962AA76ED395');
        $this->addSql('ALTER TABLE galleries_users DROP FOREIGN KEY FK_EF22B1F24E7AF8F');
        $this->addSql('ALTER TABLE galleries_users DROP FOREIGN KEY FK_EF22B1F2A76ED395');
        $this->addSql('ALTER TABLE photos DROP FOREIGN KEY FK_876E0D94E7AF8F');
        $this->addSql('ALTER TABLE photos DROP FOREIGN KEY FK_876E0D9F675F31B');
        $this->addSql('ALTER TABLE photos_tags DROP FOREIGN KEY FK_F8A626C77E9E4C8C');
        $this->addSql('ALTER TABLE photos_tags DROP FOREIGN KEY FK_F8A626C7BAD26311');
        $this->addSql('ALTER TABLE rating DROP FOREIGN KEY FK_D8892622A76ED395');
        $this->addSql('ALTER TABLE rating DROP FOREIGN KEY FK_D88926227E9E4C8C');
        $this->addSql('DROP TABLE avatars');
        $this->addSql('DROP TABLE comments');
        $this->addSql('DROP TABLE galleries');
        $this->addSql('DROP TABLE galleries_users');
        $this->addSql('DROP TABLE photos');
        $this->addSql('DROP TABLE photos_tags');
        $this->addSql('DROP TABLE rating');
        $this->addSql('DROP TABLE tags');
        $this->addSql('DROP TABLE users');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
