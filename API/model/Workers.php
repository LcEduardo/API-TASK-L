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

    public function registerWorker($name, $email): int {

        $stmt = $this->conn->prepare(
                "INSERT INTO workers (name, email)
                VALUES(:name, :email)
                RETURNING id"
            );

        $registerWorker = $stmt->execute([
            'name'  => $name,
            'email' => $email,
        ]);

        if ($registerWorker === false) {
            throw new Exception("The worker was not created", 400);
        }

        return (int) $stmt->fetchColumn();
    }
    
}