<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\SignatureInvalidException;

class MyJwt extends Singleton
{

    private string $secret_key;

    protected function __construct()
    {
        $this->secret_key = $_ENV['SECRET_KEY'];
    }

    public function encode($data)
    {
        $token = [
            'iss' => 'http://localhost/api-sh/',
            'iat' => time(),
            'exp' => time() + 3600,
            'data' => $data
        ];

        return JWT::encode($token, $this->secret_key, 'HS256');
    }

    public function decode($token)
    {
        try {
            $decode = JWT::decode($token, new Key($this->secret_key, 'HS256'));
            return $decode->data;
        } catch (ExpiredException | SignatureInvalidException $e) {
            new Response(
                [
                    "error" => $e->getMessage()
                ],
                401
            );
            die;
        } catch (UnexpectedValueException | Exception $e) {
            new Response(
                [
                    "error" => $e->getMessage()
                ],
                400
            );
            die;
        }
    }
}
