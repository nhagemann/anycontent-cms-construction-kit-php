<?php

namespace Anycontent\CMCK\Modules\Core\Repositories;

use AnyContent\CMCK\Modules\Core\Application\Application;
use AnyContent\CMCK\Modules\Core\Repositories\RepositoryManager;

class Module extends \AnyContent\CMCK\Modules\Core\Core\Module
{

    public function init(Application $app, $options = array())
    {
        parent::init($app, $options);

        $app['repos'] = $app->share(function ($app)
        {
            return new RepositoryManager($app['cache']);
        });

    }

}