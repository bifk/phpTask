<?php


require_once __DIR__ . "/../../../models/User.php";
require_once __DIR__ . "/../Database.php";



function createUser($userData) {
    $database = new Database();
    $db = $database->getConnection();

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

    return false;
}
