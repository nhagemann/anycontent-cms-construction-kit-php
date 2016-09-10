<?php

namespace AnyContent\Dev;

use AnyContent\CMCK\Modules\Backend\Core\Application\Application;
use Symfony\Component\HttpKernel\KernelEvents;

class Module extends \AnyContent\CMCK\Modules\Backend\Core\Core\Module
{

    public function init(Application $app, $options = array())
    {
        parent::init($app, $options);
        $app->addTemplatesFolders(__DIR__ . '/views/');
        //$app->addListener(\AnyContent\CMCK\Modules\Backend\Core\Menu\Module::EVENT_MENU_BUTTONGROUP_RENDER, 'AnyContent\Dev\EventListener::onMenuButtonGroupRenderEvent');

//        $app->addListener(KernelEvents::EXCEPTION,'AnyContent\Dev\EventListener::onKernelException');


        //$app['form']->registerCustomFormElement('pageselector', 'AnyContent\Dev\PageSelector');
    }


    public function run(Application $app)
    {
        $app['form']->registerCustomFormElement('pageselector', 'AnyContent\Dev\PageSelector');
        $app['layout']->addCssFile('test.css');
        $app->addListener(\AnyContent\CMCK\Modules\Backend\Core\Edit\Module::EVENT_EDIT_RECORD_BEFORE_UPDATE,'AnyContent\Dev\EventListener::onRecordSave');


    }
}