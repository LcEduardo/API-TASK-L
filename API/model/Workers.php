<?php

class Worker {
    private PDO $conn;

    public function __construct(PDO $conn) {
        $this->conn = $conn;
    }

    public function getWorkerById($id) {
        $stmt = $this->conn->prepare("
            SELECT * FROM workers w WHERE w.id = :id;
        ");
        $stmt->execute(['id' => $id]);
        $worker = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($worker === false) {
            throw new Exception("Worker with id $id not found", 404);
        }

        return $worker;

    }
    
}