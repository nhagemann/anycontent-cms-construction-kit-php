<?php

namespace AnyContent\CMCK\Modules\Backend\Admin\CMDL;

use AnyContent\Client\ContentFilter;
use CMDL\Parser;
use CMDL\Util;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

use AnyContent\CMCK\Modules\Backend\Core\Application\Application;

use AnyContent\Client\Repository;
use AnyContent\Client\Record;
use AnyContent\Client\UserInfo;

use CMDL\ContentTypeDefinition;

class Controller
{

    public static function adminList(Application $app)
    {
        $app['layout']->addCssFile('listing.css');
        $app['layout']->addJsFile('app.js');
        $app['layout']->addJsFile('admin.js');

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

        return $app->renderPage('admin.twig', $vars);
    }


    public static function adminEditContentType(Application $app, $contentTypeAccessHash)
    {
        $vars                    = array();
        $vars['menu_mainmenu']   = $app['menus']->renderMainMenu();
        $vars['links']['home']   = $app['url_generator']->generate('index');
        $vars['links']['admin']  = $app['url_generator']->generate('adminList');
        $vars['links']['delete'] = $app['url_generator']->generate('adminDeleteContentType', array( 'contentTypeAccessHash' => $contentTypeAccessHash ));

        /** @var Repository $repository */
        $repository = $app['repos']->getRepositoryByContentTypeAccessHash($contentTypeAccessHash);

        if ($repository)
        {
            $app['layout']->addJsFile('app.js');
            $app['layout']->addJsFile('admin.js');

            $vars['record'] = false;

            /** @var ContentTypeDefinition $contentTypeDefinition */
            $contentTypeDefinition = $repository->getContentTypeDefinition();

            $cmdl = $contentTypeDefinition->getCMDL();

            $vars['definition'] = $contentTypeDefinition;
            $vars['cmdl']       = $cmdl;
            $vars['data_type']  = 'content';

            return $app->renderPage('admin-data-type.twig', $vars);

        }
    }


    public static function adminEditConfigType(Application $app, $configTypeAccessHash)
    {
        $vars                    = array();
        $vars['menu_mainmenu']   = $app['menus']->renderMainMenu();
        $vars['links']['home']   = $app['url_generator']->generate('index');
        $vars['links']['admin']  = $app['url_generator']->generate('adminList');
        $vars['links']['delete'] = $app['url_generator']->generate('adminDeleteConfigType', array( 'configTypeAccessHash' => $configTypeAccessHash ));

        /** @var Repository $repository */
        $repository = $app['repos']->getRepositoryByConfigTypeAccessHash($configTypeAccessHash);

        if ($repository)
        {
            $app['layout']->addJsFile('app.js');
            $app['layout']->addJsFile('admin.js');

            $vars['record'] = false;

            $configTypeDefinition = $repository->getConfigTypeDefinition();

            $cmdl = $configTypeDefinition->getCMDL();

            $vars['definition'] = $configTypeDefinition;
            $vars['cmdl']       = $cmdl;
            $vars['data_type']  = 'config';

            return $app->renderPage('admin-data-type.twig', $vars);

        }
    }


    public static function postEditContentType(Application $app, Request $request, $contentTypeAccessHash)
    {
        /** @var Repository $repository */
        $repository = $app['repos']->getRepositoryByContentTypeAccessHash($contentTypeAccessHash);

        $response            = array();
        $response['success'] = false;

        if ($repository)
        {

            $cmdl = $request->get('cmdl');

            try
            {
                /** @var ContentTypeDefinition $contentTypeDefinition */
                $contentTypeDefinition = Parser::parseCMDLString($cmdl);
            }
            catch (\Exception $e)
            {
                $response['message'] = $e->getMessage();
            }

            try
            {
                /** @var ContentTypeDefinition $contentTypeDefinition */
                $contentTypeDefinition = $repository->getContentTypeDefinition();

                $client = $repository->getClient();

                if ($client->saveContentTypeCMDL($contentTypeDefinition->getName(), $cmdl))
                {
                    $response['success'] = true;
                }
            }
            catch (\Exception $e)
            {

            }

        }

        return new JsonResponse($response);
    }


    public static function postEditConfigType(Application $app, Request $request, $configTypeAccessHash)
    {
        /** @var Repository $repository */
        $repository = $app['repos']->getRepositoryByConfigTypeAccessHash($configTypeAccessHash);

        $response            = array();
        $response['success'] = false;

        if ($repository)
        {

            $cmdl = $request->get('cmdl');

            try
            {
                /** @var ConfigTypeDefinition $contentTypeDefinition */
                $configTypeDefinition = Parser::parseCMDLString($cmdl, null, null, 'config');
            }
            catch (\Exception $e)
            {
                $response['message'] = $e->getMessage();
            }

            try
            {
                /** @var ConfigTypeDefinition $contentTypeDefinition */
                $configTypeDefinition = $repository->getConfigTypeDefinition();

                $client = $repository->getClient();

                if ($client->saveConfigTypeCMDL($configTypeDefinition->getName(), $cmdl))
                {
                    $response['success'] = true;
                }
            }
            catch (\Exception $e)
            {

            }

        }

        return new JsonResponse($response);
    }


    public static function adminAddContentType(Application $app, Request $request, $repositoryAccessHash)
    {
        $url = $app['url_generator']->generate('adminList');

        /** @var RepositoryManager $repositoryManager */
        $repositoryManager = $app['repos'];

        /** @var Repository $repository */
        $repository = $repositoryManager->getRepositoryByRepositoryAccessHash($repositoryAccessHash);

        if ($repository)
        {
            $contentTypeName = trim($request->request->get('create_content_type'));

            $contentTypeName = Util::generateValidIdentifier($contentTypeName);

            if ($contentTypeName != '')
            {
                if (!$repository->hasContentType($contentTypeName))
                {
                    $client = $repository->getClient();

                    if ($client->saveContentTypeCMDL($contentTypeName, '### definition of content type ' . $contentTypeName . ' ###' . PHP_EOL . PHP_EOL . 'Name'))
                    {
                        $app['context']->addSuccessMessage('Content Type ' . $contentTypeName . ' created.');

                        return new RedirectResponse($url);
                    }
                }
                else
                {
                    $app['context']->addAlertMessage('Content Type ' . $contentTypeName . ' already exists.');
                }
            }

        }

        $app['context']->addErrorMessage('Error generating new content type.');

        return new RedirectResponse($url);

    }


    public static function adminAddConfigType(Application $app, Request $request, $repositoryAccessHash)
    {
        $url = $app['url_generator']->generate('adminList');

        /** @var RepositoryManager $repositoryManager */
        $repositoryManager = $app['repos'];

        /** @var Repository $repository */
        $repository = $repositoryManager->getRepositoryByRepositoryAccessHash($repositoryAccessHash);

        if ($repository)
        {
            $configTypeName = trim($request->request->get('create_config_type'));

            $configTypeName = Util::generateValidIdentifier($configTypeName);

            if ($configTypeName != '')
            {
                if (!$repository->hasConfigType($configTypeName))
                {
                    $client = $repository->getClient();

                    if ($client->saveConfigTypeCMDL($configTypeName, '### definition of config type ' . $configTypeName . ' ###' . PHP_EOL))
                    {
                        $app['context']->addSuccessMessage('Config Type ' . $configTypeName . ' created.');

                        return new RedirectResponse($url);
                    }
                }
                else
                {
                    $app['context']->addAlertMessage('Config Type ' . $configTypeName . ' already exists.');
                }
            }

        }

        $app['context']->addErrorMessage('Error generating new config type.');

        return new RedirectResponse($url);

    }


    public static function adminDeleteContentType(Application $app, Request $request, $contentTypeAccessHash)
    {
        $url = $app['url_generator']->generate('adminList');

        /** @var Repository $repository */
        $repository = $app['repos']->getRepositoryByContentTypeAccessHash($contentTypeAccessHash);

        if ($repository)
        {
            /** @var ContentTypeDefinition $contentTypeDefinition */
            $contentTypeDefinition = $repository->getContentTypeDefinition();

            $contentTypeName = $contentTypeDefinition->getName();

            $client = $repository->getClient();

            if ($client->deleteContentType($contentTypeName))
            {
                $app['context']->addSuccessMessage('Content Type ' . $contentTypeName . ' deleted.');

                return new RedirectResponse($url);
            }

        }

        $app['context']->addErrorMessage('Error deleting content type.');

        return new RedirectResponse($url);
    }


    public static function adminDeleteConfigType(Application $app, Request $request, $configTypeAccessHash)
    {
        $url = $app['url_generator']->generate('adminList');

        /** @var Repository $repository */
        $repository = $app['repos']->getRepositoryByConfigTypeAccessHash($configTypeAccessHash);

        if ($repository)
        {
            /** @var ConfigTypeDefinition $configTypeDefinition */
            $configTypeDefinition = $repository->getConfigTypeDefinition();

            $configTypeName = $configTypeDefinition->getName();

            $client = $repository->getClient();

            if ($client->deleteConfigType($configTypeName))
            {
                $app['context']->addSuccessMessage('Config Type ' . $configTypeName . ' deleted.');

                return new RedirectResponse($url);
            }

        }

        $app['context']->addErrorMessage('Error deleting config type.');

        return new RedirectResponse($url);
    }


    protected static function extractRepositoryInfos($app, $repositoryUrl, $repositoryItem, $definition = false)
    {
        /** @var RepositoryManager $repositoryManager */
        $repositoryManager = $app['repos'];

        if ($definition)
        {
            $repository = $repositoryManager->getRepositoryByRepositoryAccessHash($repositoryItem['accessHash']);
        }

        $item                              = array();
        $item['title']                     = $repositoryUrl;
        $item['url']                       = $repositoryUrl;
        $item['link']                      = $app['url_generator']->generate('indexRepository', array( 'repositoryAccessHash' => $repositoryItem['accessHash'] ));
        $item['links']['add_content_type'] = $app['url_generator']->generate('adminAddContentType', array( 'repositoryAccessHash' => $repositoryItem['accessHash'] ));
        $item['links']['add_config_type']  = $app['url_generator']->generate('adminAddConfigType', array( 'repositoryAccessHash' => $repositoryItem['accessHash'] ));

        $item['content_types'] = array();

        foreach ($repositoryManager->listContentTypes($repositoryUrl) as $contentTypeName => $contentTypeItem)
        {
            $info = array( 'name' => $contentTypeName, 'edit' => $app['url_generator']->generate('adminEditContentType', array( 'contentTypeAccessHash' => $contentTypeItem['accessHash'] )), 'delete' => $app['url_generator']->generate('adminDeleteContentType', array( 'contentTypeAccessHash' => $contentTypeItem['accessHash'] )) );

            if ($definition)
            {
                $info['definition'] = $repository->getContentTypeDefinition($contentTypeName);
            }

            $item['content_types'][] = $info;
        }

        $item['config_types'] = array();

        foreach ($repositoryManager->listConfigTypes($repositoryUrl) as $configTypeName => $configTypeItem)
        {
            $info = array( 'name' => $configTypeName, 'edit' => $app['url_generator']->generate('adminEditConfigType', array( 'configTypeAccessHash' => $configTypeItem['accessHash'] )), 'delete' => $app['url_generator']->generate('adminDeleteConfigType', array( 'configTypeAccessHash' => $configTypeItem['accessHash'] )) );

            if ($definition)
            {
                $info['definition'] = $repository->getConfigTypeDefinition($configTypeName);
            }

            $item['config_types'][] = $info;
        }

        return $item;

    }
}