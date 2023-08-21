<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230821135413 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE carousel (id INT AUTO_INCREMENT NOT NULL, first_id INT DEFAULT NULL, second_id INT DEFAULT NULL, third_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_1DD74700E84D625F (first_id), UNIQUE INDEX UNIQ_1DD74700FF961BCC (second_id), UNIQUE INDEX UNIQ_1DD7470074CCD3CA (third_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE carousel ADD CONSTRAINT FK_1DD74700E84D625F FOREIGN KEY (first_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE carousel ADD CONSTRAINT FK_1DD74700FF961BCC FOREIGN KEY (second_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE carousel ADD CONSTRAINT FK_1DD7470074CCD3CA FOREIGN KEY (third_id) REFERENCES product (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE carousel DROP FOREIGN KEY FK_1DD74700E84D625F');
        $this->addSql('ALTER TABLE carousel DROP FOREIGN KEY FK_1DD74700FF961BCC');
        $this->addSql('ALTER TABLE carousel DROP FOREIGN KEY FK_1DD7470074CCD3CA');
        $this->addSql('DROP TABLE carousel');
    }
}
