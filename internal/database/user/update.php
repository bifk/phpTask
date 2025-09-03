<?php


require_once __DIR__ . "/../../../models/User.php";
require_once __DIR__ . "/../Database.php";

function updateUser($userId, $userData) {
    $database = new Database();
    $db = $database->getConnection();

    foreach ($userData as $key => $value) {
        if ($value === "") {
            unset($userData[$key]);
        }
    }

    $allow = ["username", "phone", "email", "password"];

    $newData = [];

    foreach ($userData as $key => $value) {
        if (in_array($key, $allow)) {
            $newData[$key] = $value;
        }
    }

    if (count($newData) == 0) {
        return false;
    }

    if (isset($newData['username'])) {
        $checkStmt = $db->prepare("SELECT id FROM users WHERE username = :username AND id != :id");
        $checkStmt->bindParam(":username", $newData['username']);
        $checkStmt->bindParam(":id", $userId);
        $checkStmt->execute();
        if ($checkStmt->fetch()) {
            throw new Exception("Данное имя пользователя уже зарегистрированно");
        }
    }

    if (isset($newData['phone'])) {
        $checkStmt = $db->prepare("SELECT id FROM users WHERE phone = :phone AND id != :id");
        $checkStmt->bindParam(":phone", $newData['phone']);
        $checkStmt->bindParam(":id", $userId);
        $checkStmt->execute();
        if ($checkStmt->fetch()) {
            throw new Exception("Данный телефон уже зарегистрирован");
        }
    }

    if (isset($newData['email'])) {
        $checkStmt = $db->prepare("SELECT id FROM users WHERE username = :email AND id != :id");
        $checkStmt->bindParam(":email", $newData['email']);
        $checkStmt->bindParam(":id", $userId);
        $checkStmt->execute();
        if ($checkStmt->fetch()) {
            throw new Exception("Данная эл. почта уже зарегистрированна");
        }
    }


    $setParts = [];
    foreach ($newData as $key => $value) {
        $setParts[] = "$key = :$key";
    }

    $sql = "UPDATE users SET " . implode(", ", $setParts) . " WHERE id = :id
        RETURNING id, username, phone, email
";

    $params = [':id' => $userId];
    foreach ($newData as $key => $value) {
        $params[':' . $key] = $value;
    }

    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $newUser = $stmt->fetch(PDO::FETCH_ASSOC);

    $stmt->closeCursor();

    return new User($newUser['id'], $newUser['username'], $newUser['phone'], $newUser['email']);

}

