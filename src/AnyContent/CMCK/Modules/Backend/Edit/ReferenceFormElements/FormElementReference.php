<?php
namespace Anycontent\CMCK\Modules\Backend\Edit\ReferenceFormElements;

use Anycontent\Client\Repository;

class FormElementReference extends \AnyContent\CMCK\Modules\Backend\Edit\SelectionFormElements\FormElementSelection
{

    public function __construct($id, $name, $formElementDefinition, $app, $value = '')
    {
        parent::__construct($id, $name, $formElementDefinition, $app, $value);

        $this->vars['type'] = $this->definition->getType();

        /** @var Repository $repository */
        $repository = $app['context']->getCurrentRepository();

        $repository->selectContentType($this->definition->getContentType());
        $contentTypeDefinition = $repository->getContentTypeDefinition();

        $workspace    = $this->definition->getWorkspace();
        $clippingName = $contentTypeDefinition->getListClippingDefinition()->getName();
        $language     = $this->definition->getLanguage();
        $order        = $this->definition->getOrder();
        $timeshift    = $this->definition->getTimeShift();

        $records = $repository->getRecords($workspace, $clippingName, $language, $order, $timeshift);

        $options = array();
        foreach ($records as $record)
        {
            $options[$record->getId()]=$record->getName();
        }

        $this->vars['options'] = $options;
    }


    public function render($layout)
    {

        return $this->twig->render('formelement-selection.twig', $this->vars);
    }

}