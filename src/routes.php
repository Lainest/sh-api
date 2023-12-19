<?php

use FastRoute\RouteCollector;
use function FastRoute\simpleDispatcher;

$dispatcher = simpleDispatcher(function (RouteCollector $r) {
    $r->addRoute('GET', '/api-sh/users', 'getUsers');
    $r->addRoute('GET', '/api-sh/users/{id:\d+}', 'getUser');
    $r->addRoute('POST', '/api-sh/users', 'registerUser');
    $r->addRoute('POST', '/api-sh/login', 'loginUser');
    $r->addRoute('PUT', '/api-sh/users/{id:\d+}', 'updateUser');
    $r->addRoute('DELETE', '/api-sh/users/{id:\d+}', 'deleteUser');
});
