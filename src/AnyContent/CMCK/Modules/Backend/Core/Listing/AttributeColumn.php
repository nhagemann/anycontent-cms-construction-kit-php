<?php

namespace AnyContent\CMCK\Modules\Backend\Core\Listing;

use AnyContent\Client\Record;
use CMDL\FormElementDefinition;

class AttributeColumn extends PropertyColumn
{
    protected $type = 'Attribute';

    protected $attribute;


    /**
     * @return mixed
     */
    public function getAttribute()
    {
        return $this->attribute;
    }


    /**
     * @param mixed $attribute
     */
    public function setAttribute($attribute)
    {
        $this->attribute = trim($attribute,'.');
    }







}