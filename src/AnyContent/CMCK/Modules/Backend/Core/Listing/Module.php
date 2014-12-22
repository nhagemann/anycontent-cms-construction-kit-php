<?php

namespace AnyContent\CMCK\Modules\Backend\Core\Listing;

use AnyContent\CMCK\Modules\Backend\Core\Application\Application;

class Module extends \AnyContent\CMCK\Modules\Backend\Core\Core\Module
{
    const EVENT_LISTING_RENDER = 'event.listing.render';

    public function init(Application $app, $options = array())
    {
        parent::init($app, $options);

        $app->addTemplatesFolders(__DIR__ . '/views/');
        $app->get('/content/list/{contentTypeAccessHash}/page/{page}/{workspace}/{language}', 'AnyContent\CMCK\Modules\Backend\Core\Listing\Controller::listRecords')->bind('listRecords')->value('workspace',null)->value('language',null);
        $app->get('/content/list/{contentTypeAccessHash}/{workspace}/{language}', 'AnyContent\CMCK\Modules\Backend\Core\Listing\Controller::listRecords')->bind('listRecordsReset')->value('workspace',null)->value('language',null);

    }

}