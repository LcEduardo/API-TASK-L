<?php

namespace App\Model;

use Exception;
use PDO;
use ReflectionClass;

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
            // getShortName() = nome da classe filha sem o namespace (Worker, Boss...)
            throw new Exception((new ReflectionClass($this))->getShortName() . " with id $id not found", 404);
        }

        return $row;
    }

    // $data = ['coluna' => valor]. As chaves vêm sempre do código, nunca do usuário,
    // por isso podem entrar direto no SQL; os valores vão pelo prepared statement.
    public function create(array $data): int {
        $columns = array_keys($data);

        $stmt = $this->conn->prepare(
            "INSERT INTO " . $this->getTable() . " (" . implode(', ', $columns) . ")
            VALUES (:" . implode(', :', $columns) . ")
            RETURNING id"
        );

        $created = $stmt->execute($data);

        if ($created === false) {
            throw new Exception((new ReflectionClass($this))->getShortName() . " was not created", 400);
        }

        return (int) $stmt->fetchColumn();
    }

    // Mesma regra do create(): as chaves de $data vêm do código, os valores vão pelo prepared statement.
    public function update(int $id, array $data): void {
        $sets = array_map(fn ($column) => "$column = :$column", array_keys($data));

        $stmt = $this->conn->prepare(
            "UPDATE " . $this->getTable() . " SET " . implode(', ', $sets) . " WHERE id = :id"
        );

        $stmt->execute($data + ['id' => $id]);

        // nenhuma linha com esse id -> mesmo 404 do getById()
        if ($stmt->rowCount() === 0) {
            throw new Exception((new ReflectionClass($this))->getShortName() . " with id $id not found", 404);
        }
    }
}