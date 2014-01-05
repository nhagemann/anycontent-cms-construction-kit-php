<?php

namespace AnyContent\CMCK\Modules\Core\Sort;

use AnyContent\CMCK\Modules\Core\Application\Application;

class Module extends \AnyContent\CMCK\Modules\Core\Core\Module
{

    public static function init(Application $app)
    {

        $app->addTemplatesFolders(__DIR__ . '/views/');
        $app->get('/content/sort/{contentTypeAccessHash}', 'AnyContent\CMCK\Modules\Core\Sort\Controller::sortRecords')
            ->bind('sortRecords');
        $app->post('/content/sort/{contentTypeAccessHash}', 'AnyContent\CMCK\Modules\Core\Sort\Controller::postSortRecords')
            ->bind('postSortRecords');

    }


    public static function run(Application $app)
    {
        $app['layout']->addJsFile('sort.js');
        $app['layout']->addCssFile('sort.css');
    }
}