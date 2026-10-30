<?php

class ControllerTask 
{
    private Task $task;

    public function __construct(Task $task) {
        $this->task = $task;
    }

    public function getTask($id) {
        try {
            $task = $this->task->getById($id);

        }catch(PDOException $e) {
            return [
                "status" => 500,
                "body" => ['error' => 'Database error'],
            ];
        }catch(Exception $e) {
            return [
                "status" => $e->getCode(),
                "body" => ['error' => $e->getMessage()],
            ];
        }

        return [
            "status" => 200,
            "body" => $task,
        ];
    }

    public function createTask($worker, $boss, $description, $title) {
        try {

            $taskId = $this->task->createTask($worker['id'] ?? null, $boss['id'], $description, $title);

            $task = $this->task->getById($taskId);

        }catch(PDOException $e) {
            return [
                "status" => 500,
                "body" => ['error' => 'Database error'],
            ];
        }catch(Exception $e) {
            return [
                "status" => $e->getCode(),
                "body" => ['error' => $e->getMessage()],
            ];
        }

        return [
            "status" => 201,
            "body" => $task,
        ];
    }
}