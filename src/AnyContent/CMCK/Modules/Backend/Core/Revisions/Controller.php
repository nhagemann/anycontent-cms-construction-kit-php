<?php

namespace AnyContent\CMCK\Modules\Backend\Core\Revisions;

use AnyContent\Client\ContentFilter;
use AnyContent\CMCK\Modules\Backend\Core\Application\Application;

use AnyContent\CMCK\Modules\Backend\Core\User\UserManager;
use CMDL\ContentTypeDefinition;
use CMDL\ViewDefinition;

use AnyContent\Client\Repository;
use AnyContent\Client\Record;

use AnyContent\CMCK\Modules\Backend\Core\Edit\FormManager;

use CMDL\FormElementDefinition;

use cogpowered\FineDiff\Diff;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class Controller
{

    public static function listRecordRevisions(Application $app, $contentTypeAccessHash, $recordId, $workspace, $language)
    {
        /** @var UserManager $user */
        $user = $app['user'];

        $differ = new Diff();

        $vars = array();

        $vars['menu_mainmenu']   = $app['menus']->renderMainMenu();
        $vars['links']['search'] = $app['url_generator']->generate(
            'listRecords',
            array('contentTypeAccessHash' => $contentTypeAccessHash, 'page' => 1, 's' => 'name')
        );

        /** @var Repository $repository */
        $repository = $app['repos']->getRepositoryByContentTypeAccessHash($contentTypeAccessHash);

        if ($repository) {

            $vars['repository']           = $repository;
            $repositoryAccessHash         = $app['repos']->getRepositoryAccessHash($repository);
            $vars['links']['repository']  = $app['url_generator']->generate(
                'indexRepository',
                array('repositoryAccessHash' => $repositoryAccessHash)
            );
            $vars['links']['listRecords'] = $app['url_generator']->generate(
                'listRecords',
                array(
                    'contentTypeAccessHash' => $contentTypeAccessHash,
                    'page'                  => 1,
                    'workspace'             => $app['context']->getCurrentWorkspace(),
                    'language'              => $app['context']->getCurrentLanguage(),
                )
            );

            $app['context']->setCurrentRepository($repository);

            $contentTypeDefinition = $repository->getContentTypeDefinition();
            $app['context']->setCurrentContentType($contentTypeDefinition);
            $app['form']->setDataTypeDefinition($contentTypeDefinition);

            if ($workspace != null && $contentTypeDefinition->hasWorkspace($workspace)) {
                $app['context']->setCurrentWorkspace($workspace);
            }
            if ($language != null && $contentTypeDefinition->hasLanguage($language)) {
                $app['context']->setCurrentLanguage($language);
            }

            $repository->selectWorkspace($app['context']->getCurrentWorkspace());
            $repository->selectLanguage($app['context']->getCurrentLanguage());
            $repository->setTimeShift($app['context']->getCurrentTimeShift());
            $repository->selectView('default');

            /** @var Record $record */
            $record = $repository->getRecord($recordId);

            $buttons      = array();
            $buttons[100] = array(
                'label'     => 'List Records',
                'url'       => $app['url_generator']->generate(
                    'listRecords',
                    array(
                        'contentTypeAccessHash' => $contentTypeAccessHash,
                        'page'                  => $app['context']->getCurrentListingPage(),
                        'workspace'             => $app['context']->getCurrentWorkspace(),
                        'language'              => $app['context']->getCurrentLanguage(),
                    )
                ),
                'glyphicon' => 'glyphicon-list',
            );

            $vars['buttons'] = $app['menus']->renderButtonGroup($buttons);

            $vars['links']['timeshift']  = $app['url_generator']->generate(
                'timeShiftEditRecord',
                array('contentTypeAccessHash' => $contentTypeAccessHash, 'recordId' => $recordId)
            );
            $vars['links']['workspaces'] = $app['url_generator']->generate(
                'changeWorkspaceEditRecord',
                array('contentTypeAccessHash' => $contentTypeAccessHash, 'recordId' => $recordId)
            );
            $vars['links']['languages']  = $app['url_generator']->generate(
                'changeLanguageEditRecord',
                array('contentTypeAccessHash' => $contentTypeAccessHash, 'recordId' => $recordId)
            );

            if ($record) {
                $app['context']->setCurrentRecord($record);
                $vars['record'] = $record;

                /** @var ContentTypeDefinition $contentTypeDefinition */
                $contentTypeDefinition = $repository->getContentTypeDefinition();

                $vars['definition'] = $contentTypeDefinition;


                $revisions = $repository->getRevisionsOfRecord($recordId);

                $compare = false;
                foreach ($revisions as $revision)
                {
                    $item = ['record'=>$revision,'diff'=>false];
                    if ($compare)
                    {
                        $diff = [];
                        foreach ($contentTypeDefinition->getViewDefinition()->getFormElementDefinitions() as $formElementDefinition)
                        {
                            $property = $formElementDefinition->getName();
                            if ($property)
                            {
                                if ($revision->getProperty($property)!=$compare->getProperty($property))
                                {
                                    $html= $differ->render($revision->getProperty($property),$compare->getProperty($property));

                                    $label = $formElementDefinition->getLabel()?$formElementDefinition->getLabel():$property;
                                    $diff[]=['label'=>$label,'html'=>$html];


                                }
                            }
                        }
                        if (count($diff)>0) {
                            $item['diff'] = $diff;
                        }

                    }
                    $compare = $revision;
                    $items[]=$item;
                }

                $vars['revisions'] = $items;

                return $app->renderPage('editrecordrevision.twig', $vars);
            }
            else {
                $vars['id'] = $recordId;

                return $app->renderPage('record-not-found.twig', $vars);
            }
        }

        return $app->renderPage('forbidden.twig', $vars);
    }

}