<?php

namespace AnyContent\CMCK\Modules\Backend\ConnectionTypes\ContentArchiveConnectionType;

use AnyContent\CMCK\Modules\Backend\Core\Application\Application;

class Module extends \AnyContent\CMCK\Modules\Backend\Core\Core\Module
{

    public function init(Application $app, $options = array())
    {
        parent::init($app, $options);

        $app['repos']->registerConnectionType('contentarchive','AnyContent\CMCK\Modules\Backend\ConnectionTypes\ContentArchiveConnectionType\ContentArchiveConnectionType');


    }

}