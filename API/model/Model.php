<?php

abstract class Model 
{
    protected PDO $conn;

    public function __construct(PDO $conn) {
        $this->conn = $conn;
    }

    abstract protected function getTable(): string;

    public function getById($id) {
        $stmt = $this->conn->prepare(
            "SELECT * FROM " . $this->getTable() .  " WHERE id = :id;"
        );

        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row === false) {
            // static::class = nome da classe filha (Worker, Boss...)
            throw new Exception(static::class . " with id $id not found", 404);
        }

        return $row;
    }
}