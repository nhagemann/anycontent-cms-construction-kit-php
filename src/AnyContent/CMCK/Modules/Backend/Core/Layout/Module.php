<?php

namespace AnyContent\CMCK\Modules\Backend\Core\Layout;

use AnyContent\CMCK\Modules\Backend\Core\Application\Application;

use AnyContent\CMCK\Modules\Backend\Core\Layout\LayoutManager;


class Module extends \AnyContent\CMCK\Modules\Backend\Core\Core\Module
{
    const EVENT_LAYOUT_TEMPLATE_RENDER = 'event.layout.template.render';

    public function init(Application $app, $options = array())
    {
        parent::init($app, $options);

        $app->addTemplatesFolders(__DIR__ . '/views/');

        $app->get('/css/{revision}/{files}', 'AnyContent\CMCK\Modules\Backend\Core\Layout\Controller::css')->assert('files', '.+');
        $app->get('/js/{revision}/{files}', 'AnyContent\CMCK\Modules\Backend\Core\Layout\Controller::js')->assert('files', '.+');

        $app['layout'] = $app->share(function ($app)
        {
            return new LayoutManager($app, $app['twig'], $app['context']);
        });

        $app['console']->add(new DumpRevisionCommand());

    }



}