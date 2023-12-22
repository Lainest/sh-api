<?php

class UserGateway
{
    private PDO $db_conn;

    public function __construct(Database $database)
    {
        $this->db_conn = $database->getConnection();
    }

    public function getAll(): array
    {
        $stmt = $this->db_conn->query("SELECT * FROM users");

        $data = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row;
        }

        return $data;
    }
}
