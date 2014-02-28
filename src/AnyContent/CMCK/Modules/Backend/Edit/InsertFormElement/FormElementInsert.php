<?php

namespace AnyContent\CMCK\Modules\Backend\Edit\InsertFormElement;

use CMDL\DataTypeDefinition;

class FormElementInsert extends \AnyContent\CMCK\Modules\Backend\Core\Edit\FormElementDefault
{

    public function __construct($id, $name, $formElementDefinition, $app, $value = '')
    {
        parent::__construct($id, $name, $formElementDefinition, $app, $value);

    }


    public function render($layout)
    {
        return '';
    }


    /**
     * @param DataTypeDefinition      $dataTypeDefinition
     * @param array                   $values
     *
     * @return mixed
     */
    public function getInsertionDefinition($dataTypeDefinition, $values = array())
    {

        if ($this->definition->getPropertyName())
        {
            $value = null;
            if (array_key_exists($this->definition->getPropertyName(), $values))
            {
                $value = $values[$this->definition->getPropertyName()];
            }
            $insertionName = $this->definition->getInsertionName($value);
        }
        else
        {
            $insertionName = $this->definition->getInsertionName();
        }

        if ($dataTypeDefinition->hasInsertionDefinition($insertionName))
        {
            $insertionDefinition = $dataTypeDefinition->getInsertionDefinition($insertionName);

            return $insertionDefinition;
        }
        else
        {
            return false;
        }
    }
}