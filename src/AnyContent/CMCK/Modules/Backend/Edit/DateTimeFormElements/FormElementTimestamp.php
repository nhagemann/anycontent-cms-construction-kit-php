<?php

namespace AnyContent\CMCK\Modules\Backend\Edit\DateTimeFormElements;

use AnyContent\CMCK\Modules\Backend\Core\Edit\FormElementDefault;
use CMDL\FormElementDefinition;

class FormElementTimestamp extends \AnyContent\CMCK\Modules\Backend\Core\Edit\FormElementDefault
{

    public function render($layout)
    {
        $layout->addJsFile('fe-datetime.js');

        $value = $this->getValue();

        if (is_numeric($value))
        {
            $this->vars['month']  = date('m', $value);
            $this->vars['day']    = date('d', $value);
            $this->vars['hour']   = date('H', $value);
            $this->vars['minute'] = date('i', $value);
            $this->vars['second'] = 0;
            if ($this->definition->getType() == 'full')
            {
                $this->vars['second'] = date('s', $value);
            }

            $this->vars['value'] = date('Y-m-d', $value);
        }
        else
        {
            $this->vars['month']  = '';
            $this->vars['day']    = '';
            $this->vars['hour']   = '';
            $this->vars['minute'] = '';
            $this->vars['second'] = '';
            $this->vars['value']  = '';

            //echo 'init';
        }

        $this->vars['type'] = $this->definition->getType();

        return $this->twig->render('formelement-datetime.twig', $this->vars);
    }


    public function parseFormInput($input)
    {

        $value = '';

        if (is_array($input))
        {
            $tokens = explode('-', $input[0]);
            if (count($tokens) == 3)
            {
                $year    = $tokens[0];
                $month   = $tokens[1];
                $day     = $tokens[2];
                $hour    = $input[1];
                $minute  = $input[2];
                $seconds = 0;

                if ($this->definition->getType() == 'full')
                {
                    $seconds = $input[3];
                }

                $value = mktime($hour, $minute, $seconds, $month, $day, $year);
            }
        }

        return $value;
    }

}