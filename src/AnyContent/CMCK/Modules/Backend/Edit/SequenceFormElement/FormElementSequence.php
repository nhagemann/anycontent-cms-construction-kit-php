<?php

namespace Anycontent\CMCK\Modules\Backend\Edit\SequenceFormElement;

class FormElementSequence extends \AnyContent\CMCK\Modules\Backend\Core\Edit\FormElementDefault
{

    public function __construct($id, $name, $formElementDefinition, $app, $value = '')
    {
        parent::__construct($id, $name, $formElementDefinition, $app, $value);

    }


    public function render($layout)
    {

        $contentTypeAccessHash = $this->app['repos']->getAccessHash($this->app['context']->getCurrentRepository(), $this->app['context']->getCurrentContentType());
        $record                = $this->app['context']->getCurrentRecord();
        $recordId              = 0;
        if ($record)
        {
            $recordId = $record->getId();
        }
        $url               = $this->app['url_generator']->generate('editSequence', array( 'contentTypeAccessHash' => $contentTypeAccessHash, 'recordId' => $recordId, 'property' => $this->definition->getName() ));
        $this->vars['src'] = $url;

        $this->app['layout']->addCssFile('formelement-sequence.css');
        $this->app['layout']->addJsFile('formelement-sequence.js');

        return $this->twig->render('formelement-sequence.twig', $this->vars);

    }
}