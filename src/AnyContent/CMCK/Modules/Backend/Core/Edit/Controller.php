<?php

namespace AnyContent\CMCK\Modules\Backend\Core\Edit;

use AnyContent\CMCK\Modules\Backend\Core\Application\Application;

use CMDL\ContentTypeDefinition;
use CMDL\ViewDefinition;

use AnyContent\Client\Repository;
use AnyContent\Client\Record;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class Controller
{

    public static function addRecord(Application $app, $contentTypeAccessHash, $recordId = null)
    {
        $vars = array();

        $vars['menu_mainmenu'] = $app['menus']->renderMainMenu();

        /** @var Repository $repository */
        $repository = $app['repos']->getRepositoryByContentTypeAccessHash($contentTypeAccessHash);

        if ($repository)
        {
            $app['context']->setCurrentRepository($repository);
            $app['context']->setCurrentContentType($repository->getContentTypeDefinition());

            $app['form']->setDataTypeDefinition($repository->getContentTypeDefinition());

            $app['layout']->addCssFile('listing.css');
            $app['layout']->addJsFile('edit.js');
            $app['layout']->addJsFile('editrecord.js');

            $vars['record'] = false;

            /** @var ContentTypeDefinition $contentTypeDefinition */
            $contentTypeDefinition = $repository->getContentTypeDefinition();

            $vars['definition'] = $contentTypeDefinition;

            if ($contentTypeDefinition->hasInsertOperation())
            {
                /* @var ViewDefinition */

                $viewDefinition = $contentTypeDefinition->getInsertViewDefinition();

                $vars['form'] = $app['form']->renderFormElements('form_edit', $viewDefinition->getFormElementDefinitions(), array(), array( 'workspace' => $app['context']->getCurrentWorkspace(), 'language' => $app['context']->getCurrentLanguage() ));

                $buttons   = array();
                $buttons[] = array( 'label' => 'List Records', 'url' => $app['url_generator']->generate('listRecords', array( 'contentTypeAccessHash' => $contentTypeAccessHash, 'page' => 1, 'workspace' => $app['context']->getCurrentWorkspace(), 'language' => $app['context']->getCurrentLanguage() )), 'glyphicon' => 'glyphicon-list' );
                $buttons[] = array( 'label' => 'Sort Records', 'url' => $app['url_generator']->generate('sortRecords', array( 'contentTypeAccessHash' => $contentTypeAccessHash, 'workspace' => $app['context']->getCurrentWorkspace(), 'language' => $app['context']->getCurrentLanguage() )), 'glyphicon' => 'glyphicon-move' );
                //$buttons[] = array( 'label' => 'Import Records', 'url' => $app['url_generator']->generate('importRecords', array( 'contentTypeAccessHash' => $contentTypeAccessHash )), 'glyphicon' => 'glyphicon-transfer' );
                //$buttons[] = array( 'label' => 'Export Records', 'url' => $app['url_generator']->generate('exportRecords', array( 'contentTypeAccessHash' => $contentTypeAccessHash )), 'glyphicon' => 'glyphicon-transfer' );
                $buttons[] = array( 'label' => 'Add Record', 'url' => $app['url_generator']->generate('addRecord', array( 'contentTypeAccessHash' => $contentTypeAccessHash )), 'glyphicon' => 'glyphicon-plus' );

                $vars['buttons'] = $app['menus']->renderButtonGroup($buttons);

                $saveoperation = $app['context']->getCurrentSaveOperation();

                $vars['save_operation']       = key($saveoperation);
                $vars['save_operation_title'] = array_shift($saveoperation);

                $vars['links']['search']     = $app['url_generator']->generate('listRecords', array( 'contentTypeAccessHash' => $contentTypeAccessHash, 'page' => 1, 's' => 'name', 'workspace' => $app['context']->getCurrentWorkspace(), 'language' => $app['context']->getCurrentLanguage() ));
                $vars['links']['timeshift']  = false;
                $vars['links']['workspaces'] = $app['url_generator']->generate('changeWorkspaceAddRecord', array( 'contentTypeAccessHash' => $contentTypeAccessHash ));
                $vars['links']['languages']  = $app['url_generator']->generate('changeLanguageAddRecord', array( 'contentTypeAccessHash' => $contentTypeAccessHash ));

                return $app->renderPage('editrecord.twig', $vars);
            }
            else
            {
                return $app->renderPage('forbidden.twig', $vars);
            }
        }
    }


    public static function editRecord(Application $app, $contentTypeAccessHash, $recordId, $workspace, $language)
    {

        $vars = array();

        $vars['menu_mainmenu']   = $app['menus']->renderMainMenu();
        $vars['links']['search'] = $app['url_generator']->generate('listRecords', array( 'contentTypeAccessHash' => $contentTypeAccessHash, 'page' => 1, 's' => 'name' ));

        /** @var Repository $repository */
        $repository = $app['repos']->getRepositoryByContentTypeAccessHash($contentTypeAccessHash);

        if ($repository)
        {
            $app['context']->setCurrentRepository($repository);

            $contentTypeDefinition = $repository->getContentTypeDefinition();
            $app['context']->setCurrentContentType($contentTypeDefinition);
            $app['form']->setDataTypeDefinition($contentTypeDefinition);

            if ($workspace != null && $contentTypeDefinition->hasWorkspace($workspace))
            {
                $app['context']->setCurrentWorkspace($workspace);
            }
            if ($language != null && $contentTypeDefinition->hasLanguage($language))
            {
                $app['context']->setCurrentLanguage($language);
            }

            /** @var Record $record */
            $record = $repository->getRecord($recordId, $app['context']->getCurrentWorkspace(), 'default', $app['context']->getCurrentLanguage(), $app['context']->getCurrentTimeShift());

            $app['layout']->addCssFile('listing.css');
            $app['layout']->addJsFile('edit.js');
            $app['layout']->addJsFile('editrecord.js');

            $buttons      = array();
            $buttons[100] = array( 'label' => 'List Records', 'url' => $app['url_generator']->generate('listRecords', array( 'contentTypeAccessHash' => $contentTypeAccessHash, 'page' => $app['context']->getCurrentListingPage(), 'workspace' => $app['context']->getCurrentWorkspace(), 'language' => $app['context']->getCurrentLanguage() )), 'glyphicon' => 'glyphicon-list' );
            $buttons[200] = array( 'label' => 'Sort Records', 'url' => $app['url_generator']->generate('sortRecords', array( 'contentTypeAccessHash' => $contentTypeAccessHash, 'workspace' => $app['context']->getCurrentWorkspace(), 'language' => $app['context']->getCurrentLanguage() )), 'glyphicon' => 'glyphicon-move' );
            //$buttons[]    = array( 'label' => 'Import Records', 'url' => $app['url_generator']->generate('importRecords', array( 'contentTypeAccessHash' => $contentTypeAccessHash )), 'glyphicon' => 'glyphicon-transfer' );
            //$buttons[]    = array( 'label' => 'Export Records', 'url' => $app['url_generator']->generate('exportRecords', array( 'contentTypeAccessHash' => $contentTypeAccessHash )), 'glyphicon' => 'glyphicon-transfer' );
            $buttons[300] = array( 'label' => 'Add Record', 'url' => $app['url_generator']->generate('addRecord', array( 'contentTypeAccessHash' => $contentTypeAccessHash, 'workspace' => $app['context']->getCurrentWorkspace(), 'language' => $app['context']->getCurrentLanguage() )), 'glyphicon' => 'glyphicon-plus' );

            $vars['buttons'] = $app['menus']->renderButtonGroup($buttons);

            $saveoperation = $app['context']->getCurrentSaveOperation();

            $vars['save_operation']       = key($saveoperation);
            $vars['save_operation_title'] = array_shift($saveoperation);

            $vars['links']['search']           = $app['url_generator']->generate('listRecords', array( 'contentTypeAccessHash' => $contentTypeAccessHash, 'page' => 1, 's' => 'name', 'workspace' => $app['context']->getCurrentWorkspace(), 'language' => $app['context']->getCurrentLanguage() ));
            $vars['links']['delete']           = $app['url_generator']->generate('deleteRecord', array( 'contentTypeAccessHash' => $contentTypeAccessHash, 'recordId' => $recordId, 'workspace' => $app['context']->getCurrentWorkspace(), 'language' => $app['context']->getCurrentLanguage() ));
            $vars['links']['transfer']         = $app['url_generator']->generate('transferRecordModal', array( 'contentTypeAccessHash' => $contentTypeAccessHash, 'recordId' => $recordId, 'workspace' => $app['context']->getCurrentWorkspace(), 'language' => $app['context']->getCurrentLanguage() ));
            $vars['links']['timeshift']        = $app['url_generator']->generate('timeShiftEditRecord', array( 'contentTypeAccessHash' => $contentTypeAccessHash, 'recordId' => $recordId ));
            $vars['links']['workspaces']       = $app['url_generator']->generate('changeWorkspaceEditRecord', array( 'contentTypeAccessHash' => $contentTypeAccessHash, 'recordId' => $recordId ));
            $vars['links']['languages']        = $app['url_generator']->generate('changeLanguageEditRecord', array( 'contentTypeAccessHash' => $contentTypeAccessHash, 'recordId' => $recordId ));
            $vars['links']['addrecordversion'] = $app['url_generator']->generate('addRecordVersion', array( 'contentTypeAccessHash' => $contentTypeAccessHash, 'recordId' => $recordId, 'workspace' => $app['context']->getCurrentWorkspace(), 'language' => $app['context']->getCurrentLanguage() ));

            if ($record)
            {
                $app['context']->setCurrentRecord($record);
                $vars['record'] = $record;

                /** @var ContentTypeDefinition $contentTypeDefinition */
                $contentTypeDefinition = $repository->getContentTypeDefinition();

                $vars['definition'] = $contentTypeDefinition;

                /* @var ViewDefinition */
                $viewDefinition = $contentTypeDefinition->getViewDefinition('default');

                $vars['form'] = $app['form']->renderFormElements('form_edit', $viewDefinition->getFormElementDefinitions(), $record->getProperties(), $record->getAttributes());

                return $app->renderPage('editrecord.twig', $vars);
            }
            else
            {
                $vars['id']=$recordId;
                return $app->renderPage('record-not-found.twig', $vars);
            }
        }

        return $app->renderPage('forbidden.twig', $vars);
    }


    public static function saveRecord(Application $app, Request $request, $contentTypeAccessHash, $recordId = null)
    {

        $hidden = $request->get('$hidden');

        $saveOperationTitle = 'Save';
        $saveOperation      = 'save';
        $save               = true;
        $duplicate          = false;
        $insert             = false;
        $list               = false;

        switch ($hidden['save_operation'])
        {
            case 'save-insert':
                $saveOperationTitle = 'Save & Insert';
                $saveOperation      = 'save-insert';
                $insert             = true;
                break;
            case 'save-duplicate':
                $saveOperationTitle = 'Save & Duplicate';
                $saveOperation      = 'save-duplicate';
                $duplicate          = true;
                break;
            case 'save-list':
                $saveOperationTitle = 'Save & List';
                $saveOperation      = 'save-list';
                $list               = true;
                break;
            case 'save-insert':
                $saveOperationTitle = 'Save & Insert';
                $saveOperation      = 'save-insert';
                $insert             = true;
                break;
        }

        /** @var Repository $repository */
        $repository = $app['repos']->getRepositoryByContentTypeAccessHash($contentTypeAccessHash);

        if ($repository)
        {
            $app['context']->setCurrentRepository($repository);
            $app['context']->setCurrentContentType($repository->getContentTypeDefinition());

            $app['form']->setDataTypeDefinition($repository->getContentTypeDefinition());

            $app['context']->setCurrentSaveOperation($saveOperation, $saveOperationTitle);

            $app['context']->setCurrentWorkspace($hidden['workspace']);
            $app['context']->setCurrentLanguage($hidden['language']);

            if ($recordId)
            {
                /** @var Record $record */
                $record = $repository->getRecord($recordId, $app['context']->getCurrentWorkspace(), 'default', $app['context']->getCurrentLanguage(), $app['context']->getCurrentTimeShift());

                if (!$record) // if we don't have a record with the given id (there never was one, or it has been deleted), create a new one with the given id.
                {
                    $record = new Record($repository->getContentTypeDefinition(), 'New Record', 'default', $app['context']->getCurrentWorkspace(), 'default', $app['context']->getCurrentLanguage());
                    $record->setId($recordId);
                }
            }
            else
            {
                $record = new Record($repository->getContentTypeDefinition(), 'New Record', 'default', $app['context']->getCurrentWorkspace(), 'default', $app['context']->getCurrentLanguage());
            }

            if ($record)
            {
                /** @var ContentTypeDefinition $contentTypeDefinition */
                $contentTypeDefinition = $repository->getContentTypeDefinition();

                /* @var ViewDefinition */
                if ($recordId)
                {
                    $viewDefinition = $contentTypeDefinition->getEditViewDefinition();
                }
                else
                {
                    $viewDefinition = $contentTypeDefinition->getInsertViewDefinition();
                }

                $values = $app['form']->extractFormElementValuesFromPostRequest($request, $viewDefinition->getFormElementDefinitions(), $record->getProperties(), $record->getAttributes());

                foreach ($values as $property => $value)
                {
                    $record->setProperty($property, $value);
                }

                if ($save)
                {
                    $recordId = $repository->saveRecord($record, $app['context']->getCurrentWorkspace(), 'default', $app['context']->getCurrentLanguage());

                    $app['context']->resetTimeShift();
                    if ($recordId)
                    {
                        $app['context']->addSuccessMessage('Record saved.');
                    }
                    else
                    {
                        $app['context']->addErrorMessage('Could not save record.');

                        return new RedirectResponse($app['url_generator']->generate('listRecords', array( 'contentTypeAccessHash' => $contentTypeAccessHash, 'page' => $app['context']->getCurrentListingPage() )), 303);
                    }
                }
                if ($duplicate)
                {
                    $record->setID(null);
                    $recordId = $repository->saveRecord($record, $app['context']->getCurrentWorkspace(), 'default', $app['context']->getCurrentLanguage());
                    $app['context']->resetTimeShift();
                }

                if ($insert)
                {
                    return new RedirectResponse($app['url_generator']->generate('addRecord', array( 'contentTypeAccessHash' => $contentTypeAccessHash )), 303);
                }

                if ($list)
                {
                    return new RedirectResponse($app['url_generator']->generate('listRecords', array( 'contentTypeAccessHash' => $contentTypeAccessHash, 'page' => $app['context']->getCurrentListingPage() )), 303);
                }

                return new RedirectResponse($app['url_generator']->generate('editRecord', array( 'contentTypeAccessHash' => $contentTypeAccessHash, 'recordId' => $recordId )), 303);
            }
            else
            {
                return $app['layout']->render('record-notfound.twig');
            }
        }
    }


    public function deleteRecord(Application $app, Request $request, $contentTypeAccessHash, $recordId, $workspace, $language)
    {
        $recordId = (int)$recordId;

        if ($recordId)
        {
            /** @var Repository $repository */
            $repository = $app['repos']->getRepositoryByContentTypeAccessHash($contentTypeAccessHash);

            if ($repository)
            {
                $app['context']->setCurrentRepository($repository);
                $contentTypeDefinition = $repository->getContentTypeDefinition();
                $app['context']->setCurrentContentType($contentTypeDefinition);

                if ($workspace != null && $contentTypeDefinition->hasWorkspace($workspace))
                {
                    $app['context']->setCurrentWorkspace($workspace);
                }
                if ($language != null && $contentTypeDefinition->hasLanguage($language))
                {
                    $app['context']->setCurrentLanguage($language);
                }

                if ($repository->deleteRecord($recordId, $app['context']->getCurrentWorkspace(), $app['context']->getCurrentLanguage()))
                {
                    $app['context']->addSuccessMessage('Record ' . $recordId . ' deleted.');
                }
                else
                {
                    $app['context']->addErrorMessage('Could not delete record.');
                }
            }

            return new RedirectResponse($app['url_generator']->generate('listRecords', array( 'contentTypeAccessHash' => $contentTypeAccessHash, 'page' => $app['context']->getCurrentListingPage() )), 303);
        }
    }


    /**
     * Displays the transfer record dialog
     *
     * @param Application $app
     * @param Request     $request
     * @param             $contentTypeAccessHash
     * @param             $recordId
     * @param             $workspace
     * @param             $language
     *
     * @return mixed
     */
    public function transferRecordModal(Application $app, Request $request, $contentTypeAccessHash, $recordId, $workspace, $language)
    {
        $vars           = array();
        $vars['record'] = false;

        $recordId = (int)$recordId;

        if ($recordId)
        {
            /** @var Repository $repository */
            $repository = $app['repos']->getRepositoryByContentTypeAccessHash($contentTypeAccessHash);

            if ($repository)
            {
                $contentTypeDefinition = $repository->getContentTypeDefinition();
                $app['context']->setCurrentRepository($repository);
                $app['context']->setCurrentContentType($contentTypeDefinition);

                if ($workspace != null && $contentTypeDefinition->hasWorkspace($workspace))
                {
                    $app['context']->setCurrentWorkspace($workspace);
                }
                if ($language != null && $contentTypeDefinition->hasLanguage($language))
                {
                    $app['context']->setCurrentLanguage($language);
                }

                /** @var Record $record */
                $record = $repository->getRecord($recordId, $app['context']->getCurrentWorkspace(), 'default', $app['context']->getCurrentLanguage(), $app['context']->getCurrentTimeShift());

                if ($record)
                {
                    $app['context']->setCurrentRecord($record);
                    $vars['record'] = $record;

                    /** @var ContentTypeDefinition $contentTypeDefinition */
                    $contentTypeDefinition = $repository->getContentTypeDefinition();

                    $vars['definition'] = $contentTypeDefinition;

                    $records = array();
                    foreach ($repository->getRecords($app['context']->getCurrentWorkspace(), 'default', $app['context']->getCurrentLanguage()) as $record)
                    {
                        $records[$record->getID()] = '#' . $record->getID() . ' ' . $record->getName();
                    }
                    $vars['records'] = $records;

                    $vars['links']['transfer'] = $app['url_generator']->generate('transferRecord', array( 'contentTypeAccessHash' => $contentTypeAccessHash, 'recordId' => $recordId, "workspace" => $app['context']->getCurrentWorkspace(), "language" => $app['context']->getCurrentLanguage() ));
                }
            }
        }

        return $app->renderPage('transferrecord-modal.twig', $vars);
    }


    public function transferRecord(Application $app, Request $request, $contentTypeAccessHash, $recordId, $workspace, $language)
    {
        $recordId = (int)$recordId;

        if ($recordId)
        {
            /** @var Repository $repository */
            $repository = $app['repos']->getRepositoryByContentTypeAccessHash($contentTypeAccessHash);

            if ($repository)
            {
                $app['context']->setCurrentRepository($repository);
                $app['context']->setCurrentContentType($repository->getContentTypeDefinition());

                /** @var ContentTypeDefinition $contentTypeDefinition */
                $contentTypeDefinition = $repository->getContentTypeDefinition();

                if ($workspace != null && $contentTypeDefinition->hasWorkspace($workspace))
                {
                    $app['context']->setCurrentWorkspace($workspace);
                }
                if ($language != null && $contentTypeDefinition->hasLanguage($language))
                {
                    $app['context']->setCurrentLanguage($language);
                }

                /** @var Record $record */
                $record = $repository->getRecord($recordId, $app['context']->getCurrentWorkspace(), $contentTypeDefinition
                    ->getExchangeViewDefinition()
                    ->getName(), $app['context']->getCurrentLanguage(), $app['context']->getCurrentTimeShift());

                if ($record)
                {
                    $record->setID((int)$request->get('id'));
                    $workspace = $app['context']->getCurrentWorkspace();
                    if ($request->request->has('target_workspace'))
                    {
                        $workspace = $request->get('target_workspace');
                        $app['context']->setCurrentWorkspace($workspace);
                    }
                    $language = $app['context']->getCurrentLanguage();
                    if ($request->request->has('target_language'))
                    {
                        $language = $request->get('target_language');
                        $app['context']->setCurrentLanguage($language);
                    }

                    $recordId = $repository->saveRecord($record, $workspace, $contentTypeDefinition
                        ->getExchangeViewDefinition()
                        ->getName(), $language);
                    $app['context']->resetTimeShift();

                    $app['context']->addSuccessMessage('Record ' . $recordId . ' transfered.');

                    return new RedirectResponse($app['url_generator']->generate('editRecord', array( 'contentTypeAccessHash' => $contentTypeAccessHash, 'recordId' => $recordId )), 303);
                }
            }
        }

        $app['context']->addErrorMessage('Could not load source record.');

        return new RedirectResponse($app['url_generator']->generate('listRecords', array( 'contentTypeAccessHash' => $contentTypeAccessHash, 'page' => $app['context']->getCurrentListingPage() )), 303);
    }
}