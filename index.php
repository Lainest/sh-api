<?php

declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

set_exception_handler("ErrorHandler::handleException");

header('Content-type: application/json; charset=UTF-8');

require_once './src/routes.php';
require_once './src/envConfig.php';

$router = new Router($dispatcher);
$router->useDispatcher();
