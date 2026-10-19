<?php

require __DIR__ . "/model/tasks.php";

header('Content-Type: application/json');

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        $task_id = getTaskById($_GET['nome']);
        echo json_encode($task_id);
        break;
    case 'POST':
        echo json_encode("Hello, " . $_POST['nome'] . "!");
        break;
    default:
        echo "Deu ruim!";    
}
