<?php

namespace AnyContent\CMCK\Modules\Backend\Edit\ColorFormElement;

class FormElementColor extends \AnyContent\CMCK\Modules\Backend\Core\Edit\FormElementDefault
{

    public function render($layout)
    {

        $layout->addJsFile('jquery.minicolors.min.js');
        $layout->addJsFile('feco.js');
        $layout->addCssLinkToHead('/css/jquery-minicolors/jquery.minicolors.css');

        return $this->twig->render('formelement-color.twig', $this->vars);
    }

    public function parseFormInput($input)
    {
        $value = '';

        if (is_array($input))
        {
            $value = array_shift($input);
        }

        return $value;
    }
}