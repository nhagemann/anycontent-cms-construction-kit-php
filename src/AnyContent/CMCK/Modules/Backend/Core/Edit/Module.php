<?php

namespace AnyContent\CMCK\Modules\Backend\Core\Edit;

use AnyContent\CMCK\Modules\Backend\Core\Application\Application;

use AnyContent\CMCK\Modules\Backend\Core\Edit\FormManager;

class Module extends \AnyContent\CMCK\Modules\Backend\Core\Core\Module
{

    public function init(Application $app, $options = array())
    {
        parent::init($app, $options);

        $app->addTemplatesFolders(__DIR__ . '/views/');
        $app
            ->get('/content/edit/{contentTypeAccessHash}/{recordId}/{workspace}/{language}', 'AnyContent\CMCK\Modules\Backend\Core\Edit\Controller::editRecord')
            ->bind('editRecord')->value('workspace',null)->value('language',null);


        $app
            ->get('/content/add/{contentTypeAccessHash}/{workspace}/{language}', 'AnyContent\CMCK\Modules\Backend\Core\Edit\Controller::addRecord')
            ->bind('addRecord')->value('workspace',null)->value('language',null);

        $app
        ->get('/content/add/{contentTypeAccessHash}/{recordId}/{workspace}/{language}', 'AnyContent\CMCK\Modules\Backend\Core\Edit\Controller::addRecord')
        ->bind('addRecordVersion')->value('workspace',null)->value('language',null);

        $app
            ->get('/content/delete/{contentTypeAccessHash}/{recordId}/{workspace}/{language}', 'AnyContent\CMCK\Modules\Backend\Core\Edit\Controller::deleteRecord')
            ->bind('deleteRecord')->value('workspace',null)->value('language',null);

        $app
            ->get('/content/transfer/{contentTypeAccessHash}/{recordId}', 'AnyContent\CMCK\Modules\Backend\Core\Edit\Controller::transferRecordModal')
            ->bind('transferRecordModal');
        $app
            ->post('/content/transfer/{contentTypeAccessHash}/{recordId}', 'AnyContent\CMCK\Modules\Backend\Core\Edit\Controller::transferRecord')
            ->bind('transferRecord');

        // The url parts "workspace" and "language" are necessary to mirror the possible get routes, but aren't processed
        $app->post('/content/edit/{contentTypeAccessHash}/{recordId}/{workspace}/{language}', 'AnyContent\CMCK\Modules\Backend\Core\Edit\Controller::saveRecord')->value('workspace',null)->value('language',null);
        $app->post('/content/add/{contentTypeAccessHash}', 'AnyContent\CMCK\Modules\Backend\Core\Edit\Controller::saveRecord');
        $app->post('/content/add/{contentTypeAccessHash}/{recordId}', 'AnyContent\CMCK\Modules\Backend\Core\Edit\Controller::saveRecord');

        $app['form'] = $app->share(function ($app)
        {
            return new FormManager($app);
        });

    }


    public function run(Application $app)
    {
        $app['form']->registerFormElement('default', 'AnyContent\CMCK\Modules\Backend\Core\Edit\FormElementDefault');
    }

}