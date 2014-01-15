<?php

namespace AnyContent\CMCK\Modules\Core\Sort;

use AnyContent\CMCK\Modules\Core\Application\Application;

class Module extends \AnyContent\CMCK\Modules\Core\Core\Module
{

    public function init(Application $app, $options = array())
    {
        parent::init($app, $options);

        $app->addTemplatesFolders(__DIR__ . '/views/');
        $app->get('/content/sort/{contentTypeAccessHash}', 'AnyContent\CMCK\Modules\Core\Sort\Controller::sortRecords')
            ->bind('sortRecords');
        $app->post('/content/sort/{contentTypeAccessHash}', 'AnyContent\CMCK\Modules\Core\Sort\Controller::postSortRecords')
            ->bind('postSortRecords');

    }


    public function run(Application $app)
    {
        $app['layout']->addJsFile('sort.js');
        $app['layout']->addCssFile('sort.css');
    }
}