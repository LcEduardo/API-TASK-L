<?php

header('Content-Type: application/json');

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        echo json_encode(["message" => "Hello, World!"]);
        break;
    case 'POST':
        echo json_encode("Hello, " . $_POST['nome'] . "!");
        break;
    default:
        echo "Deu ruim!";    
}
