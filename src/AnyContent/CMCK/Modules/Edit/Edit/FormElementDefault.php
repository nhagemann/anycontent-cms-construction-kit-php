<?php

namespace Anycontent\CMCK\Modules\Edit\Edit;

use CMDL\FormElementDefinition;

class FormElementDefault
{

    protected $id = '';

    protected $name = '';

    protected $definition;
    protected $value = '';

    protected $twig;

    protected $vars = array();


    public function __construct($id, $name, $formElementDefinition, $twig, $value = '')
    {
        $this->id         = $id;
        $this->name       = $name;
        $this->definition = $formElementDefinition;
        $this->twig       = $twig;
        $this->value      = $value;

        $this->vars['id']         = $this->id;
        $this->vars['name']       = $this->name;
        $this->vars['definition'] = $this->definition;
        $this->vars['value']      = $this->value;
    }


    public function render()
    {
        return $this->twig->render('formelement-default.twig', $this->vars);
    }
}