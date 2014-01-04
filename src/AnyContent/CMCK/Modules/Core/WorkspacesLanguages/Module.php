<?php

namespace Anycontent\CMCK\Modules\Core\WorkspacesLanguages;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use AnyContent\CMCK\Modules\Core\Application\Application;

use CMDL\ContentTypeDefinition;

class Module extends \AnyContent\CMCK\Modules\Core\Core\Module
{

    public static function init(Application $app)
    {

        $app
            ->post('/change-workspace/content/list/{contentTypeAccessHash}/page/{page}', 'AnyContent\CMCK\Modules\Core\WorkspacesLanguages\Controller::changeWorkspaceListRecords')
            ->bind('changeWorkspaceListRecords');
        $app
            ->post('/change-workspace/content/edit/{contentTypeAccessHash}/{recordId}', 'AnyContent\CMCK\Modules\Core\WorkspacesLanguages\Controller::changeWorkspaceEditRecord')
            ->bind('changeWorkspaceEditRecord');
        $app
            ->post('/change-workspace/content/add/{contentTypeAccessHash}', 'AnyContent\CMCK\Modules\Core\WorkspacesLanguages\Controller::changeWorkspaceAddRecord')
            ->bind('changeWorkspaceAddRecord');

        $app
            ->post('/change-language/content/list/{contentTypeAccessHash}/page/{page}', 'AnyContent\CMCK\Modules\Core\WorkspacesLanguages\Controller::changeLanguageListRecords')
            ->bind('changeLanguageListRecords');
        $app
            ->post('/change-language/content/edit/{contentTypeAccessHash}/{recordId}', 'AnyContent\CMCK\Modules\Core\WorkspacesLanguages\Controller::changeLanguageEditRecord')
            ->bind('changeLanguageEditRecord');
        $app
            ->post('/change-language/content/add/{contentTypeAccessHash}', 'AnyContent\CMCK\Modules\Core\WorkspacesLanguages\Controller::changeLanguageAddRecord')
            ->bind('changeLanguageAddRecord');

    }


    public static function preRender(Application $app)
    {
        $workspaces = $app['layout']->getVar('workspaces', array());

        /** @var ContentTypeDefinition $contentType */
        $contentTypeDefinition = $app['context']->getCurrentContentType();

        if ($contentTypeDefinition)
        {
            $workspaces['active'] = true;
            $workspaces['list']   = $contentTypeDefinition->getWorkspaces();

            if (count($contentTypeDefinition->getWorkspaces())<2)
            {
                $workspaces['active']=false;
            }
        }
        else
        {
            $workspaces['active'] = false;
        }

        $workspaces['current'] = $app['context']->getCurrentWorkspace();

        $app['layout']->addVar('workspaces', $workspaces);

        // do the same for current language

        $languages = $app['layout']->getVar('languages', array());

        /** @var ContentTypeDefinition $contentType */
        $contentTypeDefinition = $app['context']->getCurrentContentType();

        if ($contentTypeDefinition)
        {
            $languages['active'] = true;
            $languages['list']   = $contentTypeDefinition->getLanguages();

            if (!$contentTypeDefinition->hasLanguages())
            {
                $languages['active']=false;
            }
        }
        else
        {
            $languages['active'] = false;
        }

        $languages['current'] = $app['context']->getCurrentLanguage();

        $app['layout']->addVar('languages', $languages);
    }
}