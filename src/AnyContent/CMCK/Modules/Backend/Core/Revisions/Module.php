<?php

namespace AnyContent\CMCK\Modules\Backend\Core\Revisions;

use AnyContent\CMCK\Modules\Backend\Core\Application\Application;

use AnyContent\CMCK\Modules\Backend\Core\Edit\FormManager;

class Module extends \AnyContent\CMCK\Modules\Backend\Core\Core\Module
{

    public function init(Application $app, $options = array())
    {
        parent::init($app, $options);

        $app->addTemplatesFolders(__DIR__ . '/views/');
        $app
            ->get('/content/revision/{contentTypeAccessHash}/{recordId}/{workspace}/{language}', 'AnyContent\CMCK\Modules\Backend\Core\Revisions\Controller::listRecordRevisions')
            ->bind('listRecordRevisions')->value('workspace', null)->value('language', null);
    }

}