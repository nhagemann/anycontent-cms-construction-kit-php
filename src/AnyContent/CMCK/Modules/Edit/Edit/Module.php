<?php

namespace AnyContent\CMCK\Modules\Edit\Edit;

use AnyContent\CMCK\Application\Application;

use AnyContent\CMCK\Modules\Edit\Edit\FormManager;

class Module extends \AnyContent\CMCK\Modules\Core\Core\Module
{

    public static function init(Application $app)
    {
        $app->addTemplatesFolders(__DIR__ . '/views/');
        $app
            ->get('/content/edit/{contentTypeAccessHash}/{recordId}', 'AnyContent\CMCK\Modules\Edit\Edit\Controller::editRecord')
            ->bind('editRecord');

        $app
            ->get('/content/add/{contentTypeAccessHash}', 'AnyContent\CMCK\Modules\Edit\Edit\Controller::addRecord')
            ->bind('addRecord');

        $app
            ->get('/content/delete/{contentTypeAccessHash}/{recordId}', 'AnyContent\CMCK\Modules\Edit\Edit\Controller::deleteRecord')
            ->bind('deleteRecord');

        $app->post('/content/edit/{contentTypeAccessHash}/{recordId}', 'AnyContent\CMCK\Modules\Edit\Edit\Controller::saveRecord');
        $app->post('/content/add/{contentTypeAccessHash}', 'AnyContent\CMCK\Modules\Edit\Edit\Controller::saveRecord');

        $app['form'] = $app->share(function ($app)
        {
            return new FormManager($app['twig']);
        });

    }


    public static function run(Application $app)
    {
        $app['form']->registerFormElement('default', 'AnyContent\CMCK\Modules\Edit\Edit\FormElementDefault');
        //$app['form']->registerFormElement('textfield', 'AnyContent\CMCK\Modules\Edit\Edit\FormElementTextfield');
    }

}