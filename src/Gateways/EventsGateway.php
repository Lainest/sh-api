<?php

class EventsGateway implements Gateway
{

    private PDO $db;

    public function __construct(Database $database)
    {
        $this->db = $database->getConnection();
    }

    public function getAll(): array
    {
        $query = "SELECT * FROM events JOIN stadiums ON stadiums.id = events.stadium_id";
        $stmt = $this->db->query($query);

        $data = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row;
        }

        return $data;
    }

    public function getById(string $id): array|false
    {
        $query = "SELECT * FROM events JOIN stadiums ON stadiums.id = events.stadium_id
        WHERE events.id=:id";

        $stmt = $this->db->prepare($query);

        $stmt->bindParam(":id", $id);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result;
    }

    public function create(array $data): string
    {

        if ($this->stadiumExists($data["stadium"]) === false) {
            new Response([
                "message" => "stadium not found"
            ], 404);
            die;
        }

        $sql = "INSERT INTO events (name,stadium_id,date,expiration)
                VALUES (:name,:stadium_id,:date,:expiration)";

        $stmt = $this->db->prepare($sql);

        $stmt->bindValue(":name", $data["name"]);
        $stmt->bindValue(":stadium_id", $data["stadium"]);
        $stmt->bindValue(":date", $data["date"]);
        $stmt->bindValue(":expiration", $data["expiration"]);

        $stmt->execute();

        return $this->db->lastInsertId();
    }

    private function stadiumExists(string $id): array | false
    {
        // replace with stadium gateway->getById
        $query = "SELECT * FROM stadiums WHERE id=:id";

        $stmt = $this->db->prepare($query);

        $stmt->bindParam(":id", $id);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result;
    }
}
