<?php

namespace AnyContent\CMCK\Modules\Backend\Core\Repositories;

use AnyContent\CMCK\Modules\Backend\Core\Application\Application;

use AnyContent\CMCK\Modules\Backend\Core\Repositories\ConnectionTypes\RestLikeConnectionType;
use AnyContent\CMCK\Modules\Backend\Core\Repositories\ListRepositoriesCommand;

class Module extends \AnyContent\CMCK\Modules\Backend\Core\Core\Module
{

    public function init(Application $app, $options = array())
    {
        parent::init($app, $options);

        $app['repos'] = $app->share(function ($app)
        {
            $repositoryManager = new RepositoryManager($app);
            $repositoryManager->registerConnectionType('restlike','AnyContent\CMCK\Modules\Backend\Core\Repositories\ConnectionTypes\RestLikeConnectionType');
            return $repositoryManager;
        });

        $app['console']->add(new ListRepositoriesCommand());

    }

}