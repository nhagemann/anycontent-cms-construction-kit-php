<?php
$app['debug'] = true;

$app->registerModule('AnyContent\CMCK\Modules\Backend\Edit\TextFormElements');
$app->registerModule('AnyContent\CMCK\Modules\Backend\Edit\LinkFormElement');
$app->registerModule('AnyContent\CMCK\Modules\Backend\Edit\EmailFormElement');
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
$app->registerModule('AnyContent\CMCK\Modules\Backend\Edit\PasswordFormElement');
$app->registerModule('AnyContent\CMCK\Modules\Backend\Edit\EmailFormElement');
$app->registerModule('AnyContent\CMCK\Modules\Backend\Edit\LinkFormElement');
$app->registerModule('AnyContent\CMCK\Modules\Backend\Edit\Exchange');
$app->registerModule('AnyContent\CMCK\Modules\Backend\Libs\jQueryAutosize');
$app->registerModule('AnyContent\CMCK\Modules\Backend\Libs\jQueryMiniColors');
$app->registerModule('AnyContent\CMCK\Modules\Backend\Admin\CMDL');


$app->registerModule('AnyContent\CMCK\Modules\Backend\View\Glossary');

// Uncomment next lines if you use APC Cache and your PHP version doesn't have the apc_exists function

if (!function_exists('apc_exists'))
{
    function apc_exists($keys)
    {
        $result = null;
        apc_fetch($keys, $result);

        return $result;
    }
}