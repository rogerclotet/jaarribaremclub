<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20190225205404 extends AbstractMigration
{
    private const TABLE_NAME  = 'link';
    private const COLUMN_NAME = 'priority';

    public function getDescription() : string
    {
        return 'Add priority column in link table';
    }

    public function up(Schema $schema) : void
    {
        $table = $schema->getTable(self::TABLE_NAME);
        $table->addColumn(self::COLUMN_NAME, 'integer', ['default' => 0]);
    }

    public function down(Schema $schema) : void
    {
        $table = $schema->getTable(self::TABLE_NAME);
        $table->dropColumn(self::COLUMN_NAME);
    }
}
