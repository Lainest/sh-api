<?php

class UserController
{
    public function __construct(private UserGateway $gateway)
    {
    }

    public function getUsers()
    {

        $users = $this->gateway->getAll();

        new Response($users, 200);
    }

    public function getUser(string $id)
    {
        $user = $this->gateway->getById($id);

        if ($user === false) {
            new Response([
                "Error" => "User not found"
            ], 500);
            return;
        }

        new Response($user, 200);
    }

    public function registerUser()
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

    private function validateUserRegistration($data): array | false
    {
        $validation = Validation::getInstance();

        $validation->setData($data);

        $validation->field('user')->type('text')->pattern("/^[a-z0-9_-]{3,15}$/")->required();
        $validation->field('password')->type('text')->pattern("/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$/")->required();
        $validation->field('name')->type('text')->required();
        $validation->field('surname')->type('text')->required();
        $validation->field('province')->type('text')->required()->pattern("/BO|FE/");

        return $validation->hasErrors();
    }

    public function loginUser()
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

            $token = $jwt->encode($token_data);

            header("Authorization:Bearer $token");

            new Response([
                "success" => "Login successful"
            ], 200);
        } else {
            new Response([
                "Login error" => "Password not valid"
            ], 500);
        }
    }

    private function validateUserLogin($data): array | false
    {
        $validation = Validation::getInstance();

        $validation->setData($data);

        $validation->field('user')->type('text')->pattern("/^[a-z0-9_-]{3,15}$/")->required();
        $validation->field('password')->type('text')->pattern("/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$/")->required();

        return $validation->hasErrors();
    }
}
