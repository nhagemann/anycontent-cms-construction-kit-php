<?php

namespace AnyContent\CMCK\Modules\Core\Listing;

use AnyContent\CMCK\Modules\Core\Application\Application;

class Module extends \AnyContent\CMCK\Modules\Core\Core\Module
{

    public static function init(Application $app)
    {

        $app->addTemplatesFolders(__DIR__ . '/views/');
        $app->get('/content/list/{contentTypeAccessHash}', 'AnyContent\CMCK\Modules\Core\Listing\Controller::listRecords')->bind('listRecordsReset');
        $app->get('/content/list/{contentTypeAccessHash}/page/{page}', 'AnyContent\CMCK\Modules\Core\Listing\Controller::listRecords')->bind('listRecords');

    }

}