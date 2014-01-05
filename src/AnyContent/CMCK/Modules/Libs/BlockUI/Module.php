<?php

namespace Anycontent\CMCK\Modules\Libs\BlockUI;

use AnyContent\CMCK\Modules\Core\Application\Application;

class Module extends \AnyContent\CMCK\Modules\Core\Core\Module
{

    public static function init(Application $app)
    {
        $app->addTemplatesFolders(__DIR__ . '/malsup-blockui');
    }


    public static function run(Application $app)
    {
        $app['layout']->addJsFile('jquery.blockUI.js');
    }

}