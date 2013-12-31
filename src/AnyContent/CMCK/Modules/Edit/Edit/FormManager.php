<?php

namespace Anycontent\CMCK\Modules\Edit\Edit;

use CMDL\FormElementDefinition;

class FormManager
{

    protected $twig;

    protected $formElements = array();


    public function __construct($twig)
    {
        $this->twig = $twig;
    }


    public function registerFormElement($type, $class)
    {
        $this->formElements[$type] = $class;
    }


    public function renderFormElements($formId, $formElementsDefinition, $record = null)
    {
        $html = '';
        /** @var FormElementDefinition $formElementDefinition */
        foreach ($formElementsDefinition as $formElementDefinition)
        {
            $value = '';
            $type  = $formElementDefinition->getFormElementType();

            if (!array_key_exists($type, $this->formElements))
            {
                $type = 'default';

            }

            if ($record)
            {
                $property = $formElementDefinition->getName();
                $value    = $record->getProperty($property);
                $formElementDefinition->getPlaceholder();
            }

            $name = $formElementDefinition->getName();
            $id   = $formId . '_' . $formElementDefinition->getFormElementType() . '_' . $name;

            $formelement = new $this->formElements[$type]($id, $name, $formElementDefinition, $this->twig, $value);
            $html .= $formelement->render();
        }

        return $html;
    }


    public function extractFormElementValuesFromPostRequest($request, $formElementsDefinition)
    {
        $values = array();
        /** @var FormElementDefinition $formElementDefinition */
        foreach ($formElementsDefinition as $formElementDefinition)
        {
            $property          = $formElementDefinition->getName();
            $values[$property] = $request->get($property);
        }

        return $values;
    }
}