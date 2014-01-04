<?php

// web/index.php

require_once __DIR__ . '/../vendor/autoload.php';

$app          = new \AnyContent\CMCK\Modules\Core\Application\Application();
$app['debug'] = true;

$app->registerModule('AnyContent\CMCK\Modules\Core\Init');
$app->registerModule('AnyContent\CMCK\Modules\Core\Layout');
$app->registerModule('AnyContent\CMCK\Modules\Core\Repositories');
$app->registerModule('AnyContent\CMCK\Modules\Core\Context');
$app->registerModule('AnyContent\CMCK\Modules\Core\Menu');
$app->registerModule('AnyContent\CMCK\Modules\Core\Listing');
$app->registerModule('AnyContent\CMCK\Modules\Core\Pager');
$app->registerModule('AnyContent\CMCK\Modules\Core\Sort');
$app->registerModule('AnyContent\CMCK\Modules\Core\Edit');
$app->registerModule('AnyContent\CMCK\Modules\Core\TimeShift');
$app->registerModule('AnyContent\CMCK\Modules\Core\WorkspacesLanguages');

$app->registerModule('AnyContent\CMCK\Modules\Libs\BootstrapFormHelpers');

$app->registerModule('AnyContent\CMCK\Modules\Edit\TextFormElements');
$app->registerModule('AnyContent\CMCK\Modules\Edit\RichtextTinyMCEFormElements');

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

$app['repos']->setUserInfo(new \AnyContent\Client\UserInfo('mail@nilshagemann.de', 'Nils', 'Hagemann'));
$app->run();



