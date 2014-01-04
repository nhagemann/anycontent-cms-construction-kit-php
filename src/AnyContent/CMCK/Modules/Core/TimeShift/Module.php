<?php

namespace Anycontent\CMCK\Modules\Core\Timeshift;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use AnyContent\CMCK\Modules\Core\Application\Application;

class Module extends \AnyContent\CMCK\Modules\Core\Core\Module
{

    public static function init(Application $app)
    {

        $app
            ->post('/timeshift/content/list/{contentTypeAccessHash}/page/{page}', 'AnyContent\CMCK\Modules\Core\TimeShift\Controller::timeShiftListRecords')
            ->bind('timeShiftListRecords');
        $app
            ->post('/timeshift/content/edit/{contentTypeAccessHash}/{recordId}', 'AnyContent\CMCK\Modules\Core\TimeShift\Controller::timeShiftEditRecord')
            ->bind('timeShiftEditRecord');

    }


    public static function preRender(Application $app)
    {
        $timeshift              = $app['layout']->getVar('timeshift',array());
        $timeshift['active']    = false;
        $timeshift['date']      = 'today';
        $timeshift['time']      = 'now';
        $timeshift['timestamp'] = time();

        if ($app['context']->getCurrentTimeShift() != 0)
        {
            $date = new \DateTime();
            $date->setTimestamp($app['context']->getCurrentTimeShift());
            $timeshift['active']    = true;
            $timeshift['timestamp'] = $app['context']->getCurrentTimeShift();
            $timeshift['date']      = $date->format('d.m.Y');
            $timeshift['time']      = $date->format('H:i');
            //->setTimeStamp($timestamp);

        }
        $app['layout']->addVar('timeshift', $timeshift);
    }
}