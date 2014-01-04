<?php

namespace Anycontent\CMCK\Modules\Edit\RichtextTinyMCEFormElements;

use Anycontent\CMCK\Modules\Core\Edit\FormElementDefault;
use CMDL\FormElementDefinition;

class FormElementRichtext extends \AnyContent\CMCK\Modules\Edit\TextFormElements\FormElementTextarea
{

    public function render($layout)
    {
        $layout->addJsLinkToHead('//tinymce.cachefly.net/4/tinymce.min.js');

        $layout->addJsFile('fe-richtext.js');

        return $this->twig->render('formelement-richtext.twig', $this->vars);
    }
}