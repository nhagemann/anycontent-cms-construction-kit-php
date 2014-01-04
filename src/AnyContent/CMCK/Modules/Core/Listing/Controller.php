<?php

namespace AnyContent\CMCK\Modules\Core\Listing;

use AnyContent\Client\ContentFilter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use AnyContent\CMCK\Modules\Core\Application\Application;

use AnyContent\Client\Repository;
use AnyContent\Client\Record;
use AnyContent\Client\UserInfo;

class Controller
{

    public static function listRecords(Application $app, Request $request, $contentTypeAccessHash, $page = 1)
    {

        // reset chained save operations to 'save' only upon listing of a content type
        if (key($app['context']->getCurrentSaveOperation()) != 'save-list')
        {
            $app['context']->setCurrentSaveOperation('save', 'Save');
        }

        $vars = array();

        $vars['menu_mainmenu'] = $app['menus']->renderMainMenu();

        /** @var Repository $repository */
        $repository = $app['repos']->getRepositoryContentAccessByHash($contentTypeAccessHash);

        $contentTypeDefinition = $repository->getContentTypeDefinition();
        $app['context']->setCurrentContentType($contentTypeDefinition);
        $app['context']->setCurrentListingPage($page);
        $vars['definition'] = $contentTypeDefinition;

        $records = array();

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
            $filter = new ContentFilter($contentTypeDefinition);
            $filter->addCondition('name', '><', $searchTerm);
        }

        /** @var Record $record */
        foreach ($repository->getRecords($app['context']->getCurrentWorkspace(), 'default', $app['context']->getCurrentLanguage(), $app['context']->getCurrentSortingOrder(), array(), $itemsPerPage, $page, $filter, $app['context']->getCurrentTimeShift()) AS $record)
        {
            $item                     = array();
            $item['record']           = $record;
            $item['name']             = $record->getName();
            $item['id']               = $record->getID();
            $item['editUrl']          = $app['url_generator']->generate('editRecord', array( 'contentTypeAccessHash' => $contentTypeAccessHash, 'recordId' => $record->getID() ));
            $item['deleteUrl']        = $app['url_generator']->generate('deleteRecord', array( 'contentTypeAccessHash' => $contentTypeAccessHash, 'recordId' => $record->getID() ));
            $item['status']['label']  = $record->getStatusLabel();
            $item['subtype']['label'] = $record->getSubtypeLabel();

            /** @var UserInfo $userInfo */
            $userInfo         = $record->getLastChangeUserInfo();
            $item['username'] = $userInfo->getName();
            $date             = new \DateTime();
            $date->setTimestamp($userInfo->getTimestamp());
            $item['lastChangeDate'] = $date->format('d.m.Y H:i:s');
            $item['gravatar']       = '<img src="https://www.gravatar.com/avatar/' . md5(trim($userInfo->getUsername())) . '?s=40" height="40" width="40"/>';

            $records[] = $item;
        }

        $vars['records'] = $records;

        // sorting links

        $vars['links']['sortById']         = $app['url_generator']->generate('listRecords', array( 'contentTypeAccessHash' => $contentTypeAccessHash, 'page' => 1, 's' => 'id' ));
        $vars['links']['sortBySubtype']    = $app['url_generator']->generate('listRecords', array( 'contentTypeAccessHash' => $contentTypeAccessHash, 'page' => 1, 's' => 'subtype' ));
        $vars['links']['sortByName']       = $app['url_generator']->generate('listRecords', array( 'contentTypeAccessHash' => $contentTypeAccessHash, 'page' => 1, 's' => 'name' ));
        $vars['links']['sortByLastChange'] = $app['url_generator']->generate('listRecords', array( 'contentTypeAccessHash' => $contentTypeAccessHash, 'page' => 1, 's' => 'change' ));
        $vars['links']['sortByStatus']     = $app['url_generator']->generate('listRecords', array( 'contentTypeAccessHash' => $contentTypeAccessHash, 'page' => 1, 's' => 'status' ));
        $vars['links']['sortByPosition']   = $app['url_generator']->generate('listRecords', array( 'contentTypeAccessHash' => $contentTypeAccessHash, 'page' => 1, 's' => 'pos' ));
        $vars['links']['search']           = $app['url_generator']->generate('listRecords', array( 'contentTypeAccessHash' => $contentTypeAccessHash, 'page' => 1, 's' => 'name' ));
        $vars['links']['closeSearchBox']   = $app['url_generator']->generate('listRecords', array( 'contentTypeAccessHash' => $contentTypeAccessHash, 'page' => 1, 'q' => '' ));

        // context links
        $vars['links']['timeshift'] = $app['url_generator']->generate('timeShiftListRecords', array( 'contentTypeAccessHash' => $contentTypeAccessHash, 'page' => $page ));
        $vars['links']['workspaces'] = $app['url_generator']->generate('changeWorkspaceListRecords', array( 'contentTypeAccessHash' => $contentTypeAccessHash, 'page' => $page ));
        $vars['links']['languages'] = $app['url_generator']->generate('changeLanguageListRecords', array( 'contentTypeAccessHash' => $contentTypeAccessHash, 'page' => $page ));


        $app['layout']->addCssFile('listing.css');

        $buttons      = array();
        $buttons[100] = array( 'label' => 'List Records', 'url' => $app['url_generator']->generate('listRecordsReset', array( 'contentTypeAccessHash' => $contentTypeAccessHash )), 'glyphicon' => 'glyphicon-list' );
        $buttons[200] = array( 'label' => 'Sort Records', 'url' => $app['url_generator']->generate('sortRecords', array( 'contentTypeAccessHash' => $contentTypeAccessHash )), 'glyphicon' => 'glyphicon-move' );
        $buttons[300] = array( 'label' => 'Add Record', 'url' => $app['url_generator']->generate('addRecord', array( 'contentTypeAccessHash' => $contentTypeAccessHash )), 'glyphicon' => 'glyphicon-plus' );

        $vars['buttons'] = $app['menus']->renderButtonGroup($buttons);

        $count = $repository->countRecords($app['context']->getCurrentWorkspace(), 'default', $app['context']->getCurrentLanguage(), $app['context']->getCurrentSortingOrder(), array(), $itemsPerPage, $page, $filter, $app['context']->getCurrentTimeShift());

        $vars['pager'] = $app['pager']->renderPager($count, $itemsPerPage, $page, 'listRecords', array( 'contentTypeAccessHash' => $contentTypeAccessHash ));

        return $app->renderPage('listing.twig', $vars);

    }
}