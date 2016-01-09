<?php

namespace AnyContent\CMCK\Modules\Backend\Core\Layout;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use AnyContent\CMCK\Modules\Backend\Core\Application\Application;

class Controller
{



    public static function js(Application $app, Request $request, $revision, $files)
    {
        $response = new Response();

        $response->headers->add(array( 'Content-Type' => 'text/javascript' ));

        $content = '';
        foreach (explode('/', $files) as $file)
        {
            if (trim($file) != '')
            {
                $content .= $app['twig']->render(trim($file) . '.js');
            }
        }

        $response->setContent($content);

        $response->setPublic();
        $response->setMaxAge(3600 * 24 * 7);

        return $response;
    }
}