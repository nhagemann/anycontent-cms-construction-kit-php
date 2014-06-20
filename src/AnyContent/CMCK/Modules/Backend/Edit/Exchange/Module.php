<?php

namespace AnyContent\CMCK\Modules\Backend\Edit\Exchange;

use AnyContent\CMCK\Modules\Backend\Core\Application\Application;


class Module extends \AnyContent\CMCK\Modules\Backend\Core\Core\Module
{

    /**
     * \PHPExcel_Style_NumberFormat
     *
     * @var array
     */
    protected $defaultOptions = array(

        'FormatCode.DateTime' => 'YYYY/M/D hh:mm'

    );


    public function init(Application $app, $options = array())
    {

        parent::init($app, $options);

        $app
            ->get('/content/export/{contentTypeAccessHash}', 'AnyContent\CMCK\Modules\Backend\Edit\Export\Controller::exportRecords')
            ->bind('exportRecords')->value('module',$this);
        $app
            ->get('/content/export/{contentTypeAccessHash}', 'AnyContent\CMCK\Modules\Backend\Edit\Export\Controller::importRecords')
            ->bind('importRecords');

        $app['console']->add(new ExportCommand());
        $app['console']->add(new ImportCommand());
    }




}