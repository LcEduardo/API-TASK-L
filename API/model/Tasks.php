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
        $task = $stmt->fetch(PDO::FETCH_ASSOC);
            
        if ($task === false) {
            throw new Exception("Task with id $id not found", 404);
        }

        return $task;
        
    }
}