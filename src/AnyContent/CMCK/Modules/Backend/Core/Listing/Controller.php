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

    public static function listRecords(Application $app, Request $request, $contentTypeAccessHash, $page = 1, $workspace = null, $language = null, $nr = 1)
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

        $contentViews = $contentViewsManager->getContentViews($repository, $contentTypeDefinition, $contentTypeAccessHash);

        if (count($contentViews) == 0)
        {
            $contentViews[1] = new ContentViewDefault(1, $app, $contentTypeDefinition, $contentTypeAccessHash);
        }
        $vars['contentViews'] = $contentViews;

        $currentContentView = $contentViewsManager->getContentView($repository, $contentTypeDefinition, $contentTypeAccessHash, $nr);

        if (!$currentContentView)
        {

            $currentContentView = reset($contentViews);
            $nr                 = key($contentViews);

        }

        $vars['currentContentViewNr'] = $nr;


        $vars = $currentContentView->apply($vars);


        // reset chained save operations to 'save' only upon listing of a content type
        if (key($app['context']->getCurrentSaveOperation()) != 'save-list')
        {
            $app['context']->setCurrentSaveOperation('save', 'Save');
        }

        // check for sorting/search query parameters

        if ($request->query->has('s'))
        {
            $app['context']->setCurrentSortingOrder($request->query->get('s'));
        }
        if ($request->query->has('q'))
        {
            $app['context']->setCurrentSearchTerm($request->query->get('q'));
        }
        if ($request->get('_route') == 'listRecordsReset')
        {
            $app['context']->setCurrentSortingOrder('id', false);
            $app['context']->setCurrentSearchTerm('');
        }

        $itemsPerPage = $app['context']->getCurrentItemsPerPage();

        $filter = null;

        $searchTerm         = $app['context']->getCurrentSearchTerm();
        $vars['searchTerm'] = $searchTerm;
        if ($searchTerm != '')
        {

            if (is_numeric($searchTerm))
            {
                $recordId = (int)$searchTerm;
                if ($repository->getRecord($recordId, $app['context']->getCurrentWorkspace(), 'default', $app['context']->getCurrentLanguage(), $app['context']->getCurrentTimeShift()))
                {
                    $app['context']->setCurrentSearchTerm('');

                    return new RedirectResponse($app['url_generator']->generate('editRecord', array( 'contentTypeAccessHash' => $contentTypeAccessHash, 'recordId' => $recordId )), 303);
                }
            }
            $filter = FilterUtil::normalizeFilterQuery($app, $searchTerm, $contentTypeDefinition);

        }

        $vars['records'] = self::getRecords($app, $repository, $contentTypeAccessHash, null, 'default', $itemsPerPage, $page, $filter);

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

        //$app['layout']->addJsFile('app.js');

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

        $count = $repository->countRecords($app['context']->getCurrentWorkspace(), 'default', $app['context']->getCurrentLanguage(), $app['context']->getCurrentSortingOrder(), array(), $itemsPerPage, $page, $filter, null, $app['context']->getCurrentTimeShift());

        $vars['pager'] = $app['pager']->renderPager($count, $itemsPerPage, $page, 'listRecords', array( 'contentTypeAccessHash' => $contentTypeAccessHash ));


        return $app->renderPage($currentContentView->getTemplate(), $vars);
    }


    protected static function getRecords($app, Repository $repository, $contentTypeAccessHash, $orderBy = null, $viewName = 'default', $itemsPerPage = null, $page = 1, $filter = null, $subset = null)
    {
        $records = array();

        if (!$orderBy)
        {
            $orderBy = $app['context']->getCurrentSortingOrder();
        }

        /** @var Record $record */
        foreach ($repository->getRecords($app['context']->getCurrentWorkspace(), $viewName, $app['context']->getCurrentLanguage(), $orderBy, array(), $itemsPerPage, $page, $filter, $subset, $app['context']->getCurrentTimeShift()) AS $record)
        {
            $item                     = array();
            $item['record']           = $record;
            $item['name']             = $record->getName();
            $item['id']               = $record->getID();
            $item['editUrl']          = $app['url_generator']->generate('editRecord', array( 'contentTypeAccessHash' => $contentTypeAccessHash, 'recordId' => $record->getID(), 'workspace' => $app['context']->getCurrentWorkspace(), 'language' => $app['context']->getCurrentLanguage() ));
            $item['deleteUrl']        = $app['url_generator']->generate('deleteRecord', array( 'contentTypeAccessHash' => $contentTypeAccessHash, 'recordId' => $record->getID(), 'workspace' => $app['context']->getCurrentWorkspace(), 'language' => $app['context']->getCurrentLanguage() ));
            $item['status']['label']  = $record->getStatusLabel();
            $item['subtype']['label'] = $record->getSubtypeLabel();
            $item['position']         = $record->getPosition();
            $item['level']            = $record->getLevelWithinSortedTree();

            /** @var UserInfo $userInfo */
            $userInfo         = $record->getLastChangeUserInfo();
            $item['username'] = $userInfo->getName();
            $date             = new \DateTime();
            $date->setTimestamp($userInfo->getTimestamp());
            $item['lastChangeDate'] = $date->format('d.m.Y H:i:s');
            $item['gravatar']       = '<img src="https://www.gravatar.com/avatar/' . md5(trim($userInfo->getUsername())) . '?s=40" height="40" width="40"/>';

            $records[] = $item;
        }

        return $records;
    }

}