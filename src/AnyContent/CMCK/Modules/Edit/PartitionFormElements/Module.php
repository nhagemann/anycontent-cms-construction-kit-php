<?php

namespace AnyContent\CMCK\Modules\Edit\PartitionFormElements;

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
        $app['form']->registerFormElement('headline', 'AnyContent\CMCK\Modules\Edit\PartitionFormElements\FormElementHeadline');
        $app['form']->registerFormElement('section-start', 'AnyContent\CMCK\Modules\Edit\PartitionFormElements\FormElementSectionStart');
        $app['form']->registerFormElement('section-end', 'AnyContent\CMCK\Modules\Edit\PartitionFormElements\FormElementSectionEnd');
        $app['form']->registerFormElement('tab-start', 'AnyContent\CMCK\Modules\Edit\PartitionFormElements\FormElementTabStart');
        $app['form']->registerFormElement('tab-next', 'AnyContent\CMCK\Modules\Edit\PartitionFormElements\FormElementTabNext');
        $app['form']->registerFormElement('tab-end', 'AnyContent\CMCK\Modules\Edit\PartitionFormElements\FormElementTabEnd');

        $app['layout']->addCssFile('formelement-tab.css');
        $app['layout']->addCssFile('formelement-section.css');
    }

}