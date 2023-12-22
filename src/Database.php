<?php

class Database
{
    public function __construct(
        private string $host,
        private string $name,
        private string $user,
        private string $password
    ) {
    }

    public function getConnection()
    {
        return new PDO("mysql:host={$this->host};dbname={$this->name}", $this->user, $this->password);
    }
}
