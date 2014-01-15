<?php

namespace Anycontent\CMCK\Modules\Backend\Core\Edit;

use CMDL\FormElementDefinition;

class FormManager
{

    protected $twig;
    protected $layout;
    protected $url_generator;

    protected $formElements = array();

    protected $formVars = array();

    protected $buffering = false;

    protected $buffer = '';


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


    public function renderFormElements($formId, $formElementsDefinition, $values = array(), $prefix = '')
    {
        $this->clearFormVars();
        $html = '';
        $i    = 0;
        /** @var FormElementDefinition $formElementDefinition */
        foreach ($formElementsDefinition as $formElementDefinition)
        {
            $i++;
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
                $name = trim($prefix, '_') . '_' . $name;
            }
            $id = $formId . '_' . $formElementDefinition->getFormElementType() . '_' . $name;

            $formelement = new $this->formElements[$type]($id, $name, $formElementDefinition, $this->app, $value);
            if ($i == 1)
            {
                $formelement->setIsFirstElement(true);
            }

            $htmlFormElement = $formelement->render($this->layout);
            if ($this->buffering)
            {
                $this->buffer .= $htmlFormElement;
            }
            else
            {
                $html .= $htmlFormElement;
            }
        }

        return $html;
    }


    public function extractFormElementValuesFromPostRequest($request, $formElementsDefinition)
    {
        $values = array();
        /** @var FormElementDefinition $formElementDefinition */
        foreach ($formElementsDefinition as $formElementDefinition)
        {
            $property = $formElementDefinition->getName();
            if ($property)
            {
                $values[$property] = $request->get($property);
            }
        }

        return $values;
    }


    protected function clearFormVars()
    {
        $this->formVars = array();
    }


    public function setFormVar($key, $value)
    {
        $this->formVars[$key] = $value;
    }


    public function getFormVar($key, $default = null)
    {
        if (array_key_exists($key, $this->formVars))
        {
            return $this->formVars[$key];
        }

        return $default;
    }


    public function startBuffer()
    {
        $this->buffering = true;
        $this->buffer    = '';
    }


    public function endBuffer()
    {
        $this->buffering = false;
        $buffer          = $this->buffer;
        $this->buffer    = '';

        return $buffer;
    }

}