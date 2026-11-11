<?php

require __DIR__ . "/vendor/autoload.php";
require __DIR__ . "/config/database.php";

use App\Controller\ControllerBoss;
use App\Controller\ControllerTask;
use App\Controller\ControllerWorker;
use App\Model\Boss;
use App\Model\Task;
use App\Model\Worker;

$Task = new Task($db);
$controllerTask = new ControllerTask($Task);

$Worker = new Worker($db);
$controllerWorker = new ControllerWorker($Worker);

$Boss = new Boss($db);
$controllerBoss = new ControllerBoss($Boss);

header('Content-Type: application/json');

function respond(int $status, $body): void {
    http_response_code($status);
    echo json_encode($body);
}

// id de /recurso/{id}, ou null se não foi enviado
function routeId(array $segments): ?string {
    $id = $segments[1] ?? null;
    return ($id === null || $id === '') ? null : $id;
}

$base = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/'); // "" na raiz, ou "/subpasta" se o index.php estiver em uma
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH); // /tasks/1
$path = substr($path, strlen($base)); // /tasks/1
$segments = explode('/', trim($path, '/'));  // ["tasks"] ou ["tasks", "1"]
$resource = $segments[0]; // "tasks"

$url = $_SERVER['REQUEST_METHOD'] . ' ' . $resource;


switch ($url) {
    case 'GET tasks':
        $id = routeId($segments);
        if ($id === null) {
            respond(400, ['error' => 'ID not provided']);
            break;
        }

        $response = $controllerTask->getTask($id);
        respond($response['status'], $response['body']);
        break;
    case 'GET workers':
        $id = routeId($segments);
        if ($id === null) {
            respond(400, ['error' => 'ID not provided']);
            break;
        }

        $response = $controllerWorker->getWorker($id);
        respond($response['status'], $response['body']);
        break;
    case 'GET bosses':
        $id = routeId($segments);
        if ($id === null) {
            respond(400, ['error' => 'ID not provided']);
            break;
        }

        $response = $controllerBoss->getBoss($id);
        respond($response['status'], $response['body']);
        break;
    case 'POST tasks':
        $workerId = $_POST['worker_id'] ?? null;
        $bossId = $_POST['boss_id'] ?? null;
        $title = $_POST['title'] ?? null;

        $worker = $controllerWorker->getWorker($workerId); // body null se o worker_id não foi enviado
        $boss = $controllerBoss->getBoss($bossId); // sempre retornar um array

        if ($boss['status'] !== 200) {
            respond($boss['status'], $boss['body']);
            break;
        } elseif ($worker['status'] !== 200) {
            respond($worker['status'], $worker['body']);
            break;
        }

        $response = $controllerTask->createTask($worker['body'], $boss['body'], $_POST['description'] ?? null, $title);
        respond($response['status'], $response['body']);
        break;
    case 'POST register':
        $type = $segments[1] ?? null; // /register/worker -> "worker"

        if ($type === 'worker') {
            $response = $controllerWorker->createWorker($_POST['name'] ?? null, $_POST['email'] ?? null);
        } elseif ($type === 'boss') {
            $response = $controllerBoss->createBoss($_POST['name'] ?? null, $_POST['email'] ?? null);
        } else {
            respond(404, ['error' => 'Route not found']);
            break;
        }

        respond($response['status'], $response['body']);
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
            header('Allow: ' . $allowed[$resource]);
            respond(405, ['error' => 'Method not allowed']);
            break;
        }

        // rota não existe -> 404 (erro do cliente, por isso não é 500)
        respond(404, ['error' => 'Route not found']);
}
