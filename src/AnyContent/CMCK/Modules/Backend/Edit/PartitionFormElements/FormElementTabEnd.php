<?php

namespace Anycontent\CMCK\Modules\Backend\Edit\PartitionFormElements;

class FormElementTabEnd extends \AnyContent\CMCK\Modules\Backend\Edit\PartitionFormElements\FormElementTabNext
{

    public function render($layout)
    {
        $this->fetchTabContent();
        $tabs = $this->form->getFormVar('tabs', array());

        $this->vars['tabs'] = $tabs;

        return $this->twig->render('formelement-tab.twig', $this->vars);
    }
}