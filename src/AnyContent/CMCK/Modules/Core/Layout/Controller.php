<?php

namespace AnyContent\CMCK\Modules\Core\Layout;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use AnyContent\CMCK\Modules\Core\Application\Application;

class Controller
{

    public static function css(Application $app, $files)
    {
        $content = '';
        foreach (explode('/', $files) as $file)
        {
            if (trim($file) != '')
            {
                $content .= $app['twig']->render(trim($file) . '.css');
            }
        }

        if ($app['debug'] == false)
        {
            $content = \CssMin::minify($content);
        }

        $response = new Response();
        $response->setContent($content);
        $response->headers->add(array( 'Content-Type' => 'text/css' ));

        $date = new \DateTime();
        $date->sub(new \DateInterval('PT1H'));
        $response->setLastModified($date);

        return $response;
    }


    public static function js(Application $app, $files)
    {
        $content = '';
        foreach (explode('/', $files) as $file)
        {
            if (trim($file) != '')
            {
                $content .= $app['twig']->render(trim($file) . '.js');
            }
        }

        if ($app['debug'] == false)
        {
            $content = \JSMinPlus::minify($content);
        }

        $response = new Response();
        $response->setContent($content);
        $response->headers->add(array( 'Content-Type' => 'text/javascript' ));

        $date = new \DateTime();
        $date->sub(new \DateInterval('PT1H'));
        $response->setLastModified($date);

        return $response;
    }
}