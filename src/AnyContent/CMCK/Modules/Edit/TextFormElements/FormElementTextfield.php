<?php

namespace Anycontent\CMCK\Modules\Edit\TextFormElements;

class FormElementTextfield extends \AnyContent\CMCK\Modules\Core\Edit\FormElementDefault
{

    public function __construct($id, $name, $formElementDefinition, $twig, $value = '')
    {
        parent::__construct($id, $name, $formElementDefinition, $twig, $value );


        $sizes = array( 'S'=>'col-xs-2', 'M'=>'col-xs-5', 'L'=>'col-xs-8', 'XL'=>'col-xs-10', 'XXL'=>'col-xs-12' );

        $this->vars['class']['size']         = $sizes[$this->definition->getSize()];

    }



    public function render($layout)
    {

        return $this->twig->render('formelement-textfield.twig', $this->vars);
    }
}