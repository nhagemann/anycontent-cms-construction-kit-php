<?php

namespace AnyContent\CMCK\Modules\Core\Listing;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use AnyContent\CMCK\Application\Application;

use AnyContent\Client\Repository;
use AnyContent\Client\Record;

class Controller
{


    public static function listRecords(Application $app, $contentTypeAccessHash)
    {

        $vars = array();

        $vars['menu_mainmenu']=$app['menus']->renderMainMenu();

        /** @var Repository $repository */
        $repository = $app['repos']->getRepositoryContentAccessByHash($contentTypeAccessHash);

        $records = array();

        /** @var Record $record */
        foreach ($repository->getRecords($app['context']->getCurrentWorkspace(),'default',$app['context']->getCurrentLanguage(),'id',array(),10,1,$app['context']->getCurrentTimeShift()) AS $record)
        {
            $item = array();
            $item['record']=$record;
            $item['name'] = $record->getName();
            $item['id']=$record->getID();
            $item['editUrl']=$app['url_generator']->generate('editRecord',array('contentTypeAccessHash'=>$contentTypeAccessHash,'recordId'=>$record->getID()));
            $records[] = $item;
        }

        $vars['records'] = $records;

        $app['layout']->addCssFile('curvedtables.css');


        $buttons   = array();
        $buttons[100] = array( 'label' => 'List Records', 'url' => $app['url_generator']->generate('listRecords', array( 'contentTypeAccessHash' => $contentTypeAccessHash )), 'glyphicon' => 'glyphicon-list' );
        $buttons[200] = array( 'label' => 'Sort Records', 'url' => $app['url_generator']->generate('sortRecords', array( 'contentTypeAccessHash' => $contentTypeAccessHash )), 'glyphicon' => 'glyphicon-move' );
        $buttons[300] = array( 'label' => 'Add Record', 'url' => $app['url_generator']->generate('addRecord', array( 'contentTypeAccessHash' => $contentTypeAccessHash )), 'glyphicon' => 'glyphicon-plus' );

        $vars['buttons'] = $app['menus']->renderButtonGroup($buttons);

        return $app['layout']->render('content-list.twig',$vars);


    }
}