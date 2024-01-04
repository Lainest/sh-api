<?php

class Router
{
    public string $http_method;
    public string $uri;


    public function __construct(private FastRoute\Dispatcher $dispatcher)
    {
        $this->http_method = $_SERVER['REQUEST_METHOD'];
        $this->uri = $this->buildUri();
    }

    private function buildUri(): string
    {
        $uri = $_SERVER['REQUEST_URI'];

        if (false !== $pos = strpos($uri, '?')) {
            $uri = substr($uri, 0, $pos);
        }

        $uri = rawurldecode($uri);

        return $uri;
    }

    public function useDispatcher(): void
    {
        $routeInfo = $this->dispatcher->dispatch(
            $this->http_method,
            $this->uri
        );

        switch ($routeInfo[0]) {
            case FastRoute\Dispatcher::NOT_FOUND:
                new Response([
                    "Error" => "Uri not found",
                ], 404);
                break;
            case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
                new Response([
                    "Methods allowed" => $routeInfo[1]
                ], 405);
                break;
            case FastRoute\Dispatcher::FOUND:
                $this->routeFound($routeInfo);
        }
    }

    private function routeFound(array $routeInfo): void
    {
        $middleware = AuthMiddleware::getInstance();

        $middleware->handleRequest($routeInfo);
    }
}
