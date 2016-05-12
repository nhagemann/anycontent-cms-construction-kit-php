<?php

namespace AnyContent\CMCK\Modules\Backend\Edit\SelectionFormElements;

class FormElementSelection extends \AnyContent\CMCK\Modules\Backend\Core\Edit\FormElementDefault
{

    public function render($layout)
    {
        $this->vars['type'] = $this->definition->getType();
        $this->vars['options'] = $this->definition->getOptions();
        $this->vars['json'] = $this->buildAutoCompleteLabelValueArray($this->definition->getOptions());

        return $this->twig->render('formelement-selection.twig', $this->vars);
    }

    protected function buildAutoCompleteLabelValueArray($options)
    {
        $array = [];
        foreach ($options as $k => $v) {
            $array[] = ['label' => $v, 'value' => $k];
        }
        return $array;
    }
}