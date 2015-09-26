<?php

namespace AnyContent\CMCK\Modules\Backend\Core\Listing;

use AnyContent\Client\Record;
use CMDL\FormElementDefinition;

class StatusColumn extends PropertyColumn
{

    protected $type = 'Status';

    protected $badge = true;

    public function getValue(Record $record)
    {
        $key = $record->getProperty('status');

        $list = $this->getFormElementDefinition()->getList(1);

        if (array_key_exists($key,$list))
        {
            return $list[$key];
        }

        return $key;
    }


    public function getClass()
    {
        return 'col-listing-status';
    }

}