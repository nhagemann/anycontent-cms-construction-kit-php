<?php

namespace AnyContent\CMCK\Modules\Backend\Core\Layout;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use AnyContent\CMCK\Modules\Backend\Core\Application\Application;

class Controller
{

    public static function css(Application $app, $files)
    {

        $response = new Response();

        $response->headers->add(array( 'Content-Type' => 'text/css' ));

        $date = new \DateTime();
        $date->sub(new \DateInterval('PT1H'));
        $response->setLastModified($date);

        if ($app['debug'] == false)
        {
            //$content = \CssMin::minify($content);

            $cacheToken = 'cmck_css_' . md5(serialize($files));

            if ($app['cache']->contains($cacheToken))
            {
                $response->setContent($app['cache']->fetch($cacheToken));
                return $response;
            }

        }


        $content = '';
        foreach (explode('/', $files) as $file)
        {
            if (trim($file) != '')
            {
                $content .= $app['twig']->render(trim($file) . '.css');
            }
        }

        $response->setContent($content);

        if ($app['debug'] == false)
        {
            $app['cache']->save($cacheToken, $content);
        }

        return $response;
    }


    public static function js(Application $app, $files)
    {
        $response = new Response();

        $response->headers->add(array( 'Content-Type' => 'text/javascript' ));

        $date = new \DateTime();
        $date->sub(new \DateInterval('PT1H'));
        $response->setLastModified($date);

        if ($app['debug'] == false)
        {
            //$content = \JSMinPlus::minify($content);

            $cacheToken = 'cmck_js_' . md5(serialize($files));

            if ($app['cache']->contains($cacheToken))
            {
                $response->setContent($app['cache']->fetch($cacheToken));

                return $response;
            }

        }

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
            $app['cache']->save($cacheToken, $content);
        }

        $response->setContent($content);

        return $response;
    }
}