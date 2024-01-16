<?php

include __DIR__ . '/UserValidationTrait.php';

class UserController implements Controller
{
    use UserValidationTrait;

    private UserGateway $gateway;

    public function __construct()
    {
        $this->gateway = new UserGateway(new Database());
    }

    public function getAll(): void
    {

        $users = $this->gateway->getAll();

        new Response($users, 200);
    }

    public function getOne(string $id): void
    {
        $user = $this->gateway->getById($id);

        if ($user === false) {
            new Response([
                "Error" => "User not found"
            ], 404);
            return;
        }

        new Response($user, 200);
    }

    public function create(): void
    {
        $body = (array) json_decode(file_get_contents("php://input"), true);
        $validation_errors = $this->validateUserRegistration($body);

        if (is_array($validation_errors)) {
            http_response_code(422);
            echo json_encode(["errors" => $validation_errors]);
            die;
        }

        $body['password'] = password_hash($body['password'], PASSWORD_BCRYPT);

        $id = $this->gateway->create($body);

        new Response([
            $id => "User created"
        ], 201);
    }

    public function login()
    {
        $body = (array) json_decode(file_get_contents("php://input"), true);
        $validation_errors = $this->validateUserLogin($body);

        if (is_array($validation_errors)) {
            http_response_code(422);
            echo json_encode(["errors" => $validation_errors]);
            die;
        }

        $user = $this->gateway->getByUser($body['user']);

        if ($user === false) {
            new Response([
                "Error" => "User not found"
            ], 404);
        }

        if (password_verify($body['password'], $user['password'])) {
            $token_data = [
                'id' => $user['id'],
                'user' => $user['user'],
                'role' => $user['role'],
            ];

            $jwt = MyJwt::getInstance();

            $access_token = $jwt->encode($token_data);

            new Response([
                "access_token" => $access_token
            ], 200);
        } else {
            new Response([
                "Login error" => "Password not valid"
            ], 500);
        }
    }
}
