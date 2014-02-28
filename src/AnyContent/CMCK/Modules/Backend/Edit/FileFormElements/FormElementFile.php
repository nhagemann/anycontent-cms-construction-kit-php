<?php

namespace AnyContent\CMCK\Modules\Backend\Edit\FileFormElements;

class FormElementFile extends \AnyContent\CMCK\Modules\Backend\Core\Edit\FormElementDefault
{

    public function render($layout)
    {
        $this->vars['path']  = $this->definition->getPath();
        $this->vars['types'] = $this->definition->getFileTypes();


        return $this->twig->render('formelement-file.twig', $this->vars);
    }

}