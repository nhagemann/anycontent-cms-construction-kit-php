<?php

namespace AnyContent\CMCK\Modules\Core\Context;

use AnyContent\CMCK\Modules\Core\Application\Application;

class Module extends \AnyContent\CMCK\Modules\Core\Core\Module
{


    public function init(Application $app, $options = array())
    {
        parent::init($app, $options);

        $app['context'] = $app->share(function ($app)
        {
            return new ContextManager($app['session']);
        });
    }


    public function run(Application $app)
    {

    }
}