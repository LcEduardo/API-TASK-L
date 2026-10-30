<?php

require __DIR__ . "/config/database.php";
require __DIR__ . "/controller/ControllerTask.php";
require __DIR__ . "/controller/ControllerWorker.php";
require __DIR__ . "/controller/ControllerBoss.php";
require __DIR__ . "/model/Model.php";
require __DIR__ . "/model/Tasks.php";
require __DIR__ . "/model/Workers.php";
require __DIR__ . "/model/Bosses.php";

$Task = new Task($db);
$controllerTask = new ControllerTask($Task);

$Worker = new Worker($db);
$controllerWorker = new ControllerWorker($Worker);

$Boss = new Boss($db);
$controllerBoss = new ControllerBoss($Boss);

header('Content-Type: application/json');

$base = dirname($_SERVER['SCRIPT_NAME']); // "/API" (pasta onde está o index.php)
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH); // /API/tasks/1
$path = substr($path, strlen($base)); // /tasks/1
$segments = explode('/', trim($path, '/'));  // ["tasks"] ou ["tasks", "1"]
$resource = $segments[0]; // "tasks"

$url = $_SERVER['REQUEST_METHOD'] . ' ' . $resource;


switch ($url) {
    case 'GET tasks':
        $id = $segments[1] ?? null;

        if ($id == null){
            http_response_code(400);
            echo json_encode($response = [
                'error' => 'ID not provided',
            ]);
            break;
        }

        $response = $controllerTask->getTask($id);
        http_response_code($response['status']);
        echo json_encode($response['body']);
        break;
    case 'GET workers':
        $id = $segments[1] ?? null;

        if ($id == null) {
            http_response_code(400);
            echo json_encode($response = [
                'error' => 'ID not provided',
            ]);
            break;
        }

        $response = $controllerWorker->getWorker($id);
        http_response_code($response['status']);
        echo json_encode($response['body']);
        break;
    case 'GET bosses':
        $id = $segments[1] ?? null;

        if ($id == null) {
            http_response_code(400);
            echo json_encode($response = [
                'error' => 'ID not provided',
            ]);
            break;
        }

        $response = $controllerBoss->getBoss($id);
        http_response_code($response['status']);
        echo json_encode($response['body']);
        break;
    case 'POST tasks':
        $workerId = $_POST['worker_id'] ?? null;
        $bossId = $_POST['boss_id'] ?? null;
        $title = $_POST['title'] ?? null;

        // worker é opcional: só valida se foi enviado
        $worker = null;
        if ($workerId !== null && $workerId !== '') {
            $worker = $controllerWorker->getWorker($workerId);
        }

        $boss = $controllerBoss->getBoss($bossId); // sempre retornar um array

        if ($boss['status'] !== 200) {
            http_response_code($boss['status']);
            echo json_encode($boss['body']);
            break;
        } elseif ($worker !== null && $worker['status'] !== 200) {
            http_response_code($worker['status']);
            echo json_encode($worker['body']);
            break;
        }

        $response = $controllerTask->createTask($worker['body'] ?? null, $boss['body'], $_POST['description'] ?? null, $title);
        http_response_code($response['status']);
        echo json_encode($response['body']);
        exit;
    case 'POST register':
        $type = $segments[1] ?? null; // /register/worker -> "worker"

        if ($type !== 'worker') {
            http_response_code(404);
            echo json_encode([
                'error' => 'Route not found'
            ]);
            break;
        }

        $response = $controllerWorker->createWorker($_POST['name'] ?? null, $_POST['email'] ?? null);
        http_response_code($response['status']);
        echo json_encode($response['body']);
        break;
    default:
        // rota existe, mas o método não é suportado -> 405
        $allowed = [
            'tasks'    => 'GET, POST',
            'workers'  => 'GET',
            'bosses'   => 'GET',
            'register' => 'POST',
        ];

        if (isset($allowed[$resource])) {
            http_response_code(405);
            header('Allow: ' . $allowed[$resource]);
            echo json_encode([
                'error' => 'Method not allowed'
            ]);
            break;
        }

        // rota não existe -> 404 (erro do cliente, por isso não é 500)
        http_response_code(404);
        echo json_encode([
            'error' => 'Route not found'
        ]);
}
