<?php

namespace Anycontent\CMCK\Modules\Backend\Edit\PartitionFormElements;

class FormElementSectionEnd extends \AnyContent\CMCK\Modules\Backend\Core\Edit\FormElementDefault
{

    public function render($layout)
    {
        return $this->twig->render('formelement-section-end.twig', $this->vars);
    }
}