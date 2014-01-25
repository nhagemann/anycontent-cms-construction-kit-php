<?php

namespace Anycontent\CMCK\Modules\Backend\Edit\SourceCodeFormElements;

class FormElementSourceCode extends \AnyContent\CMCK\Modules\Backend\Edit\TextFormElements\FormElementTextarea
{

    public function render($layout)
    {

        $layout->addJsLinkToHead('//cdnjs.cloudflare.com/ajax/libs/codemirror/3.20.0/codemirror.min.js');
        $layout->addCSSLinkToHead('//cdnjs.cloudflare.com/ajax/libs/codemirror/3.20.0/codemirror.css');


        $layout->addJsLinkToHead('//cdnjs.cloudflare.com/ajax/libs/codemirror/3.20.0/mode/xml/xml.min.js');
        $layout->addJsLinkToHead('//cdnjs.cloudflare.com/ajax/libs/codemirror/3.20.0/mode/javascript/javascript.min.js');
        $layout->addJsLinkToHead('//cdnjs.cloudflare.com/ajax/libs/codemirror/3.20.0/mode/css/css.min.js');
        $layout->addJsLinkToHead('//cdnjs.cloudflare.com/ajax/libs/codemirror/3.20.0/mode/vbscript/vbscript.min.js');
        $layout->addJsLinkToHead('//cdnjs.cloudflare.com/ajax/libs/codemirror/3.20.0/mode/htmlmixed/htmlmixed.min.js');


        $layout->addJsFile('fe-sourcecode.js');

        return $this->twig->render('formelement-sourcecode.twig', $this->vars);

    }
}