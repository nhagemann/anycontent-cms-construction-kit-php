<?php
if (!defined('APPLICATION_PATH'))
{
    define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/..'));
}

require_once __DIR__ . '/../vendor/autoload.php';

$app          = new \AnyContent\CMCK\Modules\Backend\Core\Application\Application();
$app['debug'] = true;

// Detect environment (default: prod) by checking for the existence of $app_env
if (isset($app_env) && in_array($app_env, array('prod','dev','test','console'))) { $app['env'] = $app_env; }else{$app['env'] = 'prod';}

$app->registerModule('AnyContent\CMCK\Modules\Backend\Core\Init');
$app->registerModule('AnyContent\CMCK\Modules\Backend\Core\Layout');
$app->registerModule('AnyContent\CMCK\Modules\Backend\Core\Repositories');
$app->registerModule('AnyContent\CMCK\Modules\Backend\Core\Context');
$app->registerModule('AnyContent\CMCK\Modules\Backend\Core\Menu');
$app->registerModule('AnyContent\CMCK\Modules\Backend\Core\Listing');
$app->registerModule('AnyContent\CMCK\Modules\Backend\Core\Pager');
$app->registerModule('AnyContent\CMCK\Modules\Backend\Core\Sort');
$app->registerModule('AnyContent\CMCK\Modules\Backend\Core\Edit');
$app->registerModule('AnyContent\CMCK\Modules\Backend\Core\Start');
$app->registerModule('AnyContent\CMCK\Modules\Backend\Core\Config');
$app->registerModule('AnyContent\CMCK\Modules\Backend\Core\Files');
$app->registerModule('AnyContent\CMCK\Modules\Backend\Core\TimeShift');
$app->registerModule('AnyContent\CMCK\Modules\Backend\Core\WorkspacesLanguages');

$app->registerModule('AnyContent\CMCK\Modules\Backend\Libs\BlockUI');
$app->registerModule('AnyContent\CMCK\Modules\Backend\Libs\BootstrapFormHelpers');
$app->registerModule('AnyContent\CMCK\Modules\Backend\Libs\NestedSortable');
$app->registerModule('AnyContent\CMCK\Modules\Backend\Libs\jQueryAutosize');

require_once (APPLICATION_PATH .'/config/modules.php');

$app->initModules();

/*
$memcache = new \Memcached();
$memcache->addServer('localhost', 11211);
$cacheDriver = new \Doctrine\Common\Cache\MemcachedCache();
$cacheDriver->setMemcached($memcache);
$app->setCacheDriver($cacheDriver);
  */

if ($app['env']=='test' || $app['env']=='console')
{
    return $app;
}

$app->run();





