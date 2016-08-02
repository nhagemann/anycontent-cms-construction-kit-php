<?php

namespace AnyContent\Dev;

use AnyContent\CMCK\Modules\Backend\Core\Application\Application;
use AnyContent\CMCK\Modules\Backend\Core\Edit\EditRecordSaveEvent;

class Module extends \AnyContent\CMCK\Modules\Backend\Core\Core\Module
{

    public function init(Application $app, $options = array())
    {
        parent::init($app, $options);

        $app->addTemplatesFolders(__DIR__ . '/views/');
    }


    public function run(Application $app)
    {
        $app['layout']->addCssFile('dev.css');
        $app->addListener(\AnyContent\CMCK\Modules\Backend\Core\Edit\Module::EVENT_EDIT_RECORD_BEFORE_INSERT,'AnyContent\Dev\URLListener::onRecordSave');
        $app->addListener(\AnyContent\CMCK\Modules\Backend\Core\Edit\Module::EVENT_EDIT_RECORD_BEFORE_UPDATE,'AnyContent\Dev\URLListener::onRecordSave');
    }
    
}