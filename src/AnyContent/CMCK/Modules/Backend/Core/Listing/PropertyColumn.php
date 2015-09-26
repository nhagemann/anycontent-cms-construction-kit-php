<?php

namespace AnyContent\CMCK\Modules\Backend\Core\Listing;

use AnyContent\Client\Record;
use CMDL\FormElementDefinition;

class PropertyColumn extends BaseColumn
{

    protected $type = 'Property';

    protected $property;

    /**
     * @var FormElementDefinition
     */
    protected $formElementDefinition;


    /**
     * @return mixed
     */
    public function getProperty()
    {
        return $this->property;
    }


    /**
     * @param mixed $property
     */
    public function setProperty($property)
    {
        $this->property = $property;
    }


    /**
     * @param FormElementDefinition $formElementDefinition
     */
    public function setFormElementDefinition($formElementDefinition)
    {
        $this->formElementDefinition = $formElementDefinition;
    }


    /**
     * @return FormElementDefinition
     */
    public function getFormElementDefinition()
    {
        return $this->formElementDefinition;
    }


    public function getClass()
    {
        return 'col-listing-property-' . $this->getProperty();
    }

}