<?php

$app->registerModule('AnyContent\CMCK\Modules\Backend\Edit\TextFormElements');
$app->registerModule('AnyContent\CMCK\Modules\Backend\Edit\PartitionFormElements');
$app->registerModule('AnyContent\CMCK\Modules\Backend\Edit\RichtextTinyMCEFormElements');
$app->registerModule('AnyContent\CMCK\Modules\Backend\Edit\SourceCodeFormElements');
$app->registerModule('AnyContent\CMCK\Modules\Backend\Edit\SelectionFormElements');
$app->registerModule('AnyContent\CMCK\Modules\Backend\Edit\NumberFormElement');
$app->registerModule('AnyContent\CMCK\Modules\Backend\Edit\DateTimeFormElements');//array('Format.Long'=>'d.m.Y','Format.Short'=>'d.m','Format.DateTime'=>'d.m.Y H:i','Format.Full'=>'d.m.Y H:i:s')
$app->registerModule('AnyContent\CMCK\Modules\Backend\Edit\FileFormElements');
$app->registerModule('AnyContent\CMCK\Modules\Backend\Edit\SequenceFormElement');
$app->registerModule('AnyContent\CMCK\Modules\Backend\Edit\InsertFormElement');
$app->registerModule('AnyContent\CMCK\Modules\Backend\Edit\ReferenceFormElements');
$app->registerModule('AnyContent\CMCK\Modules\Backend\Edit\GeoLocationFormElement');
$app->registerModule('AnyContent\CMCK\Modules\Backend\Edit\TableFormElement');
$app->registerModule('AnyContent\CMCK\Modules\Backend\Edit\Exchange',array('FormatCode.DateTime'=>'d.m.YYYY hh:mm'));