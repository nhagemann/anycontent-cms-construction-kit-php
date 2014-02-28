<?php

namespace AnyContent\CMCK\Modules\Backend\Edit\GeoLocationFormElement;

use AnyContent\Client\ContentFilter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

use AnyContent\CMCK\Modules\Backend\Core\Application\Application;

use AnyContent\Client\Repository;
use AnyContent\Client\Record;
use AnyContent\Client\UserInfo;

use AnyContent\CMCK\Modules\Backend\Edit\GeoLocationFormElement\Module;

class Controller
{

    public static function modal(Application $app, Request $request, Module $module = null, $name)
    {

        $vars = array('name'=>$name);

        return $app['twig']->render('formelement-image-modal.twig', $vars);

    }

}