<?php

require __DIR__ . "/model/tasks.php";
require __DIR__ . "/config/database.php";

header('Content-Type: application/json');

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        $task_id = new Task($db);
        echo json_encode($task_id->getTaskById($_GET['id']));
        break;
    case 'POST':
        $task_id = new Task($db);
        echo json_encode($task_id->getTaskById($_POST['id']));
        break;
    default:
        http_response_code(500);
        header('Content-Type: application/json');
        echo json_encode([
            'error' => 'Method don\'t found'
        ]);    
}
