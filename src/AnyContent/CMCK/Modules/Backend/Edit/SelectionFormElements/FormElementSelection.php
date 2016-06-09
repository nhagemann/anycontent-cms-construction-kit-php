<?php

namespace AnyContent\CMCK\Modules\Backend\Edit\SelectionFormElements;

use CMDL\FormElementDefinitions\SelectionFormElementDefinition;

class FormElementSelection extends \AnyContent\CMCK\Modules\Backend\Core\Edit\FormElementDefault
{
    /** @var  SelectionFormElementDefinition */
    protected $definition;
    
    public function render($layout)
    {
        $options = $this->definition->getOptions();
        
        $this->vars['type'] = $this->definition->getType();
        $this->vars['options'] = $options;

        if (count($options)>20)
        {
            $this->vars['label']='';
            $this->vars['json'] = $this->buildAutoCompleteLabelValueArray($options);
            if (array_key_exists($this->value,$options))
            {
                $this->vars['label'] = $options[$this->value];
            }
        }


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