<?php

namespace AnyContent\CMCK\Modules\Backend\Edit\LinkFormElement;

class FormElementLink extends \AnyContent\CMCK\Modules\Backend\Core\Edit\FormElementDefault
{

    public function render($layout)
    {
        $layout->addJsFile('fe-link.js');
        return $this->twig->render('formelement-link.twig', $this->vars);
    }
}