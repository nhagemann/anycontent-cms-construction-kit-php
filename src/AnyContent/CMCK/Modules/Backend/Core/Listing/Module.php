<?php

namespace AnyContent\CMCK\Modules\Backend\Core\Listing;

use AnyContent\CMCK\Modules\Backend\Core\Application\Application;

class Module extends \AnyContent\CMCK\Modules\Backend\Core\Core\Module
{

    public function init(Application $app, $options = array())
    {
        parent::init($app, $options);

        $app->addTemplatesFolders(__DIR__ . '/views/');
        $app->get('/content/list/{contentTypeAccessHash}', 'AnyContent\CMCK\Modules\Backend\Core\Listing\Controller::listRecords')->bind('listRecordsReset');
        $app->get('/content/list/{contentTypeAccessHash}/page/{page}', 'AnyContent\CMCK\Modules\Backend\Core\Listing\Controller::listRecords')->bind('listRecords');

    }

}