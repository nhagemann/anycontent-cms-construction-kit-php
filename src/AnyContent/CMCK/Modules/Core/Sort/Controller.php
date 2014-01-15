<?php

namespace AnyContent\CMCK\Modules\Core\Sort;

use AnyContent\Client\ContentFilter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

use AnyContent\CMCK\Modules\Core\Application\Application;

use AnyContent\Client\Repository;
use AnyContent\Client\Record;
use AnyContent\Client\UserInfo;

class Controller extends \AnyContent\CMCK\Modules\Core\Listing\Controller
{

    public static function sortRecords(Application $app, Request $request, $contentTypeAccessHash)
    {
        $vars = array();

        /** @var Repository $repository */
        $repository = $app['repos']->getRepositoryByContentTypeAccessHash($contentTypeAccessHash);

        $contentTypeDefinition = $repository->getContentTypeDefinition();
        $app['context']->setCurrentContentType($contentTypeDefinition);
        $vars['definition'] = $contentTypeDefinition;

        $vars['menu_mainmenu'] = $app['menus']->renderMainMenu();

        $buttons         = array();
        $buttons[100]    = array( 'label' => 'List Records', 'url' => $app['url_generator']->generate('listRecords', array( 'contentTypeAccessHash' => $contentTypeAccessHash, 'page' => $app['context']->getCurrentListingPage() )), 'glyphicon' => 'glyphicon-list' );
        $vars['buttons'] = $app['menus']->renderButtonGroup($buttons);

        $vars['links']['search'] = $app['url_generator']->generate('listRecords', array( 'contentTypeAccessHash' => $contentTypeAccessHash, 'page' => 1, 's' => 'name' ));

        // context links
        $vars['links']['timeshift']  = $app['url_generator']->generate('timeShiftSortRecords', array( 'contentTypeAccessHash' => $contentTypeAccessHash, 'page' => 1 ));
        $vars['links']['workspaces'] = $app['url_generator']->generate('changeWorkspaceSortRecords', array( 'contentTypeAccessHash' => $contentTypeAccessHash, 'page' => 1 ));
        $vars['links']['languages']  = $app['url_generator']->generate('changeLanguageSortRecords', array( 'contentTypeAccessHash' => $contentTypeAccessHash, 'page' => 1 ));

        $vars['links']['sort'] = $app['url_generator']->generate('postSortRecords', array( 'contentTypeAccessHash' => $contentTypeAccessHash ));

        $records       = self::getRecords($app, $repository, $contentTypeAccessHash, 'pos');
        $records_left  = array();
        $records_right = array();

        /** @var Record $record */
        foreach ($records as $record)
        {

            if ($record['record']->getParentRecordID() !== null)
            {
                $records_left[] = $record;
            }
            else
            {
                $records_right[] = $record;
            }
        }

        $vars['records_left']  = $records_left;
        $vars['records_right'] = $records_right;

        return $app->renderPage('sort-tree.twig', $vars);
    }


    public static function postSortRecords(Application $app, Request $request, $contentTypeAccessHash)
    {
        /** @var Repository $repository */
        $repository = $app['repos']->getRepositoryByContentTypeAccessHash($contentTypeAccessHash);

        $contentTypeDefinition = $repository->getContentTypeDefinition();
        $app['context']->setCurrentContentType($contentTypeDefinition);

        if ($request->request->has('list'))
        {
            $app['context']->resetTimeShift();
            $list = json_decode($request->get('list'), true);

            $result = $repository->sortRecords($list, $app['context']->getCurrentWorkspace(), $app['context']->getCurrentLanguage());

            return new RedirectResponse($app['url_generator']->generate('sortRecords', array( 'contentTypeAccessHash' => $contentTypeAccessHash )), 303);
        }
        else
        {
            return $app['layout']->render('error.twig');
        }

    }

}