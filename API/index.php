<?php

require __DIR__ . "/model/tasks.php";
require __DIR__ . "/config/database.php";

header('Content-Type: application/json');

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        $task_id = getTaskById($_GET['id'], $db);
        echo json_encode($task_id);
        break;
    case 'POST':
        $task_id = getTaskById($_POST['id'], $db);
        echo json_encode($task_id);
        break;
    default:
        echo "Deu ruim!";    
}

// function getTaskById($id, $db){
//     $stmt = $db->prepare("SELECT * FROM tasks WHERE id = :id");
//     $stmt->execute(['id' => $id]);
//     return $stmt->fetch(PDO::FETCH_ASSOC);
// } 