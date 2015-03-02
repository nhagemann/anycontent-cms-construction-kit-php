<?php
$app['debug'] = true;

$app->registerModule('AnyContent\CMCK\Modules\Backend\Edit\TextFormElements');
$app->registerModule('AnyContent\CMCK\Modules\Backend\Edit\LinkFormElement');
$app->registerModule('AnyContent\CMCK\Modules\Backend\Edit\PartitionFormElements');
$app->registerModule('AnyContent\CMCK\Modules\Backend\Edit\RichtextTinyMCEFormElements');
$app->registerModule('AnyContent\CMCK\Modules\Backend\Edit\SourceCodeFormElements');
$app->registerModule('AnyContent\CMCK\Modules\Backend\Edit\SelectionFormElements');
$app->registerModule('AnyContent\CMCK\Modules\Backend\Edit\NumberFormElement');
$app->registerModule('AnyContent\CMCK\Modules\Backend\Edit\RangeFormElement');
$app->registerModule('AnyContent\CMCK\Modules\Backend\Edit\DateTimeFormElements');
$app->registerModule('AnyContent\CMCK\Modules\Backend\Edit\FileFormElements');
$app->registerModule('AnyContent\CMCK\Modules\Backend\Edit\SequenceFormElement');
$app->registerModule('AnyContent\CMCK\Modules\Backend\Edit\InsertFormElement');
$app->registerModule('AnyContent\CMCK\Modules\Backend\Edit\ReferenceFormElements');
$app->registerModule('AnyContent\CMCK\Modules\Backend\Edit\GeoLocationFormElement');
$app->registerModule('AnyContent\CMCK\Modules\Backend\Edit\TableFormElement');
$app->registerModule('AnyContent\CMCK\Modules\Backend\Edit\ColorFormElement');
$app->registerModule('AnyContent\CMCK\Modules\Backend\Edit\Exchange', array( 'FormatCode.DateTime' => 'd.m.YYYY hh:mm' ));
$app->registerModule('AnyContent\CMCK\Modules\Backend\Libs\jQueryAutosize');
$app->registerModule('AnyContent\CMCK\Modules\Backend\Libs\jQueryMiniColors');

// If you want to edit your content/config configuration from the backend:
// $app->registerModule('AnyContent\CMCK\Modules\Admin\CMDL');

// Provide apc_exists function for older PHP versions

if (!function_exists('apc_exists'))
{
    function apc_exists($keys)
    {
        $result = null;
        apc_fetch($keys, $result);

        return $result;
    }
}

// Configure your logging here

$app['monolog'] = $app->share(function ($app) {
    $log =  new \Monolog\Logger('CMCK');
    $handler = new \Monolog\Handler\ErrorLogHandler();
    $log->pushHandler($handler);
    return $log;
});