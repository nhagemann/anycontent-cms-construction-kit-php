<?php

namespace Anycontent\CMCK\Modules\Core\Init;

use AnyContent\CMCK\Modules\Core\Application\Application;

class Module extends \AnyContent\CMCK\Modules\Core\Core\Module
{

    public static function init(Application $app)
    {

        $app->register(new \Silex\Provider\UrlGeneratorServiceProvider());
        $app->register(new \Silex\Provider\SessionServiceProvider());
    }


}