<?php

class AuthMiddleware extends Singleton
{

    public function handleRequest(array $routeInfo)
    {
        $handler = $routeInfo[1][0];
        $controller = new UserController(new UserGateway(new Database('localhost', 'steward_hub', 'root', '')));

        if ($handler == 'loginUser') {
            // login
            $controller->$handler();
            return;
        }

        $token = $this->validateJwt();

        if ($token === null) {
            die;
        }

        $role_requested = $routeInfo[1][1];
        $vars = $routeInfo[2];

        $user_role = $token->role;

        if ($user_role === $role_requested) {
            if (isset($vars['id']))
                $controller->$handler($vars['id']);
            else
                $controller->$handler();
        } else {
            new Response([
                "Error" => "Not authorized"
            ], 401);
            die;
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

    /** 
     * Get header Authorization
     * */
    private function getAuthorizationHeader()
    {
        $headers = null;
        if (isset($_SERVER['Authorization'])) {
            $headers = trim($_SERVER["Authorization"]);
        } else if (isset($_SERVER['HTTP_AUTHORIZATION'])) { //Nginx or fast CGI
            $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
        } elseif (function_exists('apache_request_headers')) {
            $requestHeaders = apache_request_headers();
            // Server-side fix for bug in old Android versions (a nice side-effect of this fix means we don't care about capitalization for Authorization)
            $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
            //print_r($requestHeaders);
            if (isset($requestHeaders['Authorization'])) {
                $headers = trim($requestHeaders['Authorization']);
            }
        }
        return $headers;
    }

    /**
     * get access token from header
     * */
    private function getBearerToken()
    {
        $headers = $this->getAuthorizationHeader();
        // HEADER: Get the access token from the header
        if (!empty($headers)) {
            if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
                return $matches[1];
            }
        }
        return null;
    }
}
