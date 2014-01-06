<?php

namespace AnyContent\CMCK\Modules\Core\Files;

use AnyContent\CMCK\Modules\Core\Application\Application;

class Module extends \AnyContent\CMCK\Modules\Core\Core\Module
{

    public static function init(Application $app)
    {

        $app->addTemplatesFolders(__DIR__ . '/views/');
        $app->get('/files/{repositoryAccessHash}/{path}', 'AnyContent\CMCK\Modules\Core\Files\Controller::listFiles')->assert('path', '.*')->bind('listFiles');

    }

}