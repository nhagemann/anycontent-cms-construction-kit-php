<?php

namespace Anycontent\CMCK\Modules\Edit\PartitionFormElements;

class FormElementSectionStart extends \AnyContent\CMCK\Modules\Core\Edit\FormElementDefault
{

    public function render($layout)
    {
        $nr = $this->form->getFormVar('section.nr', 1);

        $this->form->setFormVar('section.nr', $nr + 1);

        $this->vars['index'] = $nr;

        $this->vars['opened']= $this->definition->getOpened();

        return $this->twig->render('formelement-section-start.twig', $this->vars);
    }
}