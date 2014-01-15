<?php

namespace AnyContent\CMCK\Modules\Edit\TextFormElements;

use AnyContent\CMCK\Modules\Core\Application\Application;


class Module extends \AnyContent\CMCK\Modules\Core\Core\Module
{

    public function init(Application $app, $options = array())
    {
        parent::init($app, $options);

        $app->addTemplatesFolders(__DIR__ . '/views/');


    }


    public function run(Application $app)
    {
        $app['form']->registerFormElement('textfield', 'AnyContent\CMCK\Modules\Edit\TextFormElements\FormElementTextfield');
        $app['form']->registerFormElement('textarea', 'AnyContent\CMCK\Modules\Edit\TextFormElements\FormElementTextarea');
    }

}