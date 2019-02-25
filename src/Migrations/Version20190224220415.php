<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190224220415 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('sqlite' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('CREATE TABLE link (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, title VARCHAR(255) NOT NULL, description VARCHAR(2047) DEFAULT NULL, url VARCHAR(1023) NOT NULL, image VARCHAR(1023) DEFAULT NULL)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__next_caminada AS SELECT id, number, datetime, place FROM next_caminada');
        $this->addSql('DROP TABLE next_caminada');
        $this->addSql('CREATE TABLE next_caminada (id INTEGER NOT NULL, number INTEGER NOT NULL, datetime DATETIME NOT NULL, place VARCHAR(255) NOT NULL COLLATE BINARY, PRIMARY KEY(id))');
        $this->addSql('INSERT INTO next_caminada (id, number, datetime, place) SELECT id, number, datetime, place FROM __temp__next_caminada');
        $this->addSql('DROP TABLE __temp__next_caminada');
        $this->addSql('DROP INDEX UNIQ_39986E4390154576');
        $this->addSql('CREATE TEMPORARY TABLE __temp__album AS SELECT id, caminada_id FROM album');
        $this->addSql('DROP TABLE album');
        $this->addSql('CREATE TABLE album (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, caminada_id INTEGER DEFAULT NULL, CONSTRAINT FK_39986E4390154576 FOREIGN KEY (caminada_id) REFERENCES caminada (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO album (id, caminada_id) SELECT id, caminada_id FROM __temp__album');
        $this->addSql('DROP TABLE __temp__album');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_39986E4390154576 ON album (caminada_id)');
        $this->addSql('DROP INDEX UNIQ_876E0D993CB796C');
        $this->addSql('DROP INDEX IDX_876E0D91137ABCF');
        $this->addSql('CREATE TEMPORARY TABLE __temp__photos AS SELECT album_id, file_id FROM photos');
        $this->addSql('DROP TABLE photos');
        $this->addSql('CREATE TABLE photos (album_id INTEGER NOT NULL, file_id INTEGER NOT NULL, PRIMARY KEY(album_id, file_id), CONSTRAINT FK_876E0D91137ABCF FOREIGN KEY (album_id) REFERENCES album (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_876E0D993CB796C FOREIGN KEY (file_id) REFERENCES file (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO photos (album_id, file_id) SELECT album_id, file_id FROM __temp__photos');
        $this->addSql('DROP TABLE __temp__photos');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_876E0D993CB796C ON photos (file_id)');
        $this->addSql('CREATE INDEX IDX_876E0D91137ABCF ON photos (album_id)');
        $this->addSql('DROP INDEX UNIQ_5A8A6C8D3DA5256D');
        $this->addSql('CREATE TEMPORARY TABLE __temp__post AS SELECT id, image_id, created_at, title, text FROM post');
        $this->addSql('DROP TABLE post');
        $this->addSql('CREATE TABLE post (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, image_id INTEGER DEFAULT NULL, created_at DATETIME NOT NULL, title CLOB NOT NULL COLLATE BINARY, text CLOB NOT NULL COLLATE BINARY, CONSTRAINT FK_5A8A6C8D3DA5256D FOREIGN KEY (image_id) REFERENCES file (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO post (id, image_id, created_at, title, text) SELECT id, image_id, created_at, title, text FROM __temp__post');
        $this->addSql('DROP TABLE __temp__post');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_5A8A6C8D3DA5256D ON post (image_id)');
        $this->addSql('DROP INDEX UNIQ_635405993CB796C');
        $this->addSql('DROP INDEX IDX_63540594B89032C');
        $this->addSql('CREATE TEMPORARY TABLE __temp__files AS SELECT post_id, file_id FROM files');
        $this->addSql('DROP TABLE files');
        $this->addSql('CREATE TABLE files (post_id INTEGER NOT NULL, file_id INTEGER NOT NULL, PRIMARY KEY(post_id, file_id), CONSTRAINT FK_63540594B89032C FOREIGN KEY (post_id) REFERENCES post (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_635405993CB796C FOREIGN KEY (file_id) REFERENCES file (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO files (post_id, file_id) SELECT post_id, file_id FROM __temp__files');
        $this->addSql('DROP TABLE __temp__files');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_635405993CB796C ON files (file_id)');
        $this->addSql('CREATE INDEX IDX_63540594B89032C ON files (post_id)');
        $this->addSql('DROP INDEX UNIQ_9F07E8925ED23C43');
        $this->addSql('DROP INDEX UNIQ_9F07E892993B8D6C');
        $this->addSql('DROP INDEX UNIQ_9F07E892579BDAE2');
        $this->addSql('DROP INDEX UNIQ_9F07E89253C55F64');
        $this->addSql('DROP INDEX UNIQ_9F07E8923DA5256D');
        $this->addSql('DROP INDEX UNIQ_9F07E892BB827337');
        $this->addSql('DROP INDEX UNIQ_9F07E89296901F54');
        $this->addSql('CREATE TEMPORARY TABLE __temp__caminada AS SELECT id, image_id, map_id, elevation_id, leaflet_id, track_id, path, description, notes, number, year FROM caminada');
        $this->addSql('DROP TABLE caminada');
        $this->addSql('CREATE TABLE caminada (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, image_id INTEGER DEFAULT NULL, map_id INTEGER DEFAULT NULL, elevation_id INTEGER DEFAULT NULL, leaflet_id INTEGER DEFAULT NULL, track_id INTEGER DEFAULT NULL, path CLOB NOT NULL COLLATE BINARY --(DC2Type:array)
        , description CLOB NOT NULL COLLATE BINARY, notes CLOB DEFAULT NULL COLLATE BINARY, number INTEGER NOT NULL, year INTEGER NOT NULL, CONSTRAINT FK_9F07E8923DA5256D FOREIGN KEY (image_id) REFERENCES file (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_9F07E89253C55F64 FOREIGN KEY (map_id) REFERENCES file (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_9F07E892579BDAE2 FOREIGN KEY (elevation_id) REFERENCES file (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_9F07E892993B8D6C FOREIGN KEY (leaflet_id) REFERENCES file (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_9F07E8925ED23C43 FOREIGN KEY (track_id) REFERENCES file (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO caminada (id, image_id, map_id, elevation_id, leaflet_id, track_id, path, description, notes, number, year) SELECT id, image_id, map_id, elevation_id, leaflet_id, track_id, path, description, notes, number, year FROM __temp__caminada');
        $this->addSql('DROP TABLE __temp__caminada');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_9F07E8925ED23C43 ON caminada (track_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_9F07E892993B8D6C ON caminada (leaflet_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_9F07E892579BDAE2 ON caminada (elevation_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_9F07E89253C55F64 ON caminada (map_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_9F07E8923DA5256D ON caminada (image_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_9F07E892BB827337 ON caminada (year)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_9F07E89296901F54 ON caminada (number)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('sqlite' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('DROP TABLE link');
        $this->addSql('DROP INDEX UNIQ_39986E4390154576');
        $this->addSql('CREATE TEMPORARY TABLE __temp__album AS SELECT id, caminada_id FROM album');
        $this->addSql('DROP TABLE album');
        $this->addSql('CREATE TABLE album (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, caminada_id INTEGER DEFAULT NULL)');
        $this->addSql('INSERT INTO album (id, caminada_id) SELECT id, caminada_id FROM __temp__album');
        $this->addSql('DROP TABLE __temp__album');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_39986E4390154576 ON album (caminada_id)');
        $this->addSql('DROP INDEX UNIQ_9F07E89296901F54');
        $this->addSql('DROP INDEX UNIQ_9F07E892BB827337');
        $this->addSql('DROP INDEX UNIQ_9F07E8923DA5256D');
        $this->addSql('DROP INDEX UNIQ_9F07E89253C55F64');
        $this->addSql('DROP INDEX UNIQ_9F07E892579BDAE2');
        $this->addSql('DROP INDEX UNIQ_9F07E892993B8D6C');
        $this->addSql('DROP INDEX UNIQ_9F07E8925ED23C43');
        $this->addSql('CREATE TEMPORARY TABLE __temp__caminada AS SELECT id, image_id, map_id, elevation_id, leaflet_id, track_id, path, description, notes, number, year FROM caminada');
        $this->addSql('DROP TABLE caminada');
        $this->addSql('CREATE TABLE caminada (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, image_id INTEGER DEFAULT NULL, map_id INTEGER DEFAULT NULL, elevation_id INTEGER DEFAULT NULL, leaflet_id INTEGER DEFAULT NULL, track_id INTEGER DEFAULT NULL, path CLOB NOT NULL --(DC2Type:array)
        , description CLOB NOT NULL, notes CLOB DEFAULT NULL, number INTEGER NOT NULL, year INTEGER NOT NULL)');
        $this->addSql('INSERT INTO caminada (id, image_id, map_id, elevation_id, leaflet_id, track_id, path, description, notes, number, year) SELECT id, image_id, map_id, elevation_id, leaflet_id, track_id, path, description, notes, number, year FROM __temp__caminada');
        $this->addSql('DROP TABLE __temp__caminada');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_9F07E89296901F54 ON caminada (number)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_9F07E892BB827337 ON caminada (year)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_9F07E8923DA5256D ON caminada (image_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_9F07E89253C55F64 ON caminada (map_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_9F07E892579BDAE2 ON caminada (elevation_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_9F07E892993B8D6C ON caminada (leaflet_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_9F07E8925ED23C43 ON caminada (track_id)');
        $this->addSql('DROP INDEX IDX_63540594B89032C');
        $this->addSql('DROP INDEX UNIQ_635405993CB796C');
        $this->addSql('CREATE TEMPORARY TABLE __temp__files AS SELECT post_id, file_id FROM files');
        $this->addSql('DROP TABLE files');
        $this->addSql('CREATE TABLE files (post_id INTEGER NOT NULL, file_id INTEGER NOT NULL, PRIMARY KEY(post_id, file_id))');
        $this->addSql('INSERT INTO files (post_id, file_id) SELECT post_id, file_id FROM __temp__files');
        $this->addSql('DROP TABLE __temp__files');
        $this->addSql('CREATE INDEX IDX_63540594B89032C ON files (post_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_635405993CB796C ON files (file_id)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__next_caminada AS SELECT id, number, datetime, place FROM next_caminada');
        $this->addSql('DROP TABLE next_caminada');
        $this->addSql('CREATE TABLE next_caminada (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, number INTEGER NOT NULL, datetime DATETIME NOT NULL, place VARCHAR(255) NOT NULL)');
        $this->addSql('INSERT INTO next_caminada (id, number, datetime, place) SELECT id, number, datetime, place FROM __temp__next_caminada');
        $this->addSql('DROP TABLE __temp__next_caminada');
        $this->addSql('DROP INDEX IDX_876E0D91137ABCF');
        $this->addSql('DROP INDEX UNIQ_876E0D993CB796C');
        $this->addSql('CREATE TEMPORARY TABLE __temp__photos AS SELECT album_id, file_id FROM photos');
        $this->addSql('DROP TABLE photos');
        $this->addSql('CREATE TABLE photos (album_id INTEGER NOT NULL, file_id INTEGER NOT NULL, PRIMARY KEY(album_id, file_id))');
        $this->addSql('INSERT INTO photos (album_id, file_id) SELECT album_id, file_id FROM __temp__photos');
        $this->addSql('DROP TABLE __temp__photos');
        $this->addSql('CREATE INDEX IDX_876E0D91137ABCF ON photos (album_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_876E0D993CB796C ON photos (file_id)');
        $this->addSql('DROP INDEX UNIQ_5A8A6C8D3DA5256D');
        $this->addSql('CREATE TEMPORARY TABLE __temp__post AS SELECT id, image_id, created_at, title, text FROM post');
        $this->addSql('DROP TABLE post');
        $this->addSql('CREATE TABLE post (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, image_id INTEGER DEFAULT NULL, created_at DATETIME NOT NULL, title CLOB NOT NULL, text CLOB NOT NULL)');
        $this->addSql('INSERT INTO post (id, image_id, created_at, title, text) SELECT id, image_id, created_at, title, text FROM __temp__post');
        $this->addSql('DROP TABLE __temp__post');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_5A8A6C8D3DA5256D ON post (image_id)');
    }
}
