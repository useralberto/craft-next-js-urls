<?php

namespace useralberto\craftnextjsurls\migrations;

use Craft;
use craft\db\Migration;

use useralberto\craftnextjsurls\records\RoutersRecord;

class Install extends Migration
{
    private $tableRouters;

    public function __construct(array $config = [])
    {
        $this->tableRouters = RoutersRecord::tableName();
        parent::__construct($config);
    }

    public function safeUp()
    {
        $this->createTables();
        $this->createIndexs();
        return true;
    }
    public function safeDown()
    {
        $this->dropTableIfExists($this->tableRouters);
        return true;
    }

    private function createTables()
    {
        $this->createTable($this->tableRouters, [
            'id' => $this->primaryKey(),
            'elementId' => $this->integer()->notNull(),
            'siteId' => $this->integer()->notNull(),
            'oldUri' => $this->string()->null(),
            'newUri' => $this->string()->notNull(),
            'dateUpdated' => $this->dateTime()->notNull(),
            'dateCreated' => $this->dateTime()->notNull(),
        ]);
    }
    private function createIndexs()
    {
        $this->createIndex(null, $this->tableRouters, ['elementId', 'siteId'], true);
    }
}
