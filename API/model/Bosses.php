<?php

class Boss {
    private PDO $conn;

    public function __construct(PDO $conn) {
        $this->conn = $conn;
    }

    public function getBossById($id) {
        $stmt = $this->conn->prepare("
            SELECT * FROM bosses b WHERE b.id = :id;
        ");
        $stmt->execute(['id' => $id]);
        $boss = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($boss === false) {
            throw new Exception("Boss with id $id not found", 404);
        }

        return $boss;

    }

}
