<?php

// web/index.php

require_once __DIR__ . '/../vendor/autoload.php';

$app          = new \AnyContent\CMCK\Application\Application();
$app['debug'] = 1;

$app->registerModule('AnyContent\CMCK\Modules\Core\Layouts');
$app->registerModule('AnyContent\CMCK\Modules\Core\Listing');

$app->addRepository('http://anycontent.dev');

$app->run();