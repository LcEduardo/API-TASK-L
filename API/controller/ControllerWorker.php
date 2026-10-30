<?php

class ControllerWorker 
{
    private Worker $worker;

    public function __construct(Worker  $worker) {
        $this->worker = $worker;
    }

    public function getWorker($id) {
        try {

            $id = filter_var($id, FILTER_VALIDATE_INT);

            if ($id === false) {
                 throw new Exception("The worker_id isn't interger", 400);
            }

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

    public function createWorker($name, $email) {
        try {

            $name = trim($name ?? '');
            $email = trim($email ?? '');

            if ($name === '') {
                throw new Exception("The name is required", 400);
            }

            if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
                throw new Exception("The email isn't valid", 400);
            }

            $workerId = $this->worker->registerWorker($name, $email);

            $worker = $this->worker->getWorkerById($workerId);

        }catch(PDOException $e) {
            // 23505 = unique_violation (email já cadastrado)
            if ($e->getCode() === '23505') {
                return [
                    "status" => 409,
                    "body" => ['error' => 'Email already registered'],
                ];
            }

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
            "body" => $worker,
        ];
    }

}