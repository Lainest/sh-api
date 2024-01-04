<?php

use FastRoute\RouteCollector;
use function FastRoute\simpleDispatcher;

$dispatcher = simpleDispatcher(function (RouteCollector $r) {
    $r->addRoute('GET', '/api-sh/users', ['getUsers', 1]);
    $r->addRoute('GET', '/api-sh/users/{id:\d+}', ['getUser', 2]);
    $r->addRoute('POST', '/api-sh/users', ['registerUser', 1]);
    $r->addRoute('POST', '/api-sh/login', ['loginUser', 2]);
    $r->addRoute('PUT', '/api-sh/users/{id:\d+}', ['updateUser', 2]);
    // $r->addRoute('DELETE', '/api-sh/users/{id:\d+}', '[deleteUser], 1');
});
