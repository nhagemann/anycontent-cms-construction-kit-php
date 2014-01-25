<?php

// web/index.php

require_once __DIR__ . '/../vendor/autoload.php';

$app          = new \AnyContent\CMCK\Modules\Backend\Core\Application\Application();
$app['debug'] = true;

$app->registerModule('AnyContent\CMCK\Modules\Backend\Core\Init');
$app->registerModule('AnyContent\CMCK\Modules\Backend\Core\Layout');
$app->registerModule('AnyContent\CMCK\Modules\Backend\Core\Repositories');
$app->registerModule('AnyContent\CMCK\Modules\Backend\Core\Context');
$app->registerModule('AnyContent\CMCK\Modules\Backend\Core\Menu');
$app->registerModule('AnyContent\CMCK\Modules\Backend\Core\Listing');
$app->registerModule('AnyContent\CMCK\Modules\Backend\Core\Pager');
$app->registerModule('AnyContent\CMCK\Modules\Backend\Core\Sort');
$app->registerModule('AnyContent\CMCK\Modules\Backend\Core\Edit');
$app->registerModule('AnyContent\CMCK\Modules\Backend\Core\Files');
$app->registerModule('AnyContent\CMCK\Modules\Backend\Core\TimeShift');
$app->registerModule('AnyContent\CMCK\Modules\Backend\Core\WorkspacesLanguages');

$app->registerModule('AnyContent\CMCK\Modules\Backend\Libs\BlockUI');
$app->registerModule('AnyContent\CMCK\Modules\Backend\Libs\BootstrapFormHelpers');
$app->registerModule('AnyContent\CMCK\Modules\Backend\Libs\NestedSortable');

$app->registerModule('AnyContent\CMCK\Modules\Backend\Edit\TextFormElements');
$app->registerModule('AnyContent\CMCK\Modules\Backend\Edit\PartitionFormElements');
$app->registerModule('AnyContent\CMCK\Modules\Backend\Edit\RichtextTinyMCEFormElements');
$app->registerModule('AnyContent\CMCK\Modules\Backend\Edit\SequenceFormElement');
$app->registerModule('AnyContent\CMCK\Modules\Backend\Edit\SelectionFormElements');
$app->registerModule('AnyContent\CMCK\Modules\Backend\Edit\DateTimeFormElements');//array('Format.Long'=>'d.m.Y','Format.Short'=>'d.m','Format.DateTime'=>'d.m.Y H:i','Format.Full'=>'d.m.Y H:i:s')

$app->registerModule('AnyContent\CMCK\Modules\Backend\Edit\Export',array('FormatCode.DateTime'=>'d.m.YYYY hh:mm'));

$app->initModules();


/*
$memcache = new \Memcached();
$memcache->addServer('localhost', 11211);
$cacheDriver = new \Doctrine\Common\Cache\MemcachedCache();
$cacheDriver->setMemcached($memcache);
$app->setCacheDriver($cacheDriver);
  */

$app['repos']->addAllContentTypesOfRepository(('http://anycontent.dev/1/example'));
$app['repos']->addAllContentTypesOfRepository(('http://anycontent.dev/1/nhagemann'));
$app['repos']->addAllContentTypesOfRepository(('http://anycontent.dev/1/demo'));


$app['repos']->setUserInfo(new \AnyContent\Client\UserInfo('mail@nilshagemann.de', 'Nils', 'Hagemann'));
$app->run();



