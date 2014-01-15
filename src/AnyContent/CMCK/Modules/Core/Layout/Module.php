<?php

namespace Anycontent\CMCK\Modules\Core\Layout;

use AnyContent\CMCK\Modules\Core\Application\Application;

use AnyContent\CMCK\Modules\Core\Layout\LayoutManager;

class Module extends \AnyContent\CMCK\Modules\Core\Core\Module
{

    public function init(Application $app, $options = array())
    {
        parent::init($app, $options);

        $app->addTemplatesFolders(__DIR__ . '/views/');

        $app->get('/css/{files}', 'AnyContent\CMCK\Modules\Core\Layout\Controller::css')->assert('files', '.+');
        $app->get('/js/{files}', 'AnyContent\CMCK\Modules\Core\Layout\Controller::js')->assert('files', '.+');

        $app['layout'] = $app->share(function ($app)
        {
            return new LayoutManager($app['twig'], $app['context']);
        });

    }



}