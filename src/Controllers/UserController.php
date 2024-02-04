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
                "message" => "User not found"
            ], 404);
        }

        new Response($user, 200);
    }

    public function create(): void
    {
        $data = (array) json_decode(file_get_contents("php://input"), true);

        $this->handleCreationErrors($data);

        $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);

        $user_id = $this->gateway->create($data);
        $user = $this->gateway->getById($user_id);

        $tokens = $this->createTokens($user);

        $this->setRefreshCookie($tokens['refresh']);

        new Response([
            "message" => "User successfully registrated",
            "access_token" => $tokens['access']
        ], 201);
    }

    private function handleCreationErrors(array $data): void
    {
        $validation_errors = $this->validateUserRegistration($data);

        if (is_array($validation_errors)) {
            new Response([
                "message" => "Registration failed",
                "error" => $validation_errors
            ], 422);
        }
    }

    public function login()
    {
        $data = (array) json_decode(file_get_contents("php://input"), true);

        $this->handleLoginErrors($data);

        $user = $this->gateway->getByUser($data['user']);

        if ($user === false) {
            new Response([
                "error" => "User not found"
            ], 404);
        }

        if ($this->authenticate($user['password'], $data['password'])) {

            $tokens = $this->createTokens($user);

            $this->setRefreshCookie($tokens['refresh']);

            new Response([
                "message" => "Login successful",
                "access_token" => $tokens['access']
            ], 200);
        }
    }

    private function handleLoginErrors($data): void
    {
        $validation_errors = $this->validateUserLogin($data);

        if (is_array($validation_errors)) {
            new Response([
                "error" => $validation_errors,
                "message" => "Login failed"
            ], 422);
        }
    }

    private function authenticate(string $stored_password, string $user_password): bool
    {
        if (password_verify($user_password, $stored_password)) {
            return true;
        }

        new Response([
            "error" => "Password not valid",
            "message" => "Login failed"
        ], 500);
    }

    public function refresh()
    {
        if (!isset($_COOKIE['refresh_token'])) {
            new Response([
                "message" => "User not authorized",
                "error" => "Refresh token missing"
            ], 401);
        }

        $jwt = MyJwt::getInstance();

        $decoded_data = $jwt->decode($_COOKIE['refresh_token'], 'refresh');
        $user_data = json_decode(json_encode($decoded_data), true);

        $tokens = $this->createTokens($user_data);

        new Response([
            "message" => "Token refreshed",
            "access_token" => $tokens['access']
        ], 200);
    }

    private function createTokens($user): array
    {
        $jwt = MyJwt::getInstance();

        $token_data = [
            'id' => $user['id'],
            'user' => $user['user'],
            'role' => $user['role']
        ];

        $tokens = array();

        $tokens['access'] = $jwt->encode($token_data);
        $tokens['refresh'] = $jwt->encode($token_data, 'refresh');

        return $tokens;
    }

    private function setRefreshCookie($refresh_token): void
    {
        $max_age = MyJwt::REFRESH_EXPIRATION_TIME;
        header("Set-Cookie: refresh_token=$refresh_token; Max-Age=$max_age; HttpOnly; SameSite=Strict");
    }
}
