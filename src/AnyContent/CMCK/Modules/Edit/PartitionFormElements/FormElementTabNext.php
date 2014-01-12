<?php

namespace Anycontent\CMCK\Modules\Edit\PartitionFormElements;

class FormElementTabNext extends \AnyContent\CMCK\Modules\Core\Edit\FormElementDefault
{

    protected function fetchTabContent()
    {
        $nr = $this->form->getFormVar('tab.nr', 1);
        $this->form->setFormVar('tab.nr', $nr + 1);

        $tabs       = $this->form->getFormVar('tabs', array());
        $tabcontent = $this->form->endBuffer();

        $tabs[] = array( 'title' => $nr, 'content' => $tabcontent, 'nr' => $nr );
        $this->form->setFormVar('tabs', $tabs);

        return $tabcontent;
    }


    public function render($layout)
    {
        $this->fetchTabContent();

        $this->form->startBuffer();

        return '';
    }
}