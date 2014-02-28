<?php

namespace AnyContent\CMCK\Modules\Backend\Edit\RichtextTinyMCEFormElements;

use AnyContent\CMCK\Modules\Backend\Core\Edit\FormElementDefault;
use CMDL\FormElementDefinition;

class FormElementRichtext extends \AnyContent\CMCK\Modules\Backend\Edit\TextFormElements\FormElementTextarea
{

    public function render($layout)
    {
        $layout->addJsLinkToHead('//tinymce.cachefly.net/4/tinymce.min.js');
        //$layout->addJsLinkToHead('/js/tinymce/tinymce.min.js');

        $layout->addJsFile('fe-richtext.js');

        return $this->twig->render('formelement-richtext.twig', $this->vars);
    }
}