<?php

namespace Anycontent\CMCK\Modules\Core\Pager;

use AnyContent\CMCK\Modules\Core\Application\Application;
use AnyContent\CMCK\Modules\Core\Pager\PagingHelper;

class Module extends \AnyContent\CMCK\Modules\Core\Core\Module
{

    public static function init(Application $app)
    {
        $app->addTemplatesFolders(__DIR__ . '/views/');

        $app['pager'] = $app->share(function ($app)
        {
            return new PagingHelper($app['twig'], $app['layout'], $app['url_generator']);
        });

    }

}