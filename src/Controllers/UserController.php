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
        $validation_errors = $this->validateUser($body);

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

    private function validateUser($data): array | false
    {
        $validation = Validation::getInstance();

        $validation->setData($data);

        $validation->field('user')->type('text')->pattern("/^[a-z0-9_-]{3,15}$/")->required();
        $validation->field('password')->type('text')->pattern("/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$/")->required();
        $validation->field('name')->type('text')->required();
        $validation->field('surname')->type('text')->required();
        $validation->field('province')->type('text')->required();

        return $validation->hasErrors();
    }
}

// (user, password, name, surname, province, date_created, date_updated, role)
// test cli: 