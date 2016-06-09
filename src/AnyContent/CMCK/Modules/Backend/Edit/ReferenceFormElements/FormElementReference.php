<?php
namespace AnyContent\CMCK\Modules\Backend\Edit\ReferenceFormElements;

use AnyContent\Client\DataDimensions;
use AnyContent\Client\Repository;
use AnyContent\CMCK\Modules\Backend\Core\Application\Application;
use AnyContent\CMCK\Modules\Backend\Core\Repositories\RepositoryManager;
use CMDL\FormElementDefinitions\ReferenceFormElementDefinition;

class FormElementReference extends \AnyContent\CMCK\Modules\Backend\Edit\SelectionFormElements\FormElementSelection
{
    /** @var  ReferenceFormElementDefinition */

    protected $definition;

    public function __construct($id, $name, $formElementDefinition, Application $app, $value = '')
    {
        parent::__construct($id, $name, $formElementDefinition, $app, $value);

        $this->vars['type'] = $this->definition->getType();

        /** @var Repository $repository */
        $repository = $app['context']->getCurrentRepository();


        if ($this->definition->hasRepositoryName())
        {
            /** @var RepositoryManager $repositoryManager */
            $repositoryManager = $app['repos'];

            $repository = $repositoryManager->getRepositoryById($this->definition->getRepositoryName());

            if (!$repository)
            {
                $app['context']->addAlertMessage('Could not find repository named '.$this->definition->getRepositoryName());
            }
        }

        $options = array();

        if ($repository)
        {
            if ($repository->selectContentType($this->definition->getContentType()))
            {
                $contentTypeDefinition = $repository->getContentTypeDefinition();

                $currentDataDimensions = $repository->getCurrentDataDimensions();

                $workspace = $this->definition->getWorkspace();
                $language  = $this->definition->getLanguage();

                $referenceDataDimensions = new DataDimensions();
                $referenceDataDimensions->setWorkspace($workspace);
                $referenceDataDimensions->setLanguage($language);
                $referenceDataDimensions->setViewName($contentTypeDefinition->getListViewDefinition()->getName());
                $referenceDataDimensions->setTimeShift($this->definition->getTimeShift());

                $records = [ ];
                foreach ($repository->getRecords('', $this->definition->getOrder(), 1, null) as $record)
                {
                    $records[$record->getId()] = $record->getName();
                }

                /** @var RepositoryManager $repositoryManager */
                $repositoryManager = $app['repos'];

                $accessHash = $repositoryManager->getAccessHash($repository, $contentTypeDefinition);

                $editUrl = '#';
                if ($this->value != '')
                {
                    $editUrl = $app->getUrlGenerator()
                                   ->generate('editRecord', array( 'contentTypeAccessHash' => $accessHash, 'recordId' => $value, 'workspace' => $workspace, 'language' => $language ));
                }

                $this->vars['editUrl'] = $editUrl;

                $editUrlPattern = $app->getUrlGenerator()
                                      ->generate('editRecord', array( 'contentTypeAccessHash' => $accessHash, 'recordId' => 'recordId', 'workspace' => $workspace, 'language' => $language ));

                $this->vars['editUrlPattern'] = $editUrlPattern;

                $repository->setDataDimensions($currentDataDimensions);

                foreach ($records as $id => $name)
                {
                    $options[$id] = '#' . $id . ': ' . $name;
                }
            }
            else
            {
                $app['context']->addAlertMessage('Could not find referenced content type ' . $this->definition->getContentType() . '.');
            }

        }

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
    }


    public function render($layout)
    {
        return $this->twig->render('formelement-reference.twig', $this->vars);
    }

}