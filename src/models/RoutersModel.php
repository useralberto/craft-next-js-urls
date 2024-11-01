<?php

namespace useralberto\craftnextjsurls\models;

use craft\base\Model;

class RoutersModel extends Model
{
    public $elementId;
    public $siteId;
    public $oldUri;
    public $newUri;

    public function rules(): array
    {
        return [
            [['elementId', 'siteId', 'oldUri', 'newUri',], 'required'],
        ];
    }
}
