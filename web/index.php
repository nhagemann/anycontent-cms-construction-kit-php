<?php

// web/index.php

require_once __DIR__ . '/../vendor/autoload.php';

$app          = new \AnyContent\CMCK\Application\Application();
$app['debug'] = false;

$app->registerModule('AnyContent\CMCK\Modules\Core\Init');
$app->registerModule('AnyContent\CMCK\Modules\Core\Layout');
$app->registerModule('AnyContent\CMCK\Modules\Core\Repositories');
$app->registerModule('AnyContent\CMCK\Modules\Core\Context');
$app->registerModule('AnyContent\CMCK\Modules\Core\Menu');
$app->registerModule('AnyContent\CMCK\Modules\Core\Listing');
$app->registerModule('AnyContent\CMCK\Modules\Core\Sort');
$app->registerModule('AnyContent\CMCK\Modules\Edit\Edit');


$app->initModules();

$app['repos']->addAllContentTypesOfRepository(('http://anycontent.dev/1/example'));

$app->run();



