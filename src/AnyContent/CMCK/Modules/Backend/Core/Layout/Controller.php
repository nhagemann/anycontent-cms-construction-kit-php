<?php

namespace AnyContent\CMCK\Modules\Backend\Core\Layout;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use AnyContent\CMCK\Modules\Backend\Core\Application\Application;

class Controller
{

    public static function css(Application $app, Request $request, $files)
    {

        $response = new Response();

        $response->headers->add(array( 'Content-Type' => 'text/css' ));



        if ($app['debug'] == false)
        {
            //$content = \CssMin::minify($content);

            $cacheToken = 'cmck_css_' . md5(serialize($files));

            $response->headers->add(array( 'Cache-Control' => 'public' ));
            $response->headers->add(array('ETag'=>$cacheToken));

            if (in_array($cacheToken,$request->getETags()))
            {
                $response->setStatusCode(304);
            }

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


    public static function js(Application $app, Request $request, $files)
    {
        $response = new Response();

        $response->headers->add(array( 'Content-Type' => 'text/javascript' ));

        if ($app['debug'] == false)
        {

            //$content = \JSMinPlus::minify($content);

            $cacheToken = 'cmck_js_' . md5(serialize($files));

            $response->headers->add(array( 'Cache-Control' => 'public' ));
            $response->headers->add(array('ETag'=>$cacheToken));

            if (in_array($cacheToken,$request->getETags()))
            {
                   $response->setStatusCode(304);
            }



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