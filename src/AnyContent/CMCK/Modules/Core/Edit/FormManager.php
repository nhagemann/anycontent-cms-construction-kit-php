<?php

namespace Anycontent\CMCK\Modules\Core\Edit;

use CMDL\FormElementDefinition;

class FormManager
{

    protected $twig;
    protected $layout;
    protected $url_generator;

    protected $formElements = array();


    public function __construct($app)
    {
        $this->app    = $app;
        $this->twig   = $app['twig'];
        $this->layout = $app['layout'];
    }


    public function registerFormElement($type, $class)
    {
        $this->formElements[$type] = $class;
    }


    public function renderFormElements($formId, $formElementsDefinition, $values = array(),$prefix='')
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

            if (array_key_exists($formElementDefinition->getName(), $values))
            {
                $value = $values[$formElementDefinition->getName()];
            }

            $name = $formElementDefinition->getName();

            if ($prefix)
            {
                $name = trim($prefix,'_').'_'.$name;
            }
            $id   = $formId . '_' . $formElementDefinition->getFormElementType() . '_' . $name;

            $formelement = new $this->formElements[$type]($id, $name, $formElementDefinition, $this->app, $value);
            $html .= $formelement->render($this->layout);
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