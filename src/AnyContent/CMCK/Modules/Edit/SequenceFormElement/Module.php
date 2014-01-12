<?php

namespace AnyContent\CMCK\Modules\Edit\SequenceFormElement;

use AnyContent\CMCK\Modules\Core\Application\Application;

class Module extends \AnyContent\CMCK\Modules\Core\Core\Module
{

    public static function init(Application $app)
    {
        $app->addTemplatesFolders(__DIR__ . '/views/');

        $app
            ->get('/sequence/edit/{contentTypeAccessHash}/{recordId}/{property}', 'AnyContent\CMCK\Modules\Edit\SequenceFormElement\Controller::editSequence')
            ->bind('editSequence');
        $app
            ->post('/sequence/edit/{contentTypeAccessHash}/{recordId}/{property}', 'AnyContent\CMCK\Modules\Edit\SequenceFormElement\Controller::postSequence')
            ->bind('postSequence');

        // additional query parameter insert and count
        $app
            ->get('/sequence/add/{contentTypeAccessHash}/{property}', 'AnyContent\CMCK\Modules\Edit\SequenceFormElement\Controller::addSequenceItem')
            ->bind('addSequenceItem');
    }


    public static function run(Application $app)
    {
        $app['form']->registerFormElement('sequence', 'AnyContent\CMCK\Modules\Edit\SequenceFormElement\FormElementSequence');

    }

}