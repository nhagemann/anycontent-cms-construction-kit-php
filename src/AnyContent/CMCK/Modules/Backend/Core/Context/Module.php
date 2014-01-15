<?php

namespace AnyContent\CMCK\Modules\Backend\Core\Context;

use AnyContent\CMCK\Modules\Backend\Core\Application\Application;

class Module extends \AnyContent\CMCK\Modules\Backend\Core\Core\Module
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