<?php


require_once __DIR__ . "/../../../models/User.php";
require_once __DIR__ . "/../Database.php";

$database = new Database();
$db = $database->getConnection();

function findByPhone($phone) {
    global $db;

    $sql = "SELECT (id, username, phone, email) FROM users WHERE phone = :phone";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(":phone", $phone);
    $stmt->execute();

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        return new User($user["id"], $user["username"], $user["phone"], $user["email"]);
    }
    return false;
}

function findByEmail($email) {
    global $db;

    $sql = "SELECT (id, username, phone, email) FROM users WHERE email = :email";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(":email", $email);
    $stmt->execute();

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        return new User($user["id"], $user["username"], $user["phone"], $user["email"]);
    }
    return false;
}

function findById($id) {
    global $db;

    $sql = "SELECT (id, username, phone, email) FROM users WHERE id = :id";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(":id", $id);
    $stmt->execute();

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        return new User($user["id"], $user["username"], $user["phone"], $user["email"]);
    }
    return false;
}