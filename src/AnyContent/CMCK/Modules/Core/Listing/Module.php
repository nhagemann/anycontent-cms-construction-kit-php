<?php

namespace AnyContent\CMCK\Modules\Core\Listing;

use AnyContent\CMCK\Application\Application;

class Module
{

    public static function init(Application $app)
    {

        $app->addTemplatesFolders(__DIR__ . '/views/');
        $app->get('/content/list/{contentTypeName}', 'AnyContent\CMCK\Modules\Core\Listing\Controller::listRecords');

    }

}