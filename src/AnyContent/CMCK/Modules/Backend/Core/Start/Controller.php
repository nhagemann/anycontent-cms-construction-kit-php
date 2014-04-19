<?php

namespace AnyContent\CMCK\Modules\Backend\Core\Start;

use AnyContent\CMCK\Modules\Backend\Core\Application\Application;

use CMDL\ContentTypeDefinition;
use CMDL\ViewDefinition;

use AnyContent\Client\Repository;
use AnyContent\Client\Record;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

use AnyContent\CMCK\Modules\Backend\Core\Repositories\RepositoryManager;

class Controller
{

    public static function index(Application $app)
    {
        $app['layout']->addCssFile('listing.css');

        $vars = array();

        $vars['menu_mainmenu'] = $app['menus']->renderMainMenu();

        /** @var RepositoryManager $repositoryManager */
        $repositoryManager = $app['repos'];

        $items = array();
        foreach ($repositoryManager->listRepositories() as $repositoryUrl => $repositoryItem)
        {

            $items[] = self::extractRepositoryInfos($app, $repositoryUrl, $repositoryItem, false);

        }

        $vars['repositories'] = $items;

        return $app->renderPage('index.twig', $vars);
    }


    public static function indexRepository(Application $app, $repositoryAccessHash)
    {
        $app['layout']->addCssFile('listing.css');

        $vars = array();

        $vars['menu_mainmenu'] = $app['menus']->renderMainMenu();

        /** @var RepositoryManager $repositoryManager */
        $repositoryManager = $app['repos'];

        foreach ($repositoryManager->listRepositories() as $repositoryUrl => $repositoryItem)
        {
            if ($repositoryAccessHash == $repositoryItem['accessHash'])
            {
                $item               = self::extractRepositoryInfos($app, $repositoryUrl, $repositoryItem, true);
                $vars['repository'] = $item;
            }
        }

        // Now add additional info about workspaces and languages
        $repository = $repositoryManager->getRepositoryByRepositoryAccessHash($repositoryAccessHash);

        //$items = $item['content_types'];
        //$item['content_types'] = array();
        /* foreach ($item['content_types'] as &$contentTypeItem)
         {
             $definition = $repository->getContentTypeDefinition($contentTypeItem['name']);
             $contentTypeItem['name']='horst';
             var_dump ($definition->getWorkspaces());
             //$item['content_types'] = $contentTypeItem;
         }*/

//        $item          = array();
//        $item['title'] = 'xxx';
//        $item['url']   = 'xxx';
//        $item['link']  = $app['url_generator']->generate('indexRepository', array( 'repositoryAccessHash' => $repositoryAccessHash ));
//        $item['files'] = false;
//
//        $item['content_types'] = array();
//
//        foreach ($repository->getContentTypes() as $contentTypeName)
//        {
//            $definition = $repository->getContentTypeDefinition($contentTypeName);
//            $url                     = $app['url_generator']->generate('listRecords', array( 'contentTypeAccessHash' => $contentTypeItem['accessHash'], 'page' => 1 ));
//            $item['content_types'][] = array( 'name' => $contentTypeName, 'link' => $url );
//        }
//        $item['config_types'] = array();
//

        return $app->renderPage('index-repository.twig', $vars);
    }


    protected static function extractRepositoryInfos($app, $repositoryUrl, $repositoryItem, $definition = false)
    {
        /** @var RepositoryManager $repositoryManager */
        $repositoryManager = $app['repos'];

        if ($definition)
        {
            $repository = $repositoryManager->getRepositoryByRepositoryAccessHash($repositoryItem['accessHash']);
        }

        $item          = array();
        $item['title'] = $repositoryUrl;
        $item['url']   = $repositoryUrl;
        $item['link']  = $app['url_generator']->generate('indexRepository', array( 'repositoryAccessHash' => $repositoryItem['accessHash'] ));
        $item['files'] = false;

        $item['content_types'] = array();

        foreach ($repositoryManager->listContentTypes($repositoryUrl) as $contentTypeName => $contentTypeItem)
        {
            $info = array( 'name' => $contentTypeName, 'link' => $app['url_generator']->generate('listRecords', array( 'contentTypeAccessHash' => $contentTypeItem['accessHash'], 'page' => 1 )) );

            if ($definition)
            {
                $info['definition'] = $repository->getContentTypeDefinition($contentTypeName);
            }

            $item['content_types'][] = $info;
        }

        $item['config_types'] = array();

        foreach ($repositoryManager->listConfigTypes($repositoryUrl) as $configTypeName => $configTypeItem)
        {
            $info = array( 'name' => $configTypeName, 'link' => $app['url_generator']->generate('editConfig', array( 'configTypeAccessHash' => $configTypeItem['accessHash'] )) );

            if ($definition)
            {
                $info['definition'] = $repository->getConfigTypeDefinition($configTypeName);
            }

            $item['config_types'][] = $info;
        }

        if ($repositoryManager->hasFiles($repositoryUrl))
        {
            $item['files'] = $app['url_generator']->generate('listFiles', array( 'repositoryAccessHash' => $repositoryItem['accessHash'], 'path' => '' ));
        }

        return $item;

    }

}