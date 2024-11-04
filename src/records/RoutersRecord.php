<?php

namespace useralberto\craftnextjsurls\records;

use craft\db\ActiveRecord;

class RoutersRecord extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%nextjsurls_routers}}';
    }
}
