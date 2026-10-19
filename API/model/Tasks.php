<?php

require __DIR__ . "/../config/database.php";

echo "Db: " . $dbName;

class Tasks {

    private $db;

    public function __construct(PDO $databaseConnection) {
        $this->db = $databaseConnection;
    }

    public function getTaskById($id){
        $stmt = $this->db->prepare("SELECT * FROM tasks WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } 
}