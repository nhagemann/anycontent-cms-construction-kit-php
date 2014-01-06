<?php

namespace Anycontent\CMCK\Modules\Core\Menu;

use AnyContent\CMCK\Modules\Core\Application\Application;
use AnyContent\CMCK\Modules\Core\Menu\MenuManager;

class Module extends \AnyContent\CMCK\Modules\Core\Core\Module
{

    public static function init(Application $app)
    {

        $app->addTemplatesFolders(__DIR__ . '/views/');

        $app['menus'] = $app->share(function ($app)
        {
            return new MenuManager($app['repos'],$app['twig'],$app['layout'],$app['url_generator']);
        });

    }

}