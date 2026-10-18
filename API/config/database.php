<?php
$host = 'localhost';
$dbName = 'task';
$port = '5432';
$user = 'postgres';
$pass = 'postgres';

try {
    $db = new PDO('pgsql:host=' . $host . ';port=' . $port . ';dbname=' . $dbName, $user, $pass);
    echo "Connected successfully!";
}catch(Exception $e) {
    echo 'Code: ' . $e->getCode() . "\nFile: " . $e->getFile() . "\nMessage: " . $e->getMessage();
}

?>