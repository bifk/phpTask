<?php


require_once __DIR__ . "/../../../models/User.php";
require_once __DIR__ . "/../Database.php";


// Регистрация пользователя
function createUser($userData) {
    $database = new Database();
    $db = $database->getConnection();


    // Проверка на уникальность параметров
    if (isset($userData['username'])) {
        $checkStmt = $db->prepare("SELECT id FROM users WHERE username = :username");
        $checkStmt->bindParam(":username", $userData['username']);
        $checkStmt->execute();
        if ($checkStmt->fetch()) {
            throw new Exception("Данное имя пользователя уже зарегистрированно");
        }
    }

    if (isset($userData['phone'])) {
        $checkStmt = $db->prepare("SELECT id FROM users WHERE phone = :phone");
        $checkStmt->bindParam(":phone", $userData['phone']);
        $checkStmt->execute();
        if ($checkStmt->fetch()) {
            throw new Exception("Данный телефон уже зарегистрирован");
        }
    }

    if (isset($userData['email'])) {
        $checkStmt = $db->prepare("SELECT id FROM users WHERE username = :email");
        $checkStmt->bindParam(":email", $userData['email']);
        $checkStmt->execute();
        if ($checkStmt->fetch()) {
            throw new Exception("Данная эл. почта уже зарегистрированна");
        }
    }

    $sql = "
      INSERT INTO users (username, phone, email, password) 
      VALUES (:username, :phone, :email, :password)
      RETURNING id, username, phone, email
    ";


    $stmt = $db->prepare($sql);
    $stmt->bindParam(":username", $userData["username"]);
    $stmt->bindParam(":phone", $userData["phone"]);
    $stmt->bindParam(":email", $userData["email"]);
    $stmt->bindParam(":password", $userData["password"]);


    if ($stmt->execute()) {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return new User($user["id"], $user["username"], $user["phone"], $user["email"]);
    }
    $stmt->closeCursor();

    return false;
}
