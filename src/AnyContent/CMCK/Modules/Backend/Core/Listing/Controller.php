<?php

namespace AnyContent\CMCK\Modules\Backend\Core\Listing;

use AnyContent\Client\ContentFilter;
use CMDL\ContentTypeDefinition;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

use AnyContent\CMCK\Modules\Backend\Core\Application\Application;

use AnyContent\Client\Repository;
use AnyContent\Client\Record;
use AnyContent\Client\UserInfo;

class Controller
{

    public static function listRecords(Application $app, Request $request, $contentTypeAccessHash, $page = 1, $workspace = null, $language = null, $nr = 0)
    {

        /** @var ContentViewsManager $contentViewsManager */
        $contentViewsManager = $app['contentViews'];

        $vars = array();

        $vars['menu_mainmenu'] = $app['menus']->renderMainMenu();

        /** @var Repository $repository */
        $repository = $app['repos']->getRepositoryByContentTypeAccessHash($contentTypeAccessHash);

        $vars['repository']          = $repository;
        $repositoryAccessHash        = $app['repos']->getRepositoryAccessHashByUrl($repository->getClient()->getUrl());
        $vars['links']['repository'] = $app['url_generator']->generate('indexRepository', array( 'repositoryAccessHash' => $repositoryAccessHash ));

        $contentTypeDefinition = $repository->getContentTypeDefinition();

        $app['context']->setCurrentRepository($repository);
        $app['context']->setCurrentContentType($contentTypeDefinition);
        $app['context']->setCurrentListingPage($page);
        $vars['definition'] = $contentTypeDefinition;

        if ($workspace != null && $contentTypeDefinition->hasWorkspace($workspace))
        {
            $app['context']->setCurrentWorkspace($workspace);
        }
        if ($language != null && $contentTypeDefinition->hasLanguage($language))
        {
            $app['context']->setCurrentLanguage($language);
        }

        // Jump to record if existing id has been entered into the search field

        if ($request->query->has('q'))
        {

            if (is_numeric($request->query->get('q')))
            {
                $recordId = (int)$request->query->get('q');
                if ($repository->getRecord($recordId, $app['context']->getCurrentWorkspace(), 'default', $app['context']->getCurrentLanguage(), $app['context']->getCurrentTimeShift()))
                {
                    $app['context']->setCurrentSearchTerm('');

                    return new RedirectResponse($app['url_generator']->generate('editRecord', array( 'contentTypeAccessHash' => $contentTypeAccessHash, 'recordId' => $recordId )), 303);
                }
            }

            $app['context']->setCurrentSearchTerm($request->query->get('q'));
        }

        // Determine Content View

        $contentViews = $contentViewsManager->getContentViews($repository, $contentTypeDefinition, $contentTypeAccessHash);

        if ((int)($nr) == 0)
        {
            $nr = $app['context']->getCurrentContentViewNr();
        }

        if (count($contentViews) == 0)
        {
            $contentViews[1] = new ContentViewDefault(1, $app, $repository, $contentTypeDefinition, $contentTypeAccessHash);
        }
        $vars['contentViews'] = $contentViews;

        $currentContentView = $contentViewsManager->getContentView($repository, $contentTypeDefinition, $contentTypeAccessHash, $nr);

        if (!$currentContentView)
        {
            $currentContentView = reset($contentViews);
            $nr                 = key($contentViews);
        }

        // Switch to first content view which support search queries
        if ($request->query->has('q') && !$currentContentView->doesProcessSearch())
        {
            $error = true;
            foreach ($contentViews as $nr => $currentContentView)
            {
                if ($currentContentView->doesProcessSearch())
                {
                    $error = false;
                    break;
                }
            }
            if ($error)
            {
                $app['context']->addAlertMessage('Configuration error. Could not find content view, which is able to process search queries.');
            }
        }

        $vars['contentView']          = $currentContentView;
        $vars['currentContentViewNr'] = $nr;
        $app['context']->setCurrentContentViewNr($nr);

        // sorting links

        $vars['links']['sortById']         = $app['url_generator']->generate('listRecords', array( 'contentTypeAccessHash' => $contentTypeAccessHash, 'page' => 1, 's' => 'id' ));
        $vars['links']['sortBySubtype']    = $app['url_generator']->generate('listRecords', array( 'contentTypeAccessHash' => $contentTypeAccessHash, 'page' => 1, 's' => 'subtype' ));
        $vars['links']['sortByName']       = $app['url_generator']->generate('listRecords', array( 'contentTypeAccessHash' => $contentTypeAccessHash, 'page' => 1, 's' => 'name' ));
        $vars['links']['sortByLastChange'] = $app['url_generator']->generate('listRecords', array( 'contentTypeAccessHash' => $contentTypeAccessHash, 'page' => 1, 's' => 'change' ));
        $vars['links']['sortByStatus']     = $app['url_generator']->generate('listRecords', array( 'contentTypeAccessHash' => $contentTypeAccessHash, 'page' => 1, 's' => 'status' ));
        $vars['links']['sortByPosition']   = $app['url_generator']->generate('listRecords', array( 'contentTypeAccessHash' => $contentTypeAccessHash, 'page' => 1, 's' => 'pos' ));
        $vars['links']['search']           = $app['url_generator']->generate('listRecords', array( 'contentTypeAccessHash' => $contentTypeAccessHash, 'page' => 1, 's' => 'name', 'workspace' => $app['context']->getCurrentWorkspace(), 'language' => $app['context']->getCurrentLanguage() ));
        $vars['links']['closeSearchBox']   = $app['url_generator']->generate('listRecords', array( 'contentTypeAccessHash' => $contentTypeAccessHash, 'page' => 1, 'q' => '' ));

        // context links
        $vars['links']['timeshift']  = $app['url_generator']->generate('timeShiftListRecords', array( 'contentTypeAccessHash' => $contentTypeAccessHash, 'page' => $page ));
        $vars['links']['workspaces'] = $app['url_generator']->generate('changeWorkspaceListRecords', array( 'contentTypeAccessHash' => $contentTypeAccessHash, 'page' => $page ));
        $vars['links']['languages']  = $app['url_generator']->generate('changeLanguageListRecords', array( 'contentTypeAccessHash' => $contentTypeAccessHash, 'page' => $page ));
        $vars['links']['reset']      = $app['url_generator']->generate('listRecordsReset', array( 'contentTypeAccessHash' => $contentTypeAccessHash ));

        $buttons      = array();
        $buttons[100] = array( 'label' => 'List Records', 'url' => $app['url_generator']->generate('listRecordsReset', array( 'contentTypeAccessHash' => $contentTypeAccessHash, 'workspace' => $app['context']->getCurrentWorkspace(), 'language' => $app['context']->getCurrentLanguage() )), 'glyphicon' => 'glyphicon-list' );

        if ($contentTypeDefinition->isSortable())
        {
            $buttons[200] = array( 'label' => 'Sort Records', 'url' => $app['url_generator']->generate('sortRecords', array( 'contentTypeAccessHash' => $contentTypeAccessHash, 'workspace' => $app['context']->getCurrentWorkspace(), 'language' => $app['context']->getCurrentLanguage() )), 'glyphicon' => 'glyphicon-move' );
        }
        $buttons[300] = array( 'label' => 'Add Record', 'url' => $app['url_generator']->generate('addRecord', array( 'contentTypeAccessHash' => $contentTypeAccessHash, 'workspace' => $app['context']->getCurrentWorkspace(), 'language' => $app['context']->getCurrentLanguage() )), 'glyphicon' => 'glyphicon-plus' );
        $buttons[400] = array( 'label' => 'Export Records', 'url' => $app['url_generator']->generate('exportRecords', array( 'contentTypeAccessHash' => $contentTypeAccessHash )), 'glyphicon' => 'glyphicon-cloud-download', 'id' => 'listing_button_export' );
        $buttons[500] = array( 'label' => 'Import Records', 'url' => $app['url_generator']->generate('importRecords', array( 'contentTypeAccessHash' => $contentTypeAccessHash )), 'glyphicon' => 'glyphicon-cloud-upload', 'id' => 'listing_button_import' );

        $vars['buttons'] = $app['menus']->renderButtonGroup($buttons);

        $vars = $currentContentView->apply($vars);

        return $app->renderPage($currentContentView->getTemplate(), $vars);
    }

}