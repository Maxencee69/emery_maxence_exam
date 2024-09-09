<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240812153820 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        
        $this->addSql('ALTER TABLE camera ADD CONSTRAINT FK_3B1CEE0544F5D008 FOREIGN KEY (brand_id) REFERENCES brand (id)');
        $this->addSql('CREATE INDEX IDX_3B1CEE0544F5D008 ON camera (brand_id)');
        $this->addSql('ALTER TABLE manual DROP FOREIGN KEY FK_10DBBEC4A47890');
        $this->addSql('DROP INDEX IDX_10DBBEC4A47890 ON manual');
        $this->addSql('ALTER TABLE manual ADD camera_id INT NOT NULL, DROP camera_id_id');
        $this->addSql('ALTER TABLE manual ADD CONSTRAINT FK_10DBBEC4B47685CD FOREIGN KEY (camera_id) REFERENCES camera (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_10DBBEC4B47685CD ON manual (camera_id)');
        $this->addSql('ALTER TABLE photo DROP FOREIGN KEY FK_14B78418A47890');
        $this->addSql('DROP INDEX IDX_14B78418A47890 ON photo');
        $this->addSql('ALTER TABLE photo ADD camera_id INT NOT NULL, DROP camera_id_id');
        $this->addSql('ALTER TABLE photo ADD CONSTRAINT FK_14B78418B47685CD FOREIGN KEY (camera_id) REFERENCES camera (id)');
        $this->addSql('CREATE INDEX IDX_14B78418B47685CD ON photo (camera_id)');
    }

    public function down(Schema $schema): void
    {
        
        $this->addSql('ALTER TABLE camera DROP FOREIGN KEY FK_3B1CEE0544F5D008');
        $this->addSql('DROP INDEX IDX_3B1CEE0544F5D008 ON camera');
        $this->addSql('ALTER TABLE manual DROP FOREIGN KEY FK_10DBBEC4B47685CD');
        $this->addSql('DROP INDEX UNIQ_10DBBEC4B47685CD ON manual');
        $this->addSql('ALTER TABLE manual ADD camera_id_id INT DEFAULT NULL, DROP camera_id');
        $this->addSql('ALTER TABLE manual ADD CONSTRAINT FK_10DBBEC4A47890 FOREIGN KEY (camera_id_id) REFERENCES camera (id)');
        $this->addSql('CREATE INDEX IDX_10DBBEC4A47890 ON manual (camera_id_id)');
        $this->addSql('ALTER TABLE photo DROP FOREIGN KEY FK_14B78418B47685CD');
        $this->addSql('DROP INDEX IDX_14B78418B47685CD ON photo');
        $this->addSql('ALTER TABLE photo ADD camera_id_id INT DEFAULT NULL, DROP camera_id');
        $this->addSql('ALTER TABLE photo ADD CONSTRAINT FK_14B78418A47890 FOREIGN KEY (camera_id_id) REFERENCES camera (id)');
        $this->addSql('CREATE INDEX IDX_14B78418A47890 ON photo (camera_id_id)');
    }
}
