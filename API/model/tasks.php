<?php

require __DIR__ . "/../config/database.php";

function getTaskById($id, $db){
        $stmt = $db->prepare("SELECT * FROM tasks WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } 