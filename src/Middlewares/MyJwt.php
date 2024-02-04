<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\SignatureInvalidException;

class MyJwt extends Singleton
{
    const ACCESS_EXPIRATION_TIME = 900;
    const REFRESH_EXPIRATION_TIME = 3600;

    private string $access_secret_key;
    private string $refresh_secret_key;

    protected function __construct()
    {
        $this->access_secret_key = $_ENV['ACCESS_SECRET_KEY'];
        $this->refresh_secret_key = $_ENV['REFRESH_SECRET_KEY'];
    }

    public function encode(array $data, string $token_type = 'access')
    {

        $expiration_time = $token_type === 'access' ? self::ACCESS_EXPIRATION_TIME : self::REFRESH_EXPIRATION_TIME;

        $token = [
            'iss' => 'http://localhost/api-sh/',
            'iat' => time(),
            'exp' => time() + $expiration_time,
            'data' => $data
        ];

        return JWT::encode($token, $token_type === 'access' ? $this->access_secret_key : $this->refresh_secret_key, 'HS256');
    }

    public function decode($token, string $token_type = 'access')
    {
        try {
            $secret_key = $token_type === 'access' ? $this->access_secret_key : $this->refresh_secret_key;
            $decode = JWT::decode($token, new Key($secret_key, 'HS256'));
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
