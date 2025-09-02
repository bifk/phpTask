<?php


require_once __DIR__ . "/../../../models/User.php";
require_once __DIR__ . "/../Database.php";

function updateUser($userId, $userData) {
    $database = new Database();
    $db = $database->getConnection();

    $allow = ["username", "phone", "email", "password"];

    $newData = [":id" => $userId];

    foreach ($userData as $key => $value) {
        if (in_array($key, $allow)) {
            $newData[$key] = $value;
        }
    }

    $sql = "UPDATE users SET " . implode(", ", $newData) . " WHERE id = :id";
    $stmt = $db->prepare($sql);

    return $stmt->execute($newData);

}

