<?php

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        echo "Hello, World!";
        break;
    case 'POST':
        echo "Hello, " . $_POST['nome'] . "!\n";
        break;
    default:
        echo "Deu ruim!";    
}
