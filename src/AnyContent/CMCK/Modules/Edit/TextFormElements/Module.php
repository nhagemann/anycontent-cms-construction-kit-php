<?php

namespace AnyContent\CMCK\Modules\Edit\TextFormElements;

use AnyContent\CMCK\Modules\Core\Application\Application;


class Module extends \AnyContent\CMCK\Modules\Core\Core\Module
{

    public static function init(Application $app)
    {
        $app->addTemplatesFolders(__DIR__ . '/views/');


    }


    public static function run(Application $app)
    {
        $app['form']->registerFormElement('textfield', 'AnyContent\CMCK\Modules\Edit\TextFormElements\FormElementTextfield');
        $app['form']->registerFormElement('textarea', 'AnyContent\CMCK\Modules\Edit\TextFormElements\FormElementTextarea');
    }

}