<?php

namespace AnyContent\CMCK\Modules\Backend\Edit\SequenceFormElement;

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

        // the sequence rendering form must know, if the sequence form element has be inserted via a insert to find it's definition
        $insertName = '-';
        if ($this->definition->isInsertedByInsert())
        {
            $insertName = $this->definition->getInsertedByInsertName();
        }

        $url = $this->app['url_generator']->generate('editSequence', array( 'contentTypeAccessHash' => $contentTypeAccessHash, 'viewName' => 'default', 'insertName'=> $insertName, 'recordId' => $recordId, 'property' => $this->definition->getName() ));

        $this->vars['src'] = $url;

        $this->app['layout']->addCssFile('formelement-sequence.css');
        $this->app['layout']->addJsFile('formelement-sequence.js');

        return $this->twig->render('formelement-sequence.twig', $this->vars);

    }
}