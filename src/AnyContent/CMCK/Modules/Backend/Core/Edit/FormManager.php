<?php

namespace AnyContent\CMCK\Modules\Backend\Core\Edit;

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

    protected $dataTypeDefinition = null;


    public function __construct($app)
    {
        $this->app    = $app;
        $this->twig   = $app['twig'];
        $this->layout = $app['layout'];
    }


    public function registerFormElement($type, $class, $options = array())
    {

        $this->formElements[$type] = array( 'class' => $class, 'options' => $options );
    }


    public function renderFormElements($formId, $formElementsDefinition, $values = array(), $prefix = '')
    {
        $this->clearFormVars();

        // first check for insertions and add form elements of those
        $formElementsDefinition       = $this->integrationEventuallyInsertionFormElementsIntoFormElementsDefinition($formElementsDefinition,$values);
        $this->formElementsDefinition = $formElementsDefinition;

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

            $class = $this->formElements[$type]['class'];

            $formelement = new $class($id, $name, $formElementDefinition, $this->app, $value, $this->formElements[$type]['options']);

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
        $this->formElementsDefinition = null;

        return $html;
    }


    public function extractFormElementValuesFromPostRequest($request, $formElementsDefinition,$values=array())
    {
        // first check for insertions and add form elements of those
        $formElementsDefinition       = $this->integrationEventuallyInsertionFormElementsIntoFormElementsDefinition($formElementsDefinition,$values);
        $this->formElementsDefinition = $formElementsDefinition;

        $values = array();
        /** @var FormElementDefinition $formElementDefinition */
        foreach ($formElementsDefinition as $formElementDefinition)
        {
            $name = $formElementDefinition->getName();
            $type = $formElementDefinition->getFormElementType();

            if (!array_key_exists($type, $this->formElements))
            {
                $type = 'default';

            }
            $class = $this->formElements[$type]['class'];

            $formelement = new $class(null, $name, $formElementDefinition, $this->app, null, $this->formElements[$type]['options']);

            $property = $formElementDefinition->getName();
            if ($property)
            {
                $values[$property] = $formelement->parseFormInput($request->get($property));
            }
        }

        $this->formElementsDefinition = null;
        return $values;
    }


    public function integrationEventuallyInsertionFormElementsIntoFormElementsDefinition($formElementsDefinition, $values)
    {
        $integratedFormElementsDefinition = array();
        foreach ($formElementsDefinition as $formElementDefinition)
        {
            if ($formElementDefinition->getFormElementType() == 'insert' AND array_key_exists('insert', $this->formElements))
            {


                $class       = $this->formElements['insert']['class'];
                $formElement = new $class(null, null, $formElementDefinition, $this->app, null, $this->formElements['insert']['options']);



                $insertionDefinition = $formElement->getInsertionDefinition($this->getDataTypeDefinition(), $values);

                if ($insertionDefinition)
                {
                    foreach ($insertionDefinition->getFormElementDefinitions() as $insertionFormElementDefinition)
                    {

                        $insertionFormElementDefinition->setInsertedByInsert($insertionDefinition->getName());
                        $integratedFormElementsDefinition[] = $insertionFormElementDefinition;

                    }
                }

            }
            else
            {
                $integratedFormElementsDefinition[] = $formElementDefinition;
            }
        }

        return $integratedFormElementsDefinition;
    }


    public function setDataTypeDefinition($dataTypeDefinition)
    {
        $this->dataTypeDefinition = $dataTypeDefinition;
    }


    public function getDataTypeDefinition()
    {
        return $this->dataTypeDefinition;
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