<?php

namespace AnyContent\CMCK\Modules\Core\Listing;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use AnyContent\CMCK\Modules\Core\Application\Application;

use AnyContent\Client\Repository;
use AnyContent\Client\Record;
use AnyContent\Client\UserInfo;

class Controller
{

    public static function listRecords(Application $app, $contentTypeAccessHash, $page)
    {
        // reset chained save operations to 'save' only upon listing of a content type
        if (key($app['context']->getCurrentSaveOperation()) != 'save-list')
        {
            $app['context']->setCurrentSaveOperation('save', 'Save');
        }

        $itemsPerPage = 10;

        $vars = array();

        $vars['menu_mainmenu'] = $app['menus']->renderMainMenu();

        /** @var Repository $repository */
        $repository = $app['repos']->getRepositoryContentAccessByHash($contentTypeAccessHash);

        $definition         = $repository->getContentTypeDefinition();
        $vars['definition'] = $definition;

        $records = array();

        /** @var Record $record */
        foreach ($repository->getRecords($app['context']->getCurrentWorkspace(), 'default', $app['context']->getCurrentLanguage(), 'id', array(), $itemsPerPage, $page, $app['context']->getCurrentTimeShift()) AS $record)
        {
            $item                     = array();
            $item['record']           = $record;
            $item['name']             = $record->getName();
            $item['id']               = $record->getID();
            $item['editUrl']          = $app['url_generator']->generate('editRecord', array( 'contentTypeAccessHash' => $contentTypeAccessHash, 'recordId' => $record->getID() ));
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

        $app['layout']->addCssFile('listing.css');

        $buttons      = array();
        $buttons[100] = array( 'label' => 'List Records', 'url' => $app['url_generator']->generate('listRecords', array( 'contentTypeAccessHash' => $contentTypeAccessHash, 'page' => 1 )), 'glyphicon' => 'glyphicon-list' );
        $buttons[200] = array( 'label' => 'Sort Records', 'url' => $app['url_generator']->generate('sortRecords', array( 'contentTypeAccessHash' => $contentTypeAccessHash )), 'glyphicon' => 'glyphicon-move' );
        $buttons[300] = array( 'label' => 'Add Record', 'url' => $app['url_generator']->generate('addRecord', array( 'contentTypeAccessHash' => $contentTypeAccessHash )), 'glyphicon' => 'glyphicon-plus' );

        $vars['buttons'] = $app['menus']->renderButtonGroup($buttons);

        $count         = $repository->getRecordsCount($app['context']->getCurrentWorkspace(), $app['context']->getCurrentLanguage(), $app['context']->getCurrentTimeShift());
        $vars['pager'] = $app['pager']->renderPager($count, $itemsPerPage, $page, 'listRecords', array( 'contentTypeAccessHash' => $contentTypeAccessHash ));

        return $app['layout']->render('listing.twig', $vars);

    }
}