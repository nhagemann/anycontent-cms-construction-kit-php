<?php

namespace AnyContent\CMCK\Modules\Edit\RichtextTinyMCEFormElements;

use AnyContent\CMCK\Modules\Core\Application\Application;

use AnyContent\CMCK\Modules\Edit\Edit\FormManager;

class Module extends \AnyContent\CMCK\Modules\Core\Core\Module
{

    public static function init(Application $app)
    {
        $app->addTemplatesFolders(__DIR__ . '/views/');


    }


    public static function run(Application $app)
    {
        $app['form']->registerFormElement('richtext', 'AnyContent\CMCK\Modules\Edit\RichtextTinyMCEFormElements\FormElementRichtext');
    }

}