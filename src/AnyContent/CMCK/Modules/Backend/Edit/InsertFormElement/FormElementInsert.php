<?php

namespace Anycontent\CMCK\Modules\Backend\Edit\InsertFormElement;

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

        $insertionDefinition = $dataTypeDefinition->getInsertionDefinition($insertionName);

        return $insertionDefinition;
    }
}