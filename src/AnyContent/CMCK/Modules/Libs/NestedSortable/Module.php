<?php

namespace Anycontent\CMCK\Modules\Libs\NestedSortable;

use AnyContent\CMCK\Modules\Core\Application\Application;

class Module extends \AnyContent\CMCK\Modules\Core\Core\Module
{

    public static function init(Application $app)
    {
        $app->addTemplatesFolders(__DIR__ . '/mjsarfatti-nestedSortable');
    }


    public static function run(Application $app)
    {
        $app['layout']->addJsFile('jquery.mjs.nestedSortable.js');
    }

}