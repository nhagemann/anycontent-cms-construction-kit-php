<?php

namespace Anycontent\CMCK\Modules\Edit\TextFormElements;

class FormElementTextarea extends \AnyContent\CMCK\Modules\Edit\TextFormElements\FormElementTextfield
{

    public function render($layout)
    {

        return $this->twig->render('formelement-textarea.twig', $this->vars);
    }
}