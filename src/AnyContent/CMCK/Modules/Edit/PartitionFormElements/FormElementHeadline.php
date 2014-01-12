<?php

namespace Anycontent\CMCK\Modules\Edit\PartitionFormElements;

class FormElementHeadline extends \AnyContent\CMCK\Modules\Core\Edit\FormElementDefault
{

    public function render($layout)
    {
        $this->vars['first'] = false;
        if ($this->isFirstElement())
        {
            $this->vars['first'] = true;
        }

        return $this->twig->render('formelement-headline.twig', $this->vars);
    }
}