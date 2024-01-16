<?php

class UserGateway implements Gateway
{
    private PDO $db;

    public function __construct(Database $database)
    {
        $this->db = $database->getConnection();
    }

    public function getAll(): array
    {
        $stmt = $this->db->query("SELECT user,name,surname,province
         FROM users");

        $data = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row;
        }

        return $data;
    }

    public function getById(string $id): array | false
    {
        $sql = "SELECT user,name,surname,province 
         FROM users WHERE id = :id";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue('id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result;
    }

    public function getByUser(string $user): array | false
    {
        $sql = "SELECT * FROM users WHERE BINARY user = :user";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue('user', $user, PDO::PARAM_STR);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result;
    }

    public function create(array $data): string
    {
        $sql = "INSERT INTO users (user, password, name, surname, province, date_created, date_updated, role)
                VALUES (:user, :password, :name, :surname, :province, :date_created, :date_updated, :role)";

        $stmt = $this->db->prepare($sql);

        $stmt->bindValue(":user", $data["user"], PDO::PARAM_STR);
        $stmt->bindValue(":password", $data["password"], PDO::PARAM_STR);
        $stmt->bindValue(":name", $data["name"], PDO::PARAM_STR);
        $stmt->bindValue(":surname", $data["surname"], PDO::PARAM_STR);
        $stmt->bindValue(":province", $data["province"], PDO::PARAM_STR);
        $stmt->bindValue(":date_created", date("Y/m/d"));
        $stmt->bindValue(":date_updated", null);
        $stmt->bindValue(":role", 2);

        $stmt->execute();

        return $this->db->lastInsertId();
    }
}
