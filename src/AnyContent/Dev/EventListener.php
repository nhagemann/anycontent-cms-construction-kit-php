<?php
namespace AnyContent\Dev;

use AnyContent\CMCK\Modules\Backend\Core\Edit\EditRecordSaveEvent;
use AnyContent\CMCK\Modules\Backend\Core\Menu\MenuButtonGroupRenderEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Event\KernelEvent;

class EventListener
{

    public static function onMenuButtonGroupRenderEvent(MenuButtonGroupRenderEvent $event)
    {
        $buttons = $event->getButtons();

        /** @var Request $request */
        $request = $event->getApp('request');

        $route = $request->get('_route');

        if ($route == 'listRecords')
        {
            $buttons[]= ['label'=>'Shares', 'url'=>'http://www.ard.de', 'glyphicon'=>'glyphicon-share-alt'];
            $event->setButtons($buttons);
        }

    }


    public static function onKernelException(GetResponseForExceptionEvent $event)
    {

        $event->setResponse(new Response('TEST',404));
    }

    public static function onRecordSave(EditRecordSaveEvent $event)
    {
        $record = $event->getRecord();
        //$event->setAlertMessage('test alert');
        //$event->setErrorMessage('test error');
    }

}