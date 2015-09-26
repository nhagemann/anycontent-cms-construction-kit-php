<?php

namespace AnyContent\CMCK\Modules\Backend\Core\Listing;

use AnyContent\Client\Record;
use CMDL\FormElementDefinition;

class SubtypeColumn extends PropertyColumn
{

    protected $type = 'Subtype';

    protected $badge = true;


    public function getValue(Record $record)
    {
        $key = $record->getProperty('subtype');

        $list = $this->getFormElementDefinition()->getList(1);

        if (array_key_exists($key, $list))
        {
            return $list[$key];
        }

        return $key;
    }

}