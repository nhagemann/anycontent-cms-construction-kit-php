<?php

namespace AnyContent\CMCK\Modules\Backend\Edit\FileFormElements;

class FormElementImage extends \AnyContent\CMCK\Modules\Backend\Edit\FileFormElements\FormElementFile
{

    public function render($layout)
    {
        $this->vars['path']  = $this->definition->getPath();
        $this->vars['types'] = $this->definition->getFileTypes();
        $this->vars['url_modal'] = 'http://www.ard.de';

        return $this->twig->render('formelement-image.twig', $this->vars);
    }

}