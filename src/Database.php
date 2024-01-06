<?php

class Database
{
    private string $host;
    private string $name;
    private string $user;
    private string $password;

    public function __construct()
    {
        $this->host = $_ENV['DB_HOSTNAME'];
        $this->name = $_ENV['DB_NAME'];
        $this->user = $_ENV['DB_USER'];
        $this->password = $_ENV['DB_PASSWORD'];
    }

    public function getConnection()
    {
        return new PDO("mysql:host={$this->host};dbname={$this->name}", $this->user, $this->password);
    }
}
