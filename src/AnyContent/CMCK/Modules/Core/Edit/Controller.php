<?php

namespace AnyContent\CMCK\Modules\Core\Edit;

use AnyContent\CMCK\Modules\Core\Application\Application;

use CMDL\ContentTypeDefinition;
use CMDL\ClippingDefinition;

use AnyContent\Client\Repository;
use AnyContent\Client\Record;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class Controller
{

    public static function addRecord(Application $app, $contentTypeAccessHash)
    {
        $vars = array();

        $vars['menu_mainmenu'] = $app['menus']->renderMainMenu();

        /** @var Repository $repository */
        $repository = $app['repos']->getRepositoryContentAccessByHash($contentTypeAccessHash);

        $app['context']->setCurrentContentType($repository->getContentTypeDefinition());

        $app['layout']->addCssFile('listing.css');

        $vars['record'] = false;

        /** @var ContentTypeDefinition $contentTypeDefinition */
        $contentTypeDefinition = $repository->getContentTypeDefinition();

        $vars['definition'] = $contentTypeDefinition;

        if ($contentTypeDefinition->hasInsertOperation())
        {
            /* @var ClippingDefinition */

            $clippingDefinition = $contentTypeDefinition->getClippingDefinition('default');

            $vars['form'] = $app['form']->renderFormElements('form_edit', $clippingDefinition->getFormElementDefinitions());

            $buttons   = array();
            $buttons[] = array( 'label' => 'List Records', 'url' => $app['url_generator']->generate('listRecords', array( 'contentTypeAccessHash' => $contentTypeAccessHash, 'page' => 1 )), 'glyphicon' => 'glyphicon-list' );
            $buttons[] = array( 'label' => 'Sort Records', 'url' => $app['url_generator']->generate('sortRecords', array( 'contentTypeAccessHash' => $contentTypeAccessHash )), 'glyphicon' => 'glyphicon-move' );
            $buttons[] = array( 'label' => 'Add Record', 'url' => $app['url_generator']->generate('addRecord', array( 'contentTypeAccessHash' => $contentTypeAccessHash )), 'glyphicon' => 'glyphicon-plus' );

            $vars['buttons'] = $app['menus']->renderButtonGroup($buttons);

            $saveoperation = $app['context']->getCurrentSaveOperation();

            $vars['save_operation']       = key($saveoperation);
            $vars['save_operation_title'] = array_shift($saveoperation);

            return $app['layout']->render('editrecord.twig', $vars);
        }
        else
        {
            return $app['layout']->render('forbidden.twig', $vars);
        }

    }


    public static function editRecord(Application $app, $contentTypeAccessHash, $recordId)
    {
        $vars = array();

        $vars['menu_mainmenu'] = $app['menus']->renderMainMenu();

        /** @var Repository $repository */
        $repository = $app['repos']->getRepositoryContentAccessByHash($contentTypeAccessHash);

        $app['context']->setCurrentContentType($repository->getContentTypeDefinition());

        /** @var Record $record */
        $record = $repository->getRecord($recordId, $app['context']->getCurrentWorkspace(), 'default', $app['context']->getCurrentLanguage(), $app['context']->getCurrentTimeShift());

        $app['layout']->addCssFile('listing.css');
        $app['layout']->addJsFile('savebutton.js');

        if ($record)
        {
            $vars['record'] = $record;

            /** @var ContentTypeDefinition $contentTypeDefinition */
            $contentTypeDefinition = $repository->getContentTypeDefinition();

            $vars['definition'] = $contentTypeDefinition;

            /* @var ClippingDefinition */
            $clippingDefinition = $contentTypeDefinition->getClippingDefinition('default');

            $vars['form'] = $app['form']->renderFormElements('form_edit', $clippingDefinition->getFormElementDefinitions(), $record);

            $buttons      = array();
            $buttons[100] = array( 'label' => 'List Records', 'url' => $app['url_generator']->generate('listRecords', array( 'contentTypeAccessHash' => $contentTypeAccessHash, 'page' => $app['context']->getCurrentListingPage() )), 'glyphicon' => 'glyphicon-list' );
            $buttons[200] = array( 'label' => 'Sort Records', 'url' => $app['url_generator']->generate('sortRecords', array( 'contentTypeAccessHash' => $contentTypeAccessHash )), 'glyphicon' => 'glyphicon-move' );
            $buttons[300] = array( 'label' => 'Add Record', 'url' => $app['url_generator']->generate('addRecord', array( 'contentTypeAccessHash' => $contentTypeAccessHash )), 'glyphicon' => 'glyphicon-plus' );

            $vars['buttons'] = $app['menus']->renderButtonGroup($buttons);

            $saveoperation = $app['context']->getCurrentSaveOperation();

            $vars['save_operation']       = key($saveoperation);
            $vars['save_operation_title'] = array_shift($saveoperation);

            return $app['layout']->render('editrecord.twig', $vars);
        }

        return $app['layout']->render('record-notfound.twig', $vars);

    }


    public static function saveRecord(Application $app, Request $request, $contentTypeAccessHash, $recordId = null)
    {
        $hidden = $request->get('hidden');

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
        $repository = $app['repos']->getRepositoryContentAccessByHash($contentTypeAccessHash);

        $app['context']->setCurrentContentType($repository->getContentTypeDefinition());
        $app['context']->setCurrentSaveOperation($saveOperation, $saveOperationTitle);

        if ($recordId)
        {
            /** @var Record $record */
            $record = $repository->getRecord($recordId, $app['context']->getCurrentWorkspace(), 'default', $app['context']->getCurrentLanguage(), $app['context']->getCurrentTimeShift());
        }
        else
        {
            $record = new Record($repository->getContentTypeDefinition(), 'New Record', 'default', $app['context']->getCurrentWorkspace(), 'default', $app['context']->getCurrentLanguage());
        }

        if ($record)
        {
            /** @var ContentTypeDefinition $contentTypeDefinition */
            $contentTypeDefinition = $repository->getContentTypeDefinition();

            /* @var ClippingDefinition */
            $clippingDefinition = $contentTypeDefinition->getClippingDefinition('default');

            $values = $app['form']->extractFormElementValuesFromPostRequest($request, $clippingDefinition->getFormElementDefinitions());

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