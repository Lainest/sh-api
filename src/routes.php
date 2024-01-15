<?php

use FastRoute\RouteCollector;
use function FastRoute\simpleDispatcher;


$dispatcher = simpleDispatcher(function (RouteCollector $r) {

    $base_url = "/api-sh";

    $r->addGroup("$base_url/users", function (FastRoute\RouteCollector $r) {
        $r->addRoute('GET', '/all', ['UserController@getAll', 1]);
        $r->addRoute('GET', '/{id:\d+}', ['UserController@getOne', 3]);
        $r->addRoute('POST', '/register', ['UserController@create', 1]);
        $r->addRoute('POST', '/login', ['UserController@login', 3]);
    });

    $r->addGroup("$base_url/events", function (FastRoute\RouteCollector $r) {
        $r->addRoute('GET', '/all', ['EventsController@getAll', 1]);
        $r->addRoute('GET', '/{id:\d+}', ['EventsController@getOne', 3]);
        $r->addRoute('POST', '/create', ['EventsController@create', 1]);
    });
});
