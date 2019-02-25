<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20190225014800 extends AbstractMigration
{
    private const TABLE_NAME = 'link';

    public function getDescription(): string
    {
        return 'Create new links table';
    }

    public function up(Schema $schema): void
    {
        $linksTable = $schema->createTable(self::TABLE_NAME);
        $linksTable->addColumn('id', 'integer', ['autoincrement' => true, 'unsigned' => true, 'notnull' => true]);
        $linksTable->addColumn('title', 'string', ['notnull' => true, 'length' => 255]);
        $linksTable->addColumn('description', 'string', ['notnull' => false, 'length' => 2047]);
        $linksTable->addColumn('url', 'string', ['notnull' => true, 'length' => 1023]);

        $linksTable->setPrimaryKey(['id']);
    }

    public function down(Schema $schema): void
    {
        $schema->dropTable(self::TABLE_NAME);
    }
}
