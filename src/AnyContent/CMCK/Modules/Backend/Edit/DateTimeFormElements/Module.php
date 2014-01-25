<?php

namespace AnyContent\CMCK\Modules\Backend\Edit\DateTimeFormElements;

use AnyContent\CMCK\Modules\Backend\Core\Application\Application;

class Module extends \AnyContent\CMCK\Modules\Backend\Core\Core\Module
{

    /**
     * @link http://en.wikipedia.org/wiki/ISO_8601
     * @var array
     */
    protected $defaultOptions = array(  'Format.Long.Frontend' => 'y-m-d', 'Format.Short.Frontend' => 'm-d', 'Format.DateTime.Frontend' => 'Y-MM-DD H:i', 'Format.Full.Frontend' => 'YYYY-MM-DDH:i:s','Format.Long.PHPConvert' => 'Y-m-d', 'Format.Short.PHPConvert' => 'm-d', 'Format.DateTime.PHPConvert' => 'YYYY-MM-DD H:i', 'Format.Full.PHPConvert' => 'YYYY-MM-DDH:i:s');


    public function init(Application $app, $options = array())
    {
        parent::init($app, $options);

        $app->addTemplatesFolders(__DIR__ . '/views/');

    }


    public function run(Application $app)
    {
        $app['form']->registerFormElement('date', 'AnyContent\CMCK\Modules\Backend\Edit\DateTimeFormElements\FormElementDate', $this->options);
    }

}