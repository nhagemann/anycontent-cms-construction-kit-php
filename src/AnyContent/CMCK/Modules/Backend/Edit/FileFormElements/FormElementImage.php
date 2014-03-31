<?php

namespace AnyContent\CMCK\Modules\Backend\Edit\FileFormElements;

class FormElementImage extends \AnyContent\CMCK\Modules\Backend\Edit\FileFormElements\FormElementFile
{

    public function render($layout)
    {
        $layout->addJsFile('fe-file.js');

        $this->vars['path']  = $this->definition->getPath();
        $this->vars['types'] = $this->definition->getFileTypes();
        $this->vars['url_modal'] = $this->app['url_generator']->generate('formElementFileModal', array( 'name' => 'test'));

        return $this->twig->render('formelement-image.twig', $this->vars);
    }

}