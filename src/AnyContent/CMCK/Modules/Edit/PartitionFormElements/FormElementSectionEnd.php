<?php

namespace Anycontent\CMCK\Modules\Edit\PartitionFormElements;

class FormElementSectionEnd extends \AnyContent\CMCK\Modules\Core\Edit\FormElementDefault
{

    public function render($layout)
    {
        return $this->twig->render('formelement-section-end.twig', $this->vars);
    }
}