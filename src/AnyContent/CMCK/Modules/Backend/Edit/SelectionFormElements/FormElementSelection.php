<?php

namespace Anycontent\CMCK\Modules\Backend\Edit\SelectionFormElements;

class FormElementSelection extends \AnyContent\CMCK\Modules\Backend\Core\Edit\FormElementDefault
{

    public function render($layout)
    {
        $this->vars['type']    = $this->definition->getType();
        $this->vars['options'] = $this->definition->getOptions();

        return $this->twig->render('formelement-selection.twig', $this->vars);
    }

}