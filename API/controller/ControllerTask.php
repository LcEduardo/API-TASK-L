<?php

class ControllerTask 
{
    private Task $task;

    public function __construct(Task $task) {
        $this->task = $task;
    }

    public function getTask($id) {
        try {
            $task = $this->task->getTaskById($id);

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
}