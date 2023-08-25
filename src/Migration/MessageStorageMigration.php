<?php
declare(strict_types=1);

namespace App\Migration;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use EventSauce\MessageRepository\TableSchema\DefaultTableSchema;
use EventSauce\MessageRepository\TableSchema\TableSchema;

abstract class MessageStorageMigration extends AbstractMigration
{
    abstract public function getTableName(): string;

    protected static function getTableSchema(): TableSchema
    {
        return new DefaultTableSchema();
    }

    public function getDescription(): string
    {
        return sprintf('Create %s event store', $this->getTableName());
    }

    /**
     * @see https://eventsauce.io/docs/message-storage/repository-table-schema/
     */
    public function up(Schema $schema): void
    {
        $table = $schema->createTable($this->getTableName());
        $tableSchema = static::getTableSchema();

        $table
            ->addColumn($tableSchema->incrementalIdColumn(), 'bigint')
            ->setUnsigned(true)
            ->setNotnull(true)
            ->setAutoincrement(true);

        $table
            ->addColumn($tableSchema->eventIdColumn(), 'binary')
            ->setLength(16)
            ->setNotnull(true);

        $table
            ->addColumn($tableSchema->aggregateRootIdColumn(), 'binary')
            ->setLength(16)
            ->setNotnull(true);

        $table
            ->addColumn($tableSchema->versionColumn(), 'integer')
            ->setLength(20)
            ->setUnsigned(true);

        $table
            ->addColumn($tableSchema->payloadColumn(), 'string')
            ->setLength(16001)
            ->setNotnull(true);

        $table->setPrimaryKey([$tableSchema->incrementalIdColumn()]);

        $table->addIndex(
            [
                $tableSchema->aggregateRootIdColumn(),
                $tableSchema->versionColumn()
            ],
            'reconstitution',
        );
    }

    public function down(Schema $schema): void
    {
        if ($schema->hasTable($this->getTableName())) {
            $schema->dropTable($this->getTableName());
        }
    }
}