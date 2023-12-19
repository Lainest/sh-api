<?php

declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

header('Content-type: application/json; charset=UTF-8');

include './src/routes.php';

$router = new Router($dispatcher);
$router->useDispatcher();
