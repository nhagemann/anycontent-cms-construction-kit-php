<?php

namespace AnyContent\CMCK\Modules\Core\Context;

use AnyContent\CMCK\Modules\Core\Application\Application;

class Module extends \AnyContent\CMCK\Modules\Core\Core\Module
{

    public static function init(Application $app)
    {
        $app['context'] = $app->share(function ($app)
        {
            return new ContextManager($app['session']);
        });
    }


    public static function run(Application $app)
    {

    }
}