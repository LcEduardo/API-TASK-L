<?php

class Task {
    private PDO $conn;

    public function __construct(PDO $conn) {
        $this->conn = $conn;
    }

    public function getTaskById($id) {
        $stmt = $this->conn->prepare("
            SELECT * FROM tasks WHERE id = :id;
        ");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}