<?php
$host = 'localhost';
$dbName = 'task';
$port = '5432';
$user = 'postgres';
$pass = 'postgres';

try {
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ];

    $db = new PDO('pgsql:host=' . $host . ';port=' . $port . ';dbname=' . $dbName, $user, $pass,$options);
    //echo "Connected successfully!";
}catch(Exception $e) {
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode([
        'error' => 'Database connection failed',
        'code' => $e->getCode(),
        'message' => $e->getMessage()
    ]);
    exit;
}

?>