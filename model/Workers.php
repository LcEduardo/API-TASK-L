<?php

class Worker extends Model {
    
    protected function getTable(): string {
        return 'workers';
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