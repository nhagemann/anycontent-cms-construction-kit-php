<?php

namespace AnyContent\CMCK\Modules\Core\Files;

use AnyContent\Client\ContentFilter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

use AnyContent\CMCK\Modules\Core\Application\Application;

use AnyContent\Client\Repository;
use AnyContent\Client\Folder;
use AnyContent\Client\File;
use AnyContent\Client\UserInfo;

class Controller
{

    public static function listFiles(Application $app, Request $request, $repositoryAccessHash, $path = '')
    {
        $app['layout']->addCssFile('files');

        $vars                   = array();
        $vars['root']           = false;
        $vars['links']['files'] = $app['url_generator']->generate('listFiles', array( 'repositoryAccessHash' => $repositoryAccessHash, 'path' => '' ));

        /** @var Repository $repository */
        $repository = $app['repos']->getRepositoryByRepositoryAccessHash($repositoryAccessHash);

        if ($repository)
        {
            $path = '/' . trim($path, '/');

            $breadcrumbs = explode('/', $path);

            if ($path == '/')
            {
                $breadcrumbs  = array( '/' );
                $vars['root'] = true;
            }

            $folders  = array();
            $nextPath = '';
            foreach ($breadcrumbs as $subPath)
            {

                $nextPath .= '/' . $subPath;
                $folder = $repository->getFolder($nextPath);

                if ($folder)
                {
                    $items = array();
                    foreach ($folder->listSubFolders() as $id => $name)
                    {
                        $id   = trim($id, '/');
                        $item = array( 'name' => $name, 'class' => '', 'url' => $app['url_generator']->generate('listFiles', array( 'repositoryAccessHash' => $repositoryAccessHash, 'path' => $id )) );
                        if (strstr($path, $id))
                        {
                            $item['class'] = 'active';
                        }
                        $items[] = $item;
                    }
                    $folders[] = $items;

                    $files = $folder->getFiles();

                }
                else
                {
                    return new RedirectResponse($vars['links']['files'],303);
                }

            }

            $vars['folders'] = $folders;
            $vars['files']   = $files;

        }

        $vars['menu_mainmenu'] = $app['menus']->renderMainMenu();

        $buttons = array();

        $buttons      = array();
        $buttons[100] = array( 'label' => 'Upload File', 'url' => '', 'glyphicon' => 'glyphicon-cloud-upload' );
        $buttons[200] = array( 'label' => 'Create Folder', 'url' => '', 'glyphicon' => 'glyphicon-folder-open' );
        $buttons[300] = array( 'label' => 'Delete Folder', 'url' => '', 'glyphicon' => 'glyphicon-trash' );

        $vars['buttons'] = $app['menus']->renderButtonGroup($buttons);

        return $app->renderPage('files.twig', $vars);
    }
}