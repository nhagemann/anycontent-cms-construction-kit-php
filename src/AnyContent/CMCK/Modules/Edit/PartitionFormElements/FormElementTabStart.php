<?php

namespace Anycontent\CMCK\Modules\Edit\PartitionFormElements;

class FormElementTabStart extends \AnyContent\CMCK\Modules\Core\Edit\FormElementDefault
{

    public function render($layout)
    {
        $this->form->setFormVar('tab.label', $this->definition->getLabel());

        $this->form->startBuffer();

        return '';
    }
}