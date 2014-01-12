<?php

namespace Anycontent\CMCK\Modules\Libs\jQueryIframeAutoHeight;

use AnyContent\CMCK\Modules\Core\Application\Application;

class Module extends \AnyContent\CMCK\Modules\Core\Core\Module
{

    public static function init(Application $app)
    {
        $app->addTemplatesFolders(__DIR__ . '/jquery-iframe-auto-height/release');
    }


    public static function run(Application $app)
    {
        $app['layout']->addJsFile('jquery.browser.js');
        $app['layout']->addJsFile('jquery.iframe-auto-height.plugin.1.9.3.min.js');
    }

}