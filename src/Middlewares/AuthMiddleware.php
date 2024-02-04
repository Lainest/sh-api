<?php

class AuthMiddleware extends Singleton
{

    const ROLE_ADMIN = 1;
    const ROLE_USER = 2;

    public function handleRequest(array $routeInfo)
    {
        list($controller_name, $handler) = explode('@', $routeInfo[1][0]);

        $controller = new $controller_name();

        if ($handler == 'login' || $handler == 'refresh') {
            $controller->$handler();
            return;
        }

        $token = $this->validateJwt();

        $role_requested = $routeInfo[1][1];


        if ($this->checkUserRole($token->role, $role_requested)) {

            $this->callHandler($routeInfo[2], $token, $controller, $handler);
        }
    }

    private function validateJwt()
    {
        $jwt = MyJwt::getInstance();

        $token = $this->getBearerToken();

        if ($token === null) {
            new Response([
                "error" => "Access token missing"
            ], 400);
            die;
        }

        return $jwt->decode($token);
    }

    private function callHandler(array $request_vars, $token, Controller $controller, string $handler)
    {

        if (isset($request_vars['id'])) {

            if ($token->id == $request_vars['id'])
                $controller->$handler($request_vars['id']);
            else
                new Response([
                    "error" => "Cannot access this resource"
                ], 401);
        } else {
            $controller->$handler();
        }
    }

    private function getAuthorizationHeader()
    {
        $headers = null;
        if (isset($_SERVER['Authorization'])) {
            $headers = trim($_SERVER["Authorization"]);
        } else if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
            //Nginx or fast CGI
            $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
        } elseif (function_exists('apache_request_headers')) {
            $requestHeaders = apache_request_headers();
            $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
            if (isset($requestHeaders['Authorization'])) {
                $headers = trim($requestHeaders['Authorization']);
            }
        }
        return $headers;
    }

    private function getBearerToken()
    {
        $headers = $this->getAuthorizationHeader();
        if (!empty($headers)) {
            if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
                return $matches[1];
            }
        }
        return null;
    }

    private function checkUserRole($user_role, $required_role)
    {
        if ($user_role === self::ROLE_ADMIN) {
            return true;
        }

        if ($user_role !== $required_role) {
            new Response([
                "Error" => "Not authorized"
            ], 401);
            die;
        }

        return true;
    }
}
