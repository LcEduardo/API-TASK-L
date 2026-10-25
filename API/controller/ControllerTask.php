<?php

class ControllerTask 
{
    private Task $task;

    public function __construct(Task $task) {
        $this->task = $task;
    }

    public function getTask($id) {
        $response = [];
        $task = $this->task->getTaskById($id);

        if ($task == false) {
            return $response = [
                "message" => "The id is valid, but no task has it",
                "status" => http_response_code(404),
            ];
        }

        return $response = [
            "task" => $task,
            "status" => http_response_code(200),
        ];
    }
}