<?php

namespace Anycontent\CMCK\Modules\Core\Layouts;

use AnyContent\CMCK\Application\Application;

class Module
{

    public static function init(Application $app)
    {

        $app->addTemplatesFolders(__DIR__.'/views/');



    }


}