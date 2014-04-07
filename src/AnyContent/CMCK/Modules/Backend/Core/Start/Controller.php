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

class Controller
{

    public static function index(Application $app)
    {
        $vars = array();

        $vars['menu_mainmenu'] = $app['menus']->renderMainMenu();

        return $app->renderPage('start.twig', $vars);
    }
}