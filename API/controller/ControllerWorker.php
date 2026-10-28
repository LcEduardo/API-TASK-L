<?php

class ControllerWorker 
{
    private Worker $worker;

    public function __construct(Worker  $worker) {
        $this->worker = $worker;
    }

    public function getWorker($id) {
        try {
            $worker = $this->worker->getWorkerById($id);

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
            "body" => $worker,
        ]; 
    }

}