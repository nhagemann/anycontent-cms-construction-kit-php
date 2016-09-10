<?php
namespace AnyContent\Dev;

use AnyContent\CMCK\Modules\Backend\Core\Edit\EditRecordInsertEvent;
use AnyContent\CMCK\Modules\Backend\Core\Edit\EditRecordSaveEvent;

class URLListener
{

    public static function onRecordSave(EditRecordSaveEvent $event)
    {
        $event->setInfoMessage('Text');
        $event->setAlertMessage('XXX');
        $event->setErrorMessage('ASDFASDFA');
    }


}