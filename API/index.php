<?php

require __DIR__ . "/config/database.php";
require __DIR__ . "/controller/ControllerTask.php";
require __DIR__ . "/model/Tasks.php";

$TaskModel = new Task($db);
$controllerTask = new ControllerTask($TaskModel);

header('Content-Type: application/json');
http_response_code(400);

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        $id = $_GET['id'] ?? null;
        $response = [];

        if ($id == null){
            http_response_code();
            echo json_encode($response = [
                'message' => 'ID not provided',
            ]);
            break;
        }

        $response = $controllerTask->getTask($id);
        http_response_code($response['status']);
        echo json_encode($response['body']);
        break;
    case 'POST':
        $id = $_POST['id'] ?? null;
        $response = [];

        if ($id == null){
            http_response_code();
            echo json_encode($response = [
                'message' => 'ID not provided',
            ]);
            break;
        }       

        $response = $controllerTask->getTask($_POST['id']);
        http_response_code($response['status']);
        echo json_encode($response['body']);
        break;
    default:
        http_response_code(405); //lado do cliente não do servidor por isso não pode ser 500
        echo json_encode([
            'error' => 'Method don\'t found'
        ]);    
}
