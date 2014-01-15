<?php

namespace AnyContent\CMCK\Modules\Backend\Core\Sort;

use AnyContent\CMCK\Modules\Backend\Core\Application\Application;

class Module extends \AnyContent\CMCK\Modules\Backend\Core\Core\Module
{

    public function init(Application $app, $options = array())
    {
        parent::init($app, $options);

        $app->addTemplatesFolders(__DIR__ . '/views/');
        $app->get('/content/sort/{contentTypeAccessHash}', 'AnyContent\CMCK\Modules\Backend\Core\Sort\Controller::sortRecords')
            ->bind('sortRecords');
        $app->post('/content/sort/{contentTypeAccessHash}', 'AnyContent\CMCK\Modules\Backend\Core\Sort\Controller::postSortRecords')
            ->bind('postSortRecords');

    }


    public function run(Application $app)
    {
        $app['layout']->addJsFile('sort.js');
        $app['layout']->addCssFile('sort.css');
    }
}