<?php

namespace AnyContent\CMCK\Modules\Backend\Core\Revisions;

use AnyContent\Client\AbstractRecord;
use AnyContent\Client\ContentFilter;
use AnyContent\CMCK\Modules\Backend\Core\Application\Application;

use AnyContent\CMCK\Modules\Backend\Core\User\UserManager;
use CMDL\ContentTypeDefinition;
use CMDL\DataTypeDefinition;
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
use Symfony\Component\Yaml\Yaml;

class Controller
{

    public static function listRecordRevisions(Application $app, $contentTypeAccessHash, $recordId, $workspace, $language)
    {
        /** @var UserManager $user */
        $user = $app['user'];

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

                $properties = self::getPropertiesForDiff($contentTypeDefinition);

                /** @var Record|false $compare */
                $compare = false;
                foreach ($revisions as $revision) {

                    if ($compare) {
                        $item = ['record' => $compare, 'diff' => self::diffRecords($compare, $revision, $properties)];

                        $item ['username'] = $compare->getLastChangeUserInfo()->getName();
                        $item ['gravatar'] = md5($compare->getLastChangeUserInfo()->getUsername());
                        $item ['date'] = $compare->getLastChangeUserInfo()->getTimestamp();
                        $items[] = $item;
                    }
                    if ($revision === end($revisions)) {
                        $item    = ['record' => $revision, 'diff' => self::diffRecords($revision, null, $properties)];
                        $item ['username'] = $revision->getLastChangeUserInfo()->getName();
                        $item ['gravatar'] = md5($revision->getLastChangeUserInfo()->getUsername());
                        $item ['date'] = $revision->getLastChangeUserInfo()->getTimestamp();
                        $items[] = $item;
                    }

                    $compare = $revision;
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

    protected static function getPropertiesForDiff(DataTypeDefinition $dataTypeDefinition)
    {
        $properties = [];

        // First add properties from view definition with labels

        foreach ($dataTypeDefinition->getViewDefinition()->getFormElementDefinitions() as $formElementDefinition) {
            if ($formElementDefinition->getName()) {
                $properties[$formElementDefinition->getName()] = $formElementDefinition->getLabel();
            }
        }

        // Then add all available properties not yet added

        $properties = array_merge(array_combine($dataTypeDefinition->getProperties(), $dataTypeDefinition->getProperties()), $properties);

        return $properties;
    }

    /**
     * @param AbstractRecord      $record1
     * @param AbstractRecord|null $record2
     * @param                     $properties
     */
    protected static function diffRecords(AbstractRecord $record1, $record2 = null, $properties)
    {

        $differ = new Diff();
        $diff   = [];
        foreach ($properties as $property => $label) {
            $value1 = $record1->getProperty($property);
            $value2 = '';
            if ($record2) {
                $value2 = $record2->getProperty($property);
            }
            if ($value1 != $value2) {

                $jsontest = json_decode($value1, true);
                if (json_last_error() == JSON_ERROR_NONE && is_array($jsontest)) {

                    $value1 = Yaml::dump($jsontest,4);
                    $value2 = Yaml::dump(json_decode($value2,true),4);
                    if ($value2=='null')
                    {
                        $value2='';
                    }
                }


                $html   = $differ->render($value2, $value1);
                $diff[] = ['label' => $label, 'html' => $html];
            }
        }
        if (count($diff) > 0) {
            return $diff;
        }

        return false;
        /*
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
                      }*/
        if (count($diff) > 0) {
            $item['diff'] = $diff;
        }
    }

}