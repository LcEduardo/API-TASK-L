<?php

require __DIR__ . "/config/database.php";
require __DIR__ . "/controller/ControllerTask.php";
require __DIR__ . "/model/Tasks.php";

$TaskModel = new Task($db);
$controllerTask = new ControllerTask($TaskModel);

header('Content-Type: application/json');

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        $response = $controllerTask->getTask($_GET['id']);
        echo json_encode($response['task']);
        break;
    case 'POST':
        $response = $controllerTask->getTask($_POST['id']);
        echo json_encode($response['task']);
        break;
    default:
        http_response_code(405); //lado do cliente não do servidor por isso não pode ser 500
        echo json_encode([
            'error' => 'Method don\'t found'
        ]);    
}
