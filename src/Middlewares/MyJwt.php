<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\SignatureInvalidException;

class MyJwt extends Singleton
{

    private string $access_secret_key;

    protected function __construct()
    {
        $this->access_secret_key = $_ENV['ACCESS_SECRET_KEY'];
    }

    public function encode(array $data)
    {
        $token = [
            'iss' => 'http://localhost/api-sh/',
            'iat' => time(),
            'exp' => time() + 3600 * 7, // /2
            'data' => $data
        ];

        return JWT::encode($token, $this->access_secret_key, 'HS256');
    }

    public function decode($token)
    {
        try {
            $decode = JWT::decode($token, new Key($this->access_secret_key, 'HS256'));
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
