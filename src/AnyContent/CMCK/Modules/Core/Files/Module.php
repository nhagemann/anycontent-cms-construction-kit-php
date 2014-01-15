<?php

namespace AnyContent\CMCK\Modules\Core\Files;

use AnyContent\CMCK\Modules\Core\Application\Application;

class Module extends \AnyContent\CMCK\Modules\Core\Core\Module
{

    public function init(Application $app, $options = array())
    {


        parent::init($app, $options);

        $app->addTemplatesFolders(__DIR__ . '/views/');
        $app->get('/files/{repositoryAccessHash}/{path}', 'AnyContent\CMCK\Modules\Core\Files\Controller::listFiles')
            ->assert('path', '.*')->bind('listFiles');
        $app->get('/file/{repositoryAccessHash}/view/{id}', 'AnyContent\CMCK\Modules\Core\Files\Controller::viewFile')
            ->assert('id', '.*')->bind('viewFile');
        $app
            ->get('/file/{repositoryAccessHash}/download/{id}', 'AnyContent\CMCK\Modules\Core\Files\Controller::downloadFile')
            ->assert('id', '.*')->bind('downloadFile');
        $app
            ->get('/file/{repositoryAccessHash}/delete/{id}', 'AnyContent\CMCK\Modules\Core\Files\Controller::deleteFile')
            ->assert('id', '.*')->bind('deleteFile');

    }

}