<?php

namespace AnyContent\CMCK\Modules\Backend\Edit\DateTimeFormElements;

use AnyContent\CMCK\Modules\Backend\Core\Edit\FormElementDefault;
use CMDL\FormElementDefinition;

class FormElementTime extends \AnyContent\CMCK\Modules\Backend\Core\Edit\FormElementDefault
{

    public function render($layout)
    {
        $layout->addJsFile('fe-datetime.js');

        $this->vars['hour']   = '';
        $this->vars['minute'] = '';
        $this->vars['second'] = '';
        $this->vars['value']  = '';

        $tokens = explode(':', $this->getValue());

        if (count($tokens) >= 2)
        {
            $this->vars['hour']   = $tokens[0];
            $this->vars['minute'] = $tokens[1];
            if (count($tokens) == 3 AND $this->definition->getType() == 'long')
            {
                $this->vars['second'] = $tokens[2];
            }
        }

        $this->vars['type'] = $this->definition->getType();

        return $this->twig->render('formelement-time.twig', $this->vars);
    }


    public function parseFormInput($input)
    {

        $value = '';

        if (is_array($input))
        {
            $value = join(':', $input);
        }

        return $value;
    }

}