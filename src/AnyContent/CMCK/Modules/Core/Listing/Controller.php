<?php

namespace AnyContent\CMCK\Modules\Core\Listing;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use AnyContent\CMCK\Application\Application;

class Controller
{


    public static function listRecords(Application $app, $contentTypeName)
    {

        return $app['twig']->render('content-list.twig');


    }
}