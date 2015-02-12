<?php

namespace AnyContent\CMCK\Modules\Backend\Edit\Exchange;

use AnyContent\Client\ContentFilter;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

use AnyContent\CMCK\Modules\Backend\Core\Application\Application;

use AnyContent\Client\Repository;
use AnyContent\Client\Record;
use AnyContent\Client\UserInfo;

class Controller
{

    public static function exportRecords(Application $app, Request $request, $contentTypeAccessHash, Module $module = null)
    {
        $vars = array();

        /** @var Repository $repository */
        $repository = $app['repos']->getRepositoryByContentTypeAccessHash($contentTypeAccessHash);

        if ($repository)
        {
            $contentTypeDefinition = $repository->getContentTypeDefinition();
            $app['context']->setCurrentRepository($repository);
            $app['context']->setCurrentContentType($contentTypeDefinition);
        }

        $downloadToken = md5(microtime());

        $vars['links']['execute'] = $app['url_generator']->generate('executeExportRecords', array( 'contentTypeAccessHash' => $contentTypeAccessHash, 'token' => $downloadToken ));
        $vars['links']['list']    = $app['url_generator']->generate('listRecords', array( 'contentTypeAccessHash' => $contentTypeAccessHash, 'page' => $app['context']->getCurrentListingPage(), 'workspace' => $app['context']->getCurrentWorkspace(), 'language' => $app['context']->getCurrentLanguage() ));
        $vars['token']            = $downloadToken;

        return $app->renderPage('exportrecords-modal.twig', $vars);
    }


    public static function executeExportRecords(Application $app, Request $request, $contentTypeAccessHash, $token, Module $module = null)
    {
        /** @var Repository $repository */
        $repository = $app['repos']->getRepositoryByContentTypeAccessHash($contentTypeAccessHash);

        if ($repository)
        {
            $contentTypeDefinition = $repository->getContentTypeDefinition();

            $workspace = $request->get('workspace');
            if (!$contentTypeDefinition->hasWorkspace($workspace))
            {
                $workspace = 'default';
            }

            $language = $request->get('language');
            if (!$contentTypeDefinition->hasLanguage($language))
            {
                $language = 'default';
            }

            $exporter = new Exporter();

            $data = $exporter->exportXLSX($repository, $repository->getContentTypeDefinition()
                                                                  ->getName(), $workspace, $language);
            if ($data)
            {
                $filename = strtolower(date('Ymd') . '_export_' . $contentTypeDefinition->getName() . '_' . $workspace . '_' . $language . '.xlsx');

                // Redirect output to a client’s web browser
                header('Content-Type: application/vnd.ms-excel');
                header('Content-Disposition: attachment;filename="' . $filename . '"');
                header('Cache-Control: max-age=0');
                // If you're serving to IE 9, then the following may be needed
                header('Cache-Control: max-age=1');

                // If you're serving to IE over SSL, then the following may be needed
                header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
                header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
                header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
                header('Pragma: public'); // HTTP/1.0

                $response = new Response($data);
                $cookie   = new Cookie("anycontent-download", $token, 0, '/', null, false, false); //Not http only!
                $response->headers->setCookie($cookie);

                $app['context']->addSuccessMessage('Records exported to ' . $filename);

                return $response;

            }

        }
        $app['context']->addErrorMessage('Could not export records.');

        return new RedirectResponse($app['url_generator']->generate('listRecords', array( 'contentTypeAccessHash' => $contentTypeAccessHash, 'page' => $app['context']->getCurrentListingPage(), 'workspace' => $app['context']->getCurrentWorkspace(), 'language' => $app['context']->getCurrentLanguage() )));

    }


    public static function importRecords(Application $app, Request $request, $contentTypeAccessHash, Module $module = null)
    {
        $vars           = array();
        $vars['record'] = false;

        /** @var Repository $repository */
        $repository = $app['repos']->getRepositoryByContentTypeAccessHash($contentTypeAccessHash);

        if ($repository)
        {
            $contentTypeDefinition = $repository->getContentTypeDefinition();
            $app['context']->setCurrentRepository($repository);
            $app['context']->setCurrentContentType($contentTypeDefinition);

        }

        return $app->renderPage('importrecords-modal.twig', $vars);
    }
}