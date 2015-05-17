<?php

namespace AnyContent\CMCK\Modules\Backend\Edit\GeoLocationFormElement;

use AnyContent\CMCK\Modules\Backend\Core\Application\Application;

class Module extends \AnyContent\CMCK\Modules\Backend\Core\Core\Module
{

    public function init(Application $app, $options = array())
    {
        $app->addTemplatesFolders(__DIR__ . '/views/');
        $app
            ->get('/edit/modal/geolocation/{name}', 'AnyContent\CMCK\Modules\Backend\Edit\GeoLocationFormElement\Controller::modal')
            ->value('module',$this);
        $app
            ->get('/edit/modal/geolocation/{name}/{lat}/{long}', 'AnyContent\CMCK\Modules\Backend\Edit\GeoLocationFormElement\Controller::modal')
            ->value('module',$this);

    }


    public function run(Application $app)
    {
        $app['form']->registerFormElement('geolocation', 'AnyContent\CMCK\Modules\Backend\Edit\GeoLocationFormElement\FormElementGeoLocation');

        //@upgrade API KEY as module option
        //@upgrade add only if a record with geolocation form element is edited
        $app['layout']->addJsLinkToHead('https://maps.googleapis.com/maps/api/js?v=3.19&sensor=false');
    }

}