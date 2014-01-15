<?php

namespace AnyContent\CMCK\Modules\Edit\Export;

use AnyContent\CMCK\Modules\Core\Application\Application;

class Module extends \AnyContent\CMCK\Modules\Core\Core\Module
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
            ->get('/content/export/{contentTypeAccessHash}', 'AnyContent\CMCK\Modules\Edit\Export\Controller::exportRecords')
            ->bind('exportRecords')->value('module',$this);
        $app
            ->get('/content/export/{contentTypeAccessHash}', 'AnyContent\CMCK\Modules\Edit\Export\Controller::importRecords')
            ->bind('importRecords');
    }




}