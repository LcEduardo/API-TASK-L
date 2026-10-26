<?php

class Task {
    private PDO $conn;

    public function __construct(PDO $conn) {
        $this->conn = $conn;
    }

    public function getTaskById($id) {
        try {
            $stmt = $this->conn->prepare("
                SELECT * FROM tasks WHERE id = :id;
            ");

            $stmt->execute(['id' => $id]);
            
        }catch(PDOException $e) {
            return $response
        }

        return $stmt->fetch(PDO::FETCH_ASSOC);
        
    }
}