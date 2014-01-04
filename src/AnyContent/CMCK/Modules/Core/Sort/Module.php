<?php

namespace AnyContent\CMCK\Modules\Core\Sort;

use AnyContent\CMCK\Modules\Core\Application\Application;

class Module extends \AnyContent\CMCK\Modules\Core\Core\Module
{

    public static function init(Application $app)
    {

        $app->addTemplatesFolders(__DIR__ . '/views/');
        $app->get('/content/sort/{contentTypeAccessHash}', 'AnyContent\CMCK\Modules\Core\Listing\Controller::sortRecords')->bind('sortRecords');

    }

}