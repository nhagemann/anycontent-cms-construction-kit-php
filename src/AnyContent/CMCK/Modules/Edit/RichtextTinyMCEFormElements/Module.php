<?php

namespace AnyContent\CMCK\Modules\Edit\RichtextTinyMCEFormElements;

use AnyContent\CMCK\Modules\Core\Application\Application;

use AnyContent\CMCK\Modules\Edit\Edit\FormManager;

class Module extends \AnyContent\CMCK\Modules\Core\Core\Module
{

    public function init(Application $app, $options = array())
    {
        parent::init($app, $options);

        $app->addTemplatesFolders(__DIR__ . '/views/');


    }


    public function run(Application $app)
    {
        $app['form']->registerFormElement('richtext', 'AnyContent\CMCK\Modules\Edit\RichtextTinyMCEFormElements\FormElementRichtext');
    }

}